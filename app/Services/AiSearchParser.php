<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phân tích câu tìm kiếm tự do (thanh search trên header) thành bộ lọc có cấu trúc:
 * từ khóa, thuộc tính kỹ thuật (RAM, màu sắc, bộ nhớ...), khoảng giá, danh mục, hãng, sắp xếp.
 *
 * Khác với GeminiChatService: không có hội thoại/lịch sử, không sinh câu trả lời tự nhiên,
 * chỉ trả về JSON lọc để ProductController dùng dựng query Eloquent.
 */
class AiSearchParser
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    protected const DEFAULTS = [
        'keywords'   => '',
        'category'   => null,
        'brand'      => null,
        'price_min'  => null,
        'price_max'  => null,
        'sort'       => null,
        'attributes' => [],
    ];

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.key');
        $this->model    = config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Phân tích chuỗi tìm kiếm thành bộ lọc có cấu trúc. Có cache theo nội dung query
     * để tránh gọi AI lặp lại khi người dùng phân trang / đổi sort với cùng từ khóa.
     */
    public function parse(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return self::DEFAULTS;
        }

        $cacheKey = 'ai_search:' . md5(mb_strtolower($query));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($query) {
            return $this->callAndParse($query);
        });
    }

    protected function callAndParse(string $query): array
    {
        $system = <<<PROMPT
Bạn là bộ phân tích câu tìm kiếm cho thanh search của một cửa hàng điện tử online. Đọc câu tìm kiếm của khách
rồi CHỈ trả về một JSON object hợp lệ, không thêm chữ nào khác, không markdown, không giải thích. Cấu trúc bắt buộc:

{
  "keywords": string,            // từ khóa chính còn lại sau khi đã tách giá/thuộc tính, vd "điện thoại", "tai nghe chống ồn"
  "category": string|null,       // một trong: dien-thoai, laptop, tai-nghe, may-tinh-bang, phu-kien, hoặc null nếu không rõ
  "brand": string|null,          // tên hãng viết thường không dấu, vd apple, samsung, xiaomi, sony, dell, hoặc null
  "price_min": number|null,
  "price_max": number|null,
  "sort": "price_asc" | "price_desc" | "newest" | "rating" | null,
  "attributes": object           // cặp thuộc tính kỹ thuật, CHỈ dùng đúng tên trong danh sách:
                                  // RAM, Bộ nhớ trong, Màn hình, Pin, CPU, GPU, Hệ điều hành, Màu sắc, Trọng lượng, Kết nối
                                  // vd {"RAM":"8GB","Màu sắc":"đỏ"}. Rỗng {} nếu khách không nêu thuộc tính cụ thể.
}

Quy tắc:
- Khoảng giá: "dưới 10 triệu" -> price_max=10000000; "trên 20 triệu" -> price_min=20000000;
  "15-20 triệu" -> price_min=15000000, price_max=20000000; "khoảng 5 triệu" -> price_min=4000000, price_max=6000000.
- "rẻ nhất", "giá thấp nhất" -> sort=price_asc; "đắt nhất", "giá cao nhất", "cao cấp nhất" -> sort=price_desc;
  "mới nhất" -> sort=newest; "đánh giá cao", "tốt nhất" -> sort=rating.
- Khi câu có thông số kỹ thuật cụ thể (RAM, dung lượng, màu, pin, chip, hệ điều hành...), tách vào "attributes"
  thay vì để trong "keywords". Vd "iphone 256gb màu đen dưới 30 triệu" ->
  keywords="iphone", attributes={"Bộ nhớ trong":"256GB","Màu sắc":"đen"}, price_max=30000000.
- Nếu khách chỉ gõ từ khóa ngẫu nhiên không rõ ý (vd "quà tặng sinh nhật", "đồ công nghệ hot"), để keywords
  nguyên văn câu tìm kiếm, các trường khác để null/{}.
- Nếu không chắc, đặt giá trị null (hoặc {} cho attributes) thay vì đoán bừa.
PROMPT;

        $raw = $this->callGemini($system, "Câu tìm kiếm: \"{$query}\"");
        $parsed = json_decode($this->stripJsonFence($raw), true);

        if (!is_array($parsed)) {
            return array_merge(self::DEFAULTS, ['keywords' => $query]);
        }

        $result = array_merge(self::DEFAULTS, $parsed);
        $result['attributes'] = is_array($result['attributes'] ?? null) ? array_filter($result['attributes']) : [];

        // Nếu AI không tách được từ khóa nào, fallback về nguyên văn câu tìm kiếm
        if (trim((string) $result['keywords']) === '' && empty($result['attributes']) && empty($result['category'])) {
            $result['keywords'] = $query;
        }

        return $result;
    }

    protected function callGemini(string $systemPrompt, string $userPrompt): string
    {
        $body = [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
            'generationConfig'   => [
                'temperature'      => 0.1,
                'maxOutputTokens'  => 512,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint . '?key=' . $this->apiKey, $body);

            if ($response->failed()) {
                Log::error('AiSearchParser Gemini error', ['status' => $response->status(), 'body' => $response->body()]);
                return '{}';
            }

            return data_get($response->json(), 'candidates.0.content.parts.0.text', '{}');
        } catch (\Throwable $e) {
            Log::error('AiSearchParser Gemini call failed: ' . $e->getMessage());
            return '{}';
        }
    }

    protected function stripJsonFence(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*|^```\s*|```$/m', '', $text);
        return trim($text);
    }
}