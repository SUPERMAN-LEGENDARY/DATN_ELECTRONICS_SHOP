<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Logic gọi AI (Gemini là provider chính, Groq là provider fallback khi TẤT CẢ key Gemini
 * đều bị rate-limit) + xoay vòng nhiều API key mỗi provider + xử lý rate-limit, dùng CHUNG
 * giữa GeminiChatService và AiSearchParser.
 *
 * Hỗ trợ NHIỀU key mỗi provider (config('services.gemini.keys') / config('services.groq.keys'))
 * để rải tải qua nhiều quota khác nhau, đỡ bị 429 khi 1 key bị giới hạn. Mỗi key có rate-limit
 * riêng (không dùng chung 1 cờ) — key A đang bị giới hạn không có nghĩa key B cũng vậy.
 *
 * LƯU Ý QUAN TRỌNG: quota free tier của Gemini tính theo GOOGLE CLOUD PROJECT, không phải theo
 * API key. Nếu nhiều key Gemini cùng thuộc 1 project, xoay vòng giữa chúng KHÔNG giúp tăng quota
 * thật sự — chỉ khi các key thuộc các project khác nhau thì mới thực sự tách quota.
 */
trait InteractsWithGemini
{
    /** @var string[] Danh sách API key Gemini khả dụng, đọc từ config('services.gemini.keys'). */
    protected array $apiKeys;

    protected string $model;
    protected string $endpoint;

    /** @var string[] Danh sách API key Groq (fallback), đọc từ config('services.groq.keys'). */
    protected array $groqApiKeys = [];

    protected string $groqModel;
    protected string $groqEndpoint;

    /** Prefix cache key rate-limit — key thật được nối thêm hash để tách riêng theo từng API key + provider. */
    protected const RATE_LIMIT_CACHE_PREFIX = 'ai_api_rate_limited_until:';

    /** Cache lưu vị trí xoay vòng, để lần gọi kế tiếp không luôn ưu tiên key đầu tiên (riêng theo provider). */
    protected const ROTATION_INDEX_CACHE_KEY = 'ai_api_key_rotation_index:';

    /**
     * Nếu tất cả key Gemini đều 429 nhưng key sắp mở nhất chỉ cần chờ <= ngưỡng này (giây),
     * callGemini sẽ tự sleep rồi retry thay vì chuyển sang Groq / báo lỗi với khách.
     * Thường gặp khi bị giới hạn RPM (requests-per-minute), retry delay chỉ 5-15 giây.
     */
    protected const MAX_AUTO_WAIT_SECONDS = 15;

    protected function initGeminiClient(): void
    {
        $this->apiKeys  = array_values(array_filter((array) config('services.gemini.keys', [])));
        $this->model    = config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $this->groqApiKeys  = array_values(array_filter((array) config('services.groq.keys', [])));
        $this->groqModel    = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->groqEndpoint = 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**
     * Gọi AI. Thử lần lượt các key Gemini chưa bị rate-limit trước; nếu TẤT CẢ key Gemini đều
     * 429 và không thể auto-wait, tự động fallback sang Groq (nếu có cấu hình key Groq) trước
     * khi báo lỗi cho khách.
     *
     * @param bool $jsonMode true = ép trả JSON thuần; false = câu trả lời tự nhiên.
     * @param string $rateLimitedText Câu trả lời khi tất cả key (kể cả Groq) đều rate-limit và wait > ngưỡng.
     *   Dùng ":seconds" làm placeholder.
     * @param string $genericErrorText Câu trả lời khi lỗi khác 429.
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
        $result = $this->callProvider('gemini', $systemPrompt, $userPrompt, $jsonMode, $temperature, $maxOutputTokens);

        if ($result['ok']) {
            return $result['text'];
        }

        // Gemini hết quota / lỗi hết cách xử lý -> thử Groq nếu có cấu hình key
        if (!empty($this->groqApiKeys)) {
            Log::warning(static::class . ': Gemini thất bại/hết quota, fallback sang Groq.');

            $groqResult = $this->callProvider('groq', $systemPrompt, $userPrompt, $jsonMode, $temperature, $maxOutputTokens);

            if ($groqResult['ok']) {
                return $groqResult['text'];
            }

            // Cả 2 provider đều fail -> ưu tiên hiện thông báo rate-limit ngắn nhất nếu có
            $waitSeconds = min(
                $result['wait_seconds'] ?? PHP_INT_MAX,
                $groqResult['wait_seconds'] ?? PHP_INT_MAX
            );

            if ($waitSeconds !== PHP_INT_MAX) {
                return $jsonMode ? '{}' : str_replace(':seconds', (string) $waitSeconds, $rateLimitedText);
            }
        } elseif (($result['wait_seconds'] ?? null) !== null) {
            return $jsonMode ? '{}' : str_replace(':seconds', (string) $result['wait_seconds'], $rateLimitedText);
        }

        return $jsonMode ? '{}' : $genericErrorText;
    }

    /**
     * Thử gọi 1 provider cụ thể (gemini hoặc groq), xoay vòng qua các key chưa bị rate-limit
     * của provider đó, tự sleep+retry nếu key sắp mở nhất chỉ cần chờ ngắn.
     *
     * @return array{ok: bool, text: string, wait_seconds: int|null}
     */
    protected function callProvider(
        string $provider,
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode,
        ?float $temperature,
        ?int $maxOutputTokens
    ): array {
        $keys = $provider === 'groq' ? $this->groqApiKeys : $this->apiKeys;

        if (empty($keys)) {
            Log::error(static::class . ": chưa cấu hình API key nào cho provider {$provider}.");
            return ['ok' => false, 'text' => '', 'wait_seconds' => null];
        }

        set_time_limit(180);

        $temperature ??= $jsonMode ? 0.1 : 0.6;
        $maxOutputTokens ??= $jsonMode ? 2048 : 4096;

        $order = $this->rotatedKeyOrder($provider, $keys);
        $longestWaitSeconds  = null;
        $shortestWaitSeconds = null;
        $shortestWaitKey     = null;

        foreach ($order as $apiKey) {
            $rateLimitCacheKey = $this->rateLimitCacheKeyFor($provider, $apiKey);
            $limitedUntil = Cache::get($rateLimitCacheKey);

            if ($limitedUntil && now()->lt($limitedUntil)) {
                $secondsLeft = now()->diffInSeconds($limitedUntil);
                $longestWaitSeconds = max($longestWaitSeconds ?? 0, $secondsLeft);
                if ($shortestWaitSeconds === null || $secondsLeft < $shortestWaitSeconds) {
                    $shortestWaitSeconds = $secondsLeft;
                    $shortestWaitKey     = $apiKey;
                }
                continue;
            }

            $attempt = $this->attemptRequest($provider, $apiKey, $systemPrompt, $userPrompt, $jsonMode, $temperature, $maxOutputTokens);

            if ($attempt['ok']) {
                return ['ok' => true, 'text' => $attempt['text'], 'wait_seconds' => null];
            }

            if ($attempt['rate_limited']) {
                Cache::put($rateLimitCacheKey, now()->addSeconds($attempt['wait_seconds']), $attempt['wait_seconds']);
                $longestWaitSeconds = max($longestWaitSeconds ?? 0, $attempt['wait_seconds']);
                if ($shortestWaitSeconds === null || $attempt['wait_seconds'] < $shortestWaitSeconds) {
                    $shortestWaitSeconds = $attempt['wait_seconds'];
                    $shortestWaitKey     = $apiKey;
                }
                continue;
            }

            if (!empty($attempt['retryable'])) {
                continue;
            }

            return ['ok' => false, 'text' => '', 'wait_seconds' => null];
        }

        if ($longestWaitSeconds === null) {
            return ['ok' => false, 'text' => '', 'wait_seconds' => null];
        }

        if ($shortestWaitKey !== null && $shortestWaitSeconds !== null
            && $shortestWaitSeconds <= self::MAX_AUTO_WAIT_SECONDS
        ) {
            $sleepSeconds = (int) ceil((float) $shortestWaitSeconds) + 1;
            Log::info(static::class . " All {$provider} keys rate-limited; auto-sleeping {$sleepSeconds}s then retrying key "
                . substr(md5($shortestWaitKey), 0, 12));
            sleep($sleepSeconds);

            Cache::forget($this->rateLimitCacheKeyFor($provider, $shortestWaitKey));
            $retryAttempt = $this->attemptRequest($provider, $shortestWaitKey, $systemPrompt, $userPrompt, $jsonMode, $temperature, $maxOutputTokens);
            if ($retryAttempt['ok']) {
                return ['ok' => true, 'text' => $retryAttempt['text'], 'wait_seconds' => null];
            }
        }

        return ['ok' => false, 'text' => '', 'wait_seconds' => (int) ceil((float) $longestWaitSeconds)];
    }

    /**
     * Thứ tự thử key trong lần gọi này — xoay vòng bắt đầu từ vị trí lưu trong cache (riêng theo provider).
     *
     * @param string[] $keys
     * @return string[]
     */
    protected function rotatedKeyOrder(string $provider, array $keys): array
    {
        $count = count($keys);
        if ($count <= 1) {
            return $keys;
        }

        $cacheKey = self::ROTATION_INDEX_CACHE_KEY . $provider;
        $startIndex = ((int) Cache::get($cacheKey, 0)) % $count;
        Cache::put($cacheKey, ($startIndex + 1) % $count, now()->addDay());

        $order = [];
        for ($i = 0; $i < $count; $i++) {
            $order[] = $keys[($startIndex + $i) % $count];
        }

        return $order;
    }

    /** Cache key rate-limit RIÊNG cho từng API key + provider (theo hash). */
    protected function rateLimitCacheKeyFor(string $provider, string $apiKey): string
    {
        return self::RATE_LIMIT_CACHE_PREFIX . $provider . ':' . substr(md5($apiKey), 0, 12);
    }

    /**
     * Gọi thật sự 1 request tới 1 provider cụ thể với 1 API key cụ thể. Timeout 10 giây/request.
     * Tự build request body + parse response theo đúng format riêng của từng provider.
     *
     * @return array{ok: bool, text: string, rate_limited: bool, retryable: bool, wait_seconds: int}
     */
    protected function attemptRequest(
        string $provider,
        string $apiKey,
        string $systemPrompt,
        string $userPrompt,
        bool $jsonMode,
        float $temperature,
        int $maxOutputTokens
    ): array {
        try {
            if ($provider === 'groq') {
                $body = [
                    'model'    => $this->groqModel,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens'  => $maxOutputTokens,
                ];
                if ($jsonMode) {
                    $body['response_format'] = ['type' => 'json_object'];
                }

                $response = Http::timeout(10)
                    ->withToken($apiKey)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->groqEndpoint, $body);
            } else {
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

                $response = Http::timeout(10)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->endpoint . '?key=' . $apiKey, $body);
            }

            if ($response->failed()) {
                Log::error(static::class . " {$provider} API error", [
                    'status'        => $response->status(),
                    'body'          => $response->body(),
                    'api_key_hash'  => substr(md5($apiKey), 0, 12),
                ]);

                if ($response->status() === 429) {
                    $waitSeconds = $this->parseRetryAfter($provider, $response);
                    return ['ok' => false, 'text' => '', 'rate_limited' => true, 'retryable' => true, 'wait_seconds' => $waitSeconds];
                }

                $is5xx = $response->status() >= 500 && $response->status() < 600;

                return ['ok' => false, 'text' => '', 'rate_limited' => false, 'retryable' => $is5xx, 'wait_seconds' => 0];
            }

            $json = $response->json();

            if ($provider === 'groq') {
                $text = data_get($json, 'choices.0.message.content', $jsonMode ? '{}' : '');
            } else {
                $finishReason = data_get($json, 'candidates.0.finishReason');
                if ($finishReason === 'MAX_TOKENS') {
                    Log::warning(static::class . ' Gemini response bị cắt do vượt maxOutputTokens');
                }
                $text = data_get($json, 'candidates.0.content.parts.0.text', $jsonMode ? '{}' : '');
            }

            return ['ok' => true, 'text' => $text, 'rate_limited' => false, 'retryable' => false, 'wait_seconds' => 0];
        } catch (\Throwable $e) {
            Log::error(static::class . " {$provider} call failed: " . $e->getMessage());
            return ['ok' => false, 'text' => '', 'rate_limited' => false, 'retryable' => true, 'wait_seconds' => 0];
        }
    }

    /**
     * Groq trả thời gian chờ qua header "Retry-After" (giây); Gemini nhét trong body dạng
     * chuỗi "Please retry in 42.69s.".
     */
    protected function parseRetryAfter(string $provider, $response): int
    {
        if ($provider === 'groq') {
            $retryAfter = $response->header('Retry-After');
            if ($retryAfter !== null && is_numeric($retryAfter)) {
                return (int) ceil((float) $retryAfter);
            }
            return 30; // fallback mặc định nếu Groq không trả header
        }

        preg_match('/retry in ([\d.]+)s/i', $response->body(), $m);
        return isset($m[1]) ? (int) ceil((float) $m[1]) : 60;
    }

    protected function stripJsonFence(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*|^```\s*|```$/m', '', $text);
        return trim($text);
    }
}