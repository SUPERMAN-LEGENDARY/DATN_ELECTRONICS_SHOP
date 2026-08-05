<?php

namespace App\Services;

use App\Services\Concerns\HasStoreTaxonomy;
use App\Services\Concerns\InteractsWithGemini;
use Illuminate\Support\Facades\Cache;

/**
 * Phân tích câu tìm kiếm tự do (thanh search trên header) thành bộ lọc có cấu trúc:
 * từ khóa, thuộc tính kỹ thuật (RAM, màu sắc, bộ nhớ...), khoảng giá, danh mục, hãng, sắp xếp.
 *
 * Khác với GeminiChatService: không có hội thoại/lịch sử, không sinh câu trả lời tự nhiên,
 * chỉ trả về JSON lọc để ProductController dùng dựng query Eloquent.
 */
class AiSearchParser
{
    use InteractsWithGemini, HasStoreTaxonomy;

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
        $this->initGeminiClient();
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
        // Danh sách category/brand/thuộc tính THẬT trong DB — lấy động qua HasStoreTaxonomy
        // (dùng CHUNG với GeminiChatService) thay vì hardcode, để search trên thanh header
        // không lọc sai với sản phẩm ngoài danh sách category/thuộc tính cũ.
        $categorySlugs = $this->allowedCategorySlugs();
        $categoryList  = $categorySlugs ? implode(', ', $categorySlugs) : '(chưa có category nào trong hệ thống)';

        $brandSlugs = $this->allowedBrandSlugs();
        $brandList  = $brandSlugs ? implode(', ', $brandSlugs) : '(chưa có brand nào trong hệ thống)';

        $attributeNames = $this->allowedAttributeNames();
        $attributeList  = $attributeNames ? implode(', ', $attributeNames) : '(chưa có thuộc tính nào trong hệ thống)';

        $system = <<<PROMPT
Bạn là bộ phân tích câu tìm kiếm cho thanh search của một cửa hàng điện tử online. Đọc câu tìm kiếm của khách
rồi CHỈ trả về một JSON object hợp lệ, không thêm chữ nào khác, không markdown, không giải thích. Cấu trúc bắt buộc:

{
  "keywords": string,            // từ khóa chính còn lại sau khi đã tách giá/thuộc tính, vd "điện thoại", "tai nghe chống ồn"
  "category": string|null,       // CHỈ dùng đúng slug có trong danh sách category thật sau: {$categoryList}
                                  // Nếu không có slug nào khớp rõ ràng, để null (không đoán bừa sang slug khác).
  "brand": string|null,          // CHỈ dùng đúng slug có trong danh sách brand thật sau: {$brandList}
                                  // Nếu không có slug nào khớp rõ ràng, để null (không đoán bừa sang slug khác).
  "price_min": number|null,
  "price_max": number|null,
  "sort": "price_asc" | "price_desc" | "newest" | "rating" | null,
  "attributes": object           // cặp thuộc tính kỹ thuật, CHỈ dùng đúng tên trong danh sách sau:
                                  // {$attributeList}
                                  // vd {"RAM":"8GB","Màu sắc":"đỏ"}. Rỗng {} nếu khách không nêu thuộc tính cụ thể,
                                  // hoặc nếu khách hỏi thuộc tính không có trong danh sách trên.
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

        $raw = $this->callGemini($system, "Câu tìm kiếm: \"{$query}\"", jsonMode: true);
        $parsed = json_decode($this->stripJsonFence($raw), true);

        if (!is_array($parsed)) {
            return array_merge(self::DEFAULTS, ['keywords' => $query]);
        }

        $result = array_merge(self::DEFAULTS, $parsed);
        $result['attributes'] = is_array($result['attributes'] ?? null) ? array_filter($result['attributes']) : [];

        // An toàn thêm: nếu Gemini lỡ trả về category/brand ngoài danh sách slug thật (dù đã
        // dặn trong prompt), bỏ về null thay vì để ProductController query nhầm slug không tồn tại.
        if (!empty($result['category']) && !in_array($result['category'], $categorySlugs, true)) {
            $result['category'] = null;
        }
        if (!empty($result['brand']) && !in_array($result['brand'], $brandSlugs, true)) {
            $result['brand'] = null;
        }

        // Nếu AI không tách được từ khóa nào, fallback về nguyên văn câu tìm kiếm
        if (trim((string) $result['keywords']) === '' && empty($result['attributes']) && empty($result['category'])) {
            $result['keywords'] = $query;
        }

        return $result;
    }
}