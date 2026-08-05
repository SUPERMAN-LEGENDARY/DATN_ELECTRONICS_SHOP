<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Logic gọi Gemini REST API + xoay vòng nhiều API key + xử lý rate-limit, dùng CHUNG giữa
 * GeminiChatService và AiSearchParser. Trước đây 2 class có 2 bản callGemini()/stripJsonFence()
 * gần như giống hệt nhau — dễ lệch logic khi chỉ sửa 1 bên. Gộp về đây để chỉ sửa 1 chỗ.
 *
 * Hỗ trợ NHIỀU key (config('services.gemini.keys')) để rải tải qua nhiều quota khác nhau,
 * đỡ bị 429 khi 1 key bị giới hạn. Mỗi key có rate-limit riêng (không dùng chung 1 cờ như
 * trước) — key A đang bị giới hạn không có nghĩa key B cũng vậy.
 */
trait InteractsWithGemini
{
    /** @var string[] Danh sách API key khả dụng, đọc từ config('services.gemini.keys'). */
    protected array $apiKeys;

    protected string $model;
    protected string $endpoint;

    /** Prefix cache key rate-limit — key thật được nối thêm hash để tách riêng theo từng API key. */
    protected const RATE_LIMIT_CACHE_PREFIX = 'gemini_api_rate_limited_until:';

    /** Cache lưu vị trí xoay vòng, để lần gọi kế tiếp không luôn ưu tiên key đầu tiên. */
    protected const ROTATION_INDEX_CACHE_KEY = 'gemini_api_key_rotation_index';

    protected function initGeminiClient(): void
    {
        $this->apiKeys  = array_values(array_filter((array) config('services.gemini.keys', [])));
        $this->model    = config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Gọi Gemini REST API. Tự động thử lần lượt các key chưa bị rate-limit (bắt đầu từ vị trí
     * xoay vòng để rải đều tải), nếu 1 key dính 429 thì chuyển ngay sang key kế tiếp trong CÙNG
     * request thay vì bắt khách chờ — chỉ khi TẤT CẢ key đều đang bị giới hạn mới trả lời chờ.
     *
     * @param bool $jsonMode true = ép trả JSON thuần (trích ý định/bộ lọc); false = câu trả lời
     *   tự nhiên. Khi không truyền, temperature/maxOutputTokens tự chọn theo $jsonMode giống
     *   hành vi gốc (0.1/512 cho JSON, 0.6/2048 cho câu trả lời tự nhiên).
     * @param string $rateLimitedText Câu trả lời hiện cho khách khi TẤT CẢ key đều đang bị
     *   rate-limit (không áp dụng khi $jsonMode=true, lúc đó luôn trả '{}'). Dùng ":seconds"
     *   làm placeholder số giây chờ.
     * @param string $genericErrorText Câu trả lời khi Gemini lỗi khác 429 (không áp dụng khi jsonMode).
     */
    protected function callGemini(
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode = false,
        ?float $temperature = null,
        ?int $maxOutputTokens = null,
        string $rateLimitedText = 'Xin lỗi, hệ thống đang xử lý quá nhiều yêu cầu cùng lúc. Bạn vui lòng đợi khoảng :seconds giây rồi nhắn lại giúp em nhé.',
        string $genericErrorText = 'Xin lỗi, hệ thống tư vấn đang gặp sự cố, bạn vui lòng thử lại sau ít phút.'
    ): string {
        if (empty($this->apiKeys)) {
            Log::error(static::class . ': chưa cấu hình Gemini API key nào (config services.gemini.keys rỗng).');
            return $jsonMode ? '{}' : $genericErrorText;
        }

        $temperature ??= $jsonMode ? 0.1 : 0.6;
        // JSON mode (trích ý định) cần ít token hơn nhiều so với câu trả lời tự nhiên (đặc biệt
        // khi context có bảng so sánh/đơn hàng/tin tức dài) — tách riêng để câu trả lời không bị
        // Gemini cắt cụt giữa chừng (finishReason=MAX_TOKENS) khi context lớn.
        $maxOutputTokens ??= $jsonMode ? 512 : 2048;

        $body = [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
            'generationConfig'   => [
                'temperature'     => $temperature,
                'maxOutputTokens' => $maxOutputTokens,
            ],
        ];

        if ($jsonMode) {
            $body['generationConfig']['responseMimeType'] = 'application/json';
        }

        $order = $this->rotatedKeyOrder();
        $longestWaitSeconds = null;

        foreach ($order as $apiKey) {
            $rateLimitCacheKey = $this->rateLimitCacheKeyFor($apiKey);
            $limitedUntil = Cache::get($rateLimitCacheKey);

            if ($limitedUntil && now()->lt($limitedUntil)) {
                // Key này đang bị 429 từ trước -> bỏ qua ngay, không tốn round-trip, thử key kế tiếp.
                $secondsLeft = now()->diffInSeconds($limitedUntil);
                $longestWaitSeconds = max($longestWaitSeconds ?? 0, $secondsLeft);
                continue;
            }

            $attempt = $this->attemptGeminiRequest($apiKey, $body);

            if ($attempt['ok']) {
                return $attempt['text'];
            }

            if ($attempt['rate_limited']) {
                Cache::put($rateLimitCacheKey, now()->addSeconds($attempt['wait_seconds']), $attempt['wait_seconds']);
                $longestWaitSeconds = max($longestWaitSeconds ?? 0, $attempt['wait_seconds']);
                continue; // key này hết quota -> thử key kế tiếp trong danh sách
            }

            // Lỗi khác 429 (network, 5xx, JSON hỏng...) — không liên quan tới quota của riêng key
            // này nên thử key khác cũng vô ích, trả lỗi luôn thay vì tốn thêm round-trip.
            return $jsonMode ? '{}' : $genericErrorText;
        }

        // Tất cả key đều đang bị giới hạn.
        $waitSeconds = $longestWaitSeconds ?? 60;
        return $jsonMode ? '{}' : str_replace(':seconds', (string) $waitSeconds, $rateLimitedText);
    }

    /**
     * Thứ tự thử key trong lần gọi này — xoay vòng bắt đầu từ vị trí lưu trong cache, để tải
     * được rải đều qua các key thay vì luôn ưu tiên key đầu tiên trong danh sách.
     *
     * @return string[]
     */
    protected function rotatedKeyOrder(): array
    {
        $count = count($this->apiKeys);
        if ($count <= 1) {
            return $this->apiKeys;
        }

        $startIndex = ((int) Cache::get(self::ROTATION_INDEX_CACHE_KEY, 0)) % $count;
        Cache::put(self::ROTATION_INDEX_CACHE_KEY, ($startIndex + 1) % $count, now()->addDay());

        $order = [];
        for ($i = 0; $i < $count; $i++) {
            $order[] = $this->apiKeys[($startIndex + $i) % $count];
        }

        return $order;
    }

    /** Cache key rate-limit RIÊNG cho từng API key (theo hash, không log lộ key thật). */
    protected function rateLimitCacheKeyFor(string $apiKey): string
    {
        return self::RATE_LIMIT_CACHE_PREFIX . substr(md5($apiKey), 0, 12);
    }

    /**
     * Gọi thật sự 1 request Gemini với 1 API key cụ thể.
     *
     * @return array{ok: bool, text: string, rate_limited: bool, wait_seconds: int}
     */
    protected function attemptGeminiRequest(string $apiKey, array $body): array
    {
        $jsonMode = isset($body['generationConfig']['responseMimeType']);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint . '?key=' . $apiKey, $body);

            if ($response->failed()) {
                // status 429 = dính rate limit/quota (RPM hoặc TPM) của CHÍNH key này.
                Log::error(static::class . ' Gemini API error', [
                    'status'        => $response->status(),
                    'body'          => $response->body(),
                    'api_key_hash'  => substr(md5($apiKey), 0, 12),
                ]);

                if ($response->status() === 429) {
                    // Google trả kèm số giây cần chờ trong message lỗi (vd "Please retry in 58.3s").
                    preg_match('/retry in ([\d.]+)s/i', $response->body(), $m);
                    $waitSeconds = isset($m[1]) ? (int) ceil((float) $m[1]) : 60;

                    return ['ok' => false, 'text' => '', 'rate_limited' => true, 'wait_seconds' => $waitSeconds];
                }

                return ['ok' => false, 'text' => '', 'rate_limited' => false, 'wait_seconds' => 0];
            }

            $json = $response->json();
            $finishReason = data_get($json, 'candidates.0.finishReason');

            // Ghi log riêng khi bị cắt do vượt maxOutputTokens, để phân biệt rõ với lỗi quota ở trên
            // khi debug — request vẫn 200 OK nên không rơi vào nhánh failed() phía trên.
            if ($finishReason === 'MAX_TOKENS') {
                Log::warning(static::class . ' Gemini response bị cắt do vượt maxOutputTokens', [
                    'json_mode'    => $jsonMode,
                    'prompt_chars' => mb_strlen(data_get($body, 'system_instruction.parts.0.text', '') . data_get($body, 'contents.0.parts.0.text', '')),
                ]);
            }

            $text = data_get($json, 'candidates.0.content.parts.0.text', $jsonMode ? '{}' : '');

            return ['ok' => true, 'text' => $text, 'rate_limited' => false, 'wait_seconds' => 0];
        } catch (\Throwable $e) {
            Log::error(static::class . ' Gemini call failed: ' . $e->getMessage());
            return ['ok' => false, 'text' => '', 'rate_limited' => false, 'wait_seconds' => 0];
        }
    }

    protected function stripJsonFence(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*|^```\s*|```$/m', '', $text);
        return trim($text);
    }
}