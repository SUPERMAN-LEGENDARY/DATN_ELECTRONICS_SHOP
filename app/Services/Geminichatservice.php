<?php

namespace App\Services;

use App\Models\AiSession;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\CustomerAiProfile;
use App\Models\Event;
use App\Models\News;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\Concerns\HasStoreTaxonomy;
use App\Services\Concerns\InteractsWithGemini;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GeminiChatService
{
    use InteractsWithGemini, HasStoreTaxonomy;

    /**
     * Cột LUÔN loại trừ khi dump nguyên "getAttributes()" của model (Voucher/Event/Order/
     * OrderItem) ra prompt gửi cho Gemini — dùng blacklist chung này làm lưới an toàn thêm,
     * phòng khi DB có thêm cột nhạy cảm sau này (thông tin thanh toán, ghi chú nội bộ...) mà
     * code quên cập nhật except() riêng theo từng model. Loại trừ key không tồn tại là vô hại
     * (Collection::except() bỏ qua key thiếu).
     */
    protected const SENSITIVE_FIELDS_EXCLUDE = [
        'password', 'remember_token', 'token', 'api_key', 'secret',
        'payment_token', 'payment_details', 'card_number', 'card_last4', 'cvv',
        'gateway_transaction_id', 'gateway_response', 'ip_address', 'user_agent',
        'internal_note', 'admin_note', 'staff_note', 'note_internal',
    ];

    public function __construct()
    {
        $this->initGeminiClient();
    }

    /**
     * Điểm vào chính: xử lý 1 tin nhắn của khách và trả về phản hồi.
     */
    public function handle(string $sessionToken, string $userMessage): array
    {
        $session = $this->loadSession($sessionToken);
        $history = $session->messages ?? [];

        // Bước 1: dùng Gemini để hiểu ý định + trích từ khóa/thuộc tính tìm kiếm có cấu trúc
        $intent = $this->extractIntent($userMessage, $history);

        // Bước 2: tùy ý định, truy vấn dữ liệu sản phẩm thật trong DB
        $products = collect();
        $compareTable = null;

        // Trước đây chỉ tìm sản phẩm khi intent === 'search'/'buy'. Vấn đề: nếu Gemini phân loại
        // nhầm intent (vd do lượt trước vừa nói về voucher/tin tức/đơn hàng khiến ngữ cảnh "chảy"
        // sang, gắn nhầm intent='support' cho câu hỏi sản phẩm rõ ràng tiếp theo) thì bước tìm sản
        // phẩm bị BỎ QUA HOÀN TOÀN dù Gemini vẫn trích đúng keywords/compare_names — khiến bot báo
        // "không tìm thấy" một cách oan uổng. Nới điều kiện: hễ Gemini có trích ra được từ khóa
        // hoặc tên sản phẩm cụ thể (dù intent bị gắn nhãn support/unknown), vẫn cứ thử tìm — không
        // để cả bước tìm kiếm phụ thuộc 100% vào việc phân loại intent phải tuyệt đối chính xác.
        $looksLikeProductQuery = trim((string) ($intent['keywords'] ?? '')) !== '' || !empty($intent['compare_names']);
        $shouldSearchProducts = in_array($intent['intent'], ['search', 'buy'])
            || ($looksLikeProductQuery && in_array($intent['intent'], ['support', 'unknown']));

        if ($shouldSearchProducts) {
            $products = $this->searchProducts($intent);

            // Fallback: nếu không ra kết quả và đây có vẻ là câu hỏi nối tiếp (không có lịch sử trống),
            // thử lại bằng từ khóa tìm kiếm gần nhất đã lưu trong session.
            if ($products->isEmpty() && !empty($history)) {
                $lastKeyword = collect($session->search_keywords ?? [])->last();
                if ($lastKeyword && trim((string) ($intent['keywords'] ?? '')) !== $lastKeyword) {
                    $fallbackIntent = $intent;
                    $fallbackIntent['keywords'] = $lastKeyword;
                    $fallbackIntent['compare_names'] = [];
                    $products = $this->searchProducts($fallbackIntent);
                }
            }

            // Nếu lọc theo thuộc tính (màu/dung lượng/RAM...) không ra kết quả nào,
            // nới lỏng bỏ thuộc tính để không trả lời "không tìm thấy" một cách cụt lủn —
            // AI vẫn còn dữ liệu sản phẩm gốc để giải thích "có sản phẩm nhưng không có màu/tuỳ chọn đó".
            if ($products->isEmpty() && !empty($intent['attributes'])) {
                $relaxedIntent = $intent;
                $relaxedIntent['attributes'] = [];
                $products = $this->searchProducts($relaxedIntent, narrowVariantsToAttributes: false);
            }

            // Fallback quan trọng: nếu vẫn không ra kết quả, rất có thể category/brand mà AI
            // đoán không khớp đúng slug thật trong DB (vd category ngoài danh sách quen thuộc,
            // brand viết khác slug thật). Thử lại bỏ hẳn category + brand, chỉ giữ từ khóa/giá,
            // để không báo "không tìm thấy" oan cho sản phẩm thực ra vẫn tồn tại.
            if ($products->isEmpty() && (!empty($intent['category']) || !empty($intent['brand']))) {
                $relaxedIntent2 = $intent;
                $relaxedIntent2['category']   = null;
                $relaxedIntent2['brand']      = null;
                $relaxedIntent2['attributes'] = [];
                $products = $this->searchProducts($relaxedIntent2, narrowVariantsToAttributes: false);
            }

            // Fallback cuối: câu hỏi nối tiếp mơ hồ sau một lượt SO SÁNH (vd "có các bản màu nào"
            // sau khi vừa so sánh 2 sản phẩm) khiến Gemini không chắc đang hỏi về sản phẩm nào nên
            // keywords có thể rỗng/sai — lấy tạm sản phẩm vừa được nhắc tới gần nhất trong phiên
            // chat thay vì báo "không tìm thấy" khi rõ ràng khách vừa xem sản phẩm đó.
            if ($products->isEmpty() && !empty($history)) {
                $products = $this->recentDistinctProducts($session, 1);
            }
        } elseif ($intent['intent'] === 'compare') {
            if (($intent['compare_target'] ?? 'products') === 'variants') {
                // So sánh các phiên bản/màu/dung lượng... của CÙNG 1 sản phẩm
                $baseIntent = $intent;
                $baseIntent['attributes'] = []; // không lọc, cần lấy đủ variant để so sánh
                $baseIntent['compare_names'] = array_slice($intent['compare_names'] ?? [], 0, 1);
                $matched = $this->searchProducts($baseIntent, limit: 1, narrowVariantsToAttributes: false);
                $products = $matched;
                $compareTable = $matched->isNotEmpty()
                    ? $this->buildVariantCompareTable($matched->first(), $intent['compare_names'] ?? [])
                    : null;
            } elseif (empty($intent['compare_names'])) {
                // Khách so sánh kiểu "máy nào chơi game/camera/pin tốt hơn" mà không nêu lại tên máy
                // -> lấy 2 sản phẩm vừa được nhắc tới gần nhất trong phiên chat để so sánh.
                $products = $this->recentDistinctProducts($session, 3);
                $compareTable = $products->isNotEmpty() ? $this->buildCompareTable($products) : null;
            } else {
                $products = $this->searchProducts($intent, limit: 6, narrowVariantsToAttributes: false);

                // Fallback: nếu Gemini lỡ gán category/brand sai lệch hoặc tên không khớp đúng
                // hết (vd chỉ tìm ra 1/2 sản phẩm), thử lại từng tên riêng lẻ qua đường tìm kiếm
                // theo keywords (đã có AND-toàn-bộ-từ, khoan dung hơn is 'name LIKE' đơn thuần) để
                // không báo "không tìm thấy" oan trong khi 1 hoặc cả 2 sản phẩm thực ra vẫn tồn tại.
                if ($products->count() < count($intent['compare_names'] ?? [])) {
                    $extra = collect();
                    foreach ($intent['compare_names'] ?? [] as $name) {
                        if ($products->contains(fn ($p) => Str::contains(Str::lower($p->name), Str::lower($name)))) {
                            continue;
                        }
                        $nameIntent = $intent;
                        $nameIntent['keywords'] = $name;
                        $nameIntent['compare_names'] = [];
                        $nameIntent['category'] = null;
                        $nameIntent['brand'] = null;
                        $found = $this->searchProducts($nameIntent, limit: 1, narrowVariantsToAttributes: false);
                        $extra = $extra->merge($found);
                    }
                    if ($extra->isNotEmpty()) {
                        $products = $products->merge($extra)->unique('id')->values();
                    }
                }

                // Vẫn không ra sản phẩm nào -> thử lấy các sản phẩm vừa nhắc tới gần nhất trong
                // phiên chat, phòng trường hợp tên khách gõ lệch quá xa dữ liệu thật.
                if ($products->isEmpty()) {
                    $products = $this->recentDistinctProducts($session, 3);
                }

                $compareTable = $products->isNotEmpty() ? $this->buildCompareTable($products) : null;
            }
        } elseif ($intent['intent'] === 'consult') {
            // Tư vấn theo nhu cầu: chỉ tìm sản phẩm khi đã có ĐỦ ngân sách; còn ưu tiên
            // (gaming/camera/pin...) là tuỳ chọn — nếu chưa có, để Gemini tự hỏi thêm khách
            // thay vì liệt kê sản phẩm ngẫu nhiên trong ngân sách.
            if (!empty($intent['consult_budget'])) {
                $consultIntent = $intent;
                $consultIntent['price_max'] = $intent['consult_budget'];
                $consultIntent['price_min'] = null;
                $products = $this->searchProducts($consultIntent, limit: 10, narrowVariantsToAttributes: false);
            }
        }

        // Bước 3: sinh câu trả lời tự nhiên dựa trên dữ liệu thật (tránh AI bịa sản phẩm)
        $reply = $this->generateAnswer($userMessage, $history, $intent, $products, $compareTable);

        $this->saveTurn($session, $userMessage, $reply, $intent, $products);

        return [
            'reply'    => $reply,
            'intent'   => $intent['intent'],
            'products' => $products->map(fn ($p) => $this->productSummary($p))->values(),
        ];
    }

    /**
     * Gọi Gemini lần 1: phân loại ý định + trích bộ lọc tìm kiếm dạng JSON.
     */
    protected function extractIntent(string $userMessage, array $history): array
    {
        $historyText = $this->formatHistoryShort($history);
        $attributeNames = $this->allowedAttributeNames();
        $attributeList = $attributeNames ? implode(', ', $attributeNames) : '(chưa có thuộc tính nào trong hệ thống)';

        $categorySlugs = $this->allowedCategorySlugs();
        $categoryList  = $categorySlugs ? implode(', ', $categorySlugs) : '(chưa có category nào trong hệ thống)';

        $brandSlugs = $this->allowedBrandSlugs();
        $brandList  = $brandSlugs ? implode(', ', $brandSlugs) : '(chưa có brand nào trong hệ thống)';

        $system = <<<PROMPT
Bạn là bộ phân tích ý định cho chatbot bán hàng điện tử. Đọc tin nhắn mới nhất của khách
(và lịch sử hội thoại ngắn nếu có) rồi CHỈ trả về một JSON object hợp lệ, không thêm chữ nào khác,
không markdown, không giải thích. Cấu trúc JSON bắt buộc:

{
  "intent": "search" | "compare" | "consult" | "support" | "buy" | "news" | "order" | "unknown",
  "keywords": string,            // từ khóa chính, tiếng Việt tự nhiên, vd "điện thoại pin trâu"
  "category": string|null,       // CHỈ dùng đúng slug có trong danh sách category thật sau: {$categoryList}
                                  // Nếu không có slug nào khớp rõ ràng, để null (không đoán bừa sang slug khác).
  "brand": string|null,          // CHỈ dùng đúng slug có trong danh sách brand thật sau: {$brandList}
                                  // Nếu không có slug nào khớp rõ ràng, để null (không đoán bừa sang slug khác).
  "price_min": number|null,
  "price_max": number|null,
  "sort": "price_asc" | "price_desc" | null,  // "rẻ nhất"/"giá thấp nhất" -> price_asc; "đắt nhất"/"giá cao nhất" -> price_desc
  "attributes": object,          // cặp thuộc tính kỹ thuật khách nêu ra, CHỈ dùng đúng tên trong danh sách:
                                  // {$attributeList}
                                  // vd {"RAM":"8GB","Màu sắc":"đen"}. Rỗng {} nếu khách không nêu thuộc tính cụ thể.
  "compare_target": "products" | "variants" | null,
                                  // "products" khi so sánh 2+ sản phẩm KHÁC tên/dòng với nhau;
                                  // "variants" khi so sánh các PHIÊN BẢN/MÀU/DUNG LƯỢNG khác nhau của
                                  // CÙNG MỘT sản phẩm (vd "so sánh bản đen với bản trắng", "128GB với 256GB
                                  // của iphone 15 khác nhau gì"); null nếu không phải compare.
  "compare_names": string[],     // nếu compare_target=products: tên/model từng sản phẩm muốn so sánh.
                                  // nếu compare_target=variants: [0]=tên sản phẩm, các phần tử sau (nếu có)
                                  // là nhãn phiên bản/màu/dung lượng cụ thể khách muốn so sánh (để trống nếu
                                  // khách muốn xem hết tất cả phiên bản). Rỗng nếu không phải compare.
                                  // Nếu khách so sánh kiểu "máy nào chơi game/camera/pin tốt hơn" mà KHÔNG
                                  // nêu lại tên máy, để compare_names=[] — hệ thống sẽ tự lấy 2 sản phẩm
                                  // vừa nhắc tới gần nhất trong lịch sử hội thoại.
  "consult_budget": number|null, // dùng khi intent=consult: khách nêu ngân sách nhưng CHƯA nêu tên/loại sản phẩm
                                  // cụ thể (vd "khoảng 10 triệu", "tầm 5-7tr"), lấy giá trị trung bình nếu là khoảng.
  "consult_priority": "gaming" | "camera" | "battery" | "performance" | "display" | "value" | null
                                  // ưu tiên khách chọn khi tư vấn theo nhu cầu: gaming (chơi game/hiệu năng đồ họa),
                                  // camera (chụp ảnh/quay phim), battery (pin trâu), performance (hiệu năng chung/mượt),
                                  // display (màn hình đẹp), value (giá tốt/tiết kiệm). null nếu khách chưa chọn.
}

Quy tắc:
- "so sánh", "vs", "khác nhau", "máy nào ... hơn" -> intent = compare.
- hỏi giá, tìm, gợi ý, "có ... không", "có màu gì", "có bản nào" -> intent = search.
- Khách nêu NGÂN SÁCH/nhu cầu chung chung nhưng CHƯA rõ muốn mua loại/hãng/tên máy nào cụ thể
  (vd "khoảng 10 triệu", "tư vấn giúp mình 1 con điện thoại", "chơi game thì mua máy nào") -> intent = consult.
  Nếu khách đã có tên sản phẩm/hãng/loại rõ ràng thì dùng intent = search, không dùng consult.
- muốn mua, đặt hàng, thêm giỏ -> intent = buy.
- hỏi chính sách, bảo hành, vận chuyển, đổi trả, thanh toán, cửa hàng, tài khoản, khuyến mãi/giảm giá/voucher/mã giảm giá
  đang có, sự kiện/chương trình khuyến mãi đang diễn ra (banner trang chủ, flash sale, deal hot...), hoặc báo lỗi kỹ
  thuật máy (không sạc được, không nhận SIM, không vào wifi...) -> intent = support.
- hỏi về tin tức, bài viết, blog công nghệ, "có tin gì mới không", "bài viết mới nhất" -> intent = news.
- hỏi về đơn hàng CỦA CHÍNH KHÁCH (vd "đơn hàng của tôi", "đơn tôi vừa đặt", "đơn hàng tới đâu rồi", "kiểm tra đơn hàng",
  "đã giao chưa", "trạng thái đơn hàng") -> intent = order. Đây LUÔN là câu hỏi về lịch sử/đơn mua của chính khách,
  không phải hỏi về sản phẩm.
- Khoảng giá: "dưới 10 triệu" -> price_max=10000000; "trên 20 triệu" -> price_min=20000000; "15-20 triệu" -> price_min=15000000, price_max=20000000; "khoảng 5 triệu" -> price_min=4000000, price_max=6000000.
- "rẻ nhất", "giá thấp nhất", "tiết kiệm nhất" -> sort=price_asc, keywords để rỗng hoặc rất ngắn (không lặp lại cụm "rẻ nhất" vào keywords).
- "đắt nhất", "giá cao nhất", "cao cấp nhất" -> sort=price_desc, keywords để rỗng hoặc rất ngắn.
- Khi khách nêu thông số kỹ thuật cụ thể (RAM, dung lượng, màu, pin, chip, hệ điều hành, camera, 5G, NFC,
  chống nước, eSIM, xuất xứ...), tách vào "attributes" thay vì để trong "keywords", CHỈ dùng đúng tên có
  trong danh sách thuộc tính cho phép ở trên — nếu khách hỏi về thuộc tính không có trong danh sách đó thì
  bỏ qua trường đó (không tự bịa tên thuộc tính mới). Vd "iphone màu đen 256gb" -> keywords="iphone",
  attributes={"Bộ nhớ trong":"256GB","Màu sắc":"đen"}.
- Nếu không chắc, đặt giá trị null (hoặc {}/[] tương ứng) thay vì đoán bừa.
- QUAN TRỌNG - xử lý câu hỏi nối tiếp: nếu tin nhắn mới KHÔNG nêu rõ tên/loại sản phẩm (vd chỉ hỏi "phiên bản nào tốt",
  "có nên mua không", "còn hàng không", "giá bao nhiêu", "có màu khác không", dùng từ "nó"/"sản phẩm này"), hãy đọc
  lịch sử hội thoại gần nhất để xác định khách đang nói về sản phẩm nào, rồi đặt "keywords" là TÊN sản phẩm đó
  (lấy nguyên từ lịch sử, không bịa), không phải chỉ riêng cụm từ trong câu hỏi mới (vd "màu đen").
- QUAN TRỌNG - tư vấn theo nhu cầu nhiều lượt: nếu lịch sử gần nhất cho thấy BOT vừa hỏi khách ưu tiên điều gì
  (gaming/camera/pin/hiệu năng/màn hình/giá tốt) và tin nhắn mới của khách là câu trả lời cho câu hỏi đó (vd chỉ
  trả lời "chơi game", "camera đẹp", "pin trâu"...), giữ intent=consult, lấy lại consult_budget từ lịch sử
  (khách đã nêu trước đó) và đặt consult_priority theo câu trả lời mới này.
PROMPT;

        $userPrompt = "Lịch sử gần đây:\n{$historyText}\n\nTin nhắn mới của khách: \"{$userMessage}\"";

        $raw = $this->callGemini($system, $userPrompt, jsonMode: true);

        $parsed = json_decode($this->stripJsonFence($raw), true);

        $defaults = [
            'intent' => 'unknown', 'keywords' => $userMessage, 'category' => null,
            'brand' => null, 'price_min' => null, 'price_max' => null, 'sort' => null,
            'attributes' => [], 'compare_target' => null, 'compare_names' => [],
            'consult_budget' => null, 'consult_priority' => null,
        ];

        if (!is_array($parsed)) {
            return $defaults;
        }

        $result = array_merge($defaults, $parsed);
        $result['attributes'] = is_array($result['attributes'] ?? null)
            ? array_filter($result['attributes'], fn ($v) => $v !== null && $v !== '')
            : [];

        // DEBUG TẠM THỜI: log intent thật để chẩn đoán các ca "báo không tìm thấy sản phẩm"
        // dù sản phẩm chắc chắn có trong DB. Xoá dòng này sau khi debug xong.
        \Illuminate\Support\Facades\Log::info('GeminiChatService intent debug', [
            'user_message' => $userMessage,
            'intent'       => $result,
        ]);

        // An toàn thêm (giống AiSearchParser): nếu Gemini lỡ trả về category/brand ngoài danh
        // sách slug thật (dù đã dặn trong prompt, hoặc do cache taxonomy vừa hết hạn/đổi), bỏ về
        // null ngay thay vì để searchProducts() lọc theo slug không tồn tại và trả về rỗng oan.
        if (!empty($result['category']) && !in_array($result['category'], $categorySlugs, true)) {
            $result['category'] = null;
        }
        if (!empty($result['brand']) && !in_array($result['brand'], $brandSlugs, true)) {
            $result['brand'] = null;
        }

        return $result;
    }

    /**
     * Tìm sản phẩm thật trong DB dựa trên bộ lọc đã trích xuất (không để AI tự bịa sản phẩm).
     *
     * @param bool $narrowVariantsToAttributes  Khi true, chỉ eager-load các biến thể khớp đúng
     *   thuộc tính đã lọc (vd khách hỏi "có màu đỏ không" -> chỉ nạp biến thể màu đỏ, không phải
     *   toàn bộ biến thể). Khi so sánh (compare) cần thấy TẤT CẢ biến thể nên truyền false.
     */
    protected function searchProducts(array $intent, int $limit = 8, bool $narrowVariantsToAttributes = true)
    {
        $resolvedAttrs = $this->resolveAttributeFilters($intent['attributes'] ?? []);

        $query = Product::query()
            ->where('is_active', 1)
            ->with([
                'category',
                'brand',
                'attributes.attribute',
                'variants' => function ($q) use ($resolvedAttrs, $narrowVariantsToAttributes) {
                    $q->where('is_active', 1)->with('variantAttributes.attribute');

                    // Chỉ nạp đúng biến thể khớp thuộc tính khách hỏi (vd đúng màu/dung lượng),
                    // để bot không liệt kê nhầm biến thể không liên quan tới câu hỏi.
                    if ($narrowVariantsToAttributes && !empty($resolvedAttrs['variant'])) {
                        foreach ($resolvedAttrs['variant'] as $attributeId => $values) {
                            $q->whereHas('variantAttributes', function ($qa) use ($attributeId, $values) {
                                $qa->where('attribute_id', $attributeId);
                                $qa->where(function ($qv) use ($values) {
                                    foreach ($values as $v) {
                                        $qv->orWhere('value', 'like', '%' . $v . '%');
                                    }
                                });
                            });
                        }
                    }
                },
            ]);

        $keywords = trim((string) ($intent['keywords'] ?? ''));
        $names    = $intent['compare_names'] ?? [];

        if (!empty($names)) {
            $query->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('name', 'like', '%' . $name . '%');
                }
            });
        } elseif ($keywords !== '') {
            $terms = array_values(array_filter(preg_split('/\s+/', $keywords)));
            $query->where(function ($q) use ($terms, $keywords) {
                // Ưu tiên khớp nguyên cụm từ khóa trong tên/mô tả trước.
                $q->where('name', 'like', '%' . $keywords . '%')
                  ->orWhere('description', 'like', '%' . $keywords . '%');

                if (count($terms) > 1) {
                    // Nhiều từ (vd "iphone 6"): yêu cầu tên sản phẩm chứa TẤT CẢ các từ (AND),
                    // KHÔNG match riêng lẻ từng từ (OR) — tránh việc chỉ cần trùng 1 từ chung
                    // chung như "iphone" mà match nhầm sang "iPhone 7", "iPhone 15 Pro Max"...
                    $q->orWhere(function ($qAll) use ($terms) {
                        foreach ($terms as $term) {
                            if (mb_strlen($term) >= 2) {
                                $qAll->where('name', 'like', '%' . $term . '%');
                            }
                        }
                    });
                } elseif (count($terms) === 1 && mb_strlen($terms[0]) >= 3) {
                    // Chỉ 1 từ khóa duy nhất (vd "tai nghe" tách được 2 từ riêng, hoặc 1 từ như
                    // "iphone") thì vẫn cho phép match riêng lẻ như cũ.
                    $q->orWhere('name', 'like', '%' . $terms[0] . '%');
                }
            });
        }

        // Khi đã có compare_names (khách nêu rõ tên sản phẩm muốn xem/so sánh), tên đã đủ chính
        // xác để định vị đúng sản phẩm — KHÔNG áp thêm lọc category/brand nữa. Trước đây filter
        // này vẫn AND vào dù đã có tên, nên nếu Gemini đoán sai category/brand ở bước extractIntent
        // (dù vẫn là slug hợp lệ, ví dụ đoán nhầm brand) thì sẽ loại sạch luôn cả sản phẩm đã khớp
        // đúng tên — gây lỗi "không tìm thấy dữ liệu" dù sản phẩm thực sự tồn tại.
        if (empty($names)) {
            if (!empty($intent['category'])) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $intent['category']));
            }
            if (!empty($intent['brand'])) {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $intent['brand']));
            }
        }

        // Khoảng giá: khớp nếu giá GỐC sản phẩm nằm trong khoảng, HOẶC bất kỳ biến thể còn
        // hàng nào có giá nằm trong khoảng — tránh bỏ sót sản phẩm có nhiều mức giá theo phiên bản.
        $min = $intent['price_min'] ?? null;
        $max = $intent['price_max'] ?? null;
        if ($min || $max) {
            $query->where(function ($q) use ($min, $max) {
                $q->where(function ($qq) use ($min, $max) {
                    if ($min) $qq->where('price', '>=', $min);
                    if ($max) $qq->where('price', '<=', $max);
                })->orWhereHas('variants', function ($qv) use ($min, $max) {
                    $qv->where('is_active', 1);
                    if ($min) $qv->where('price', '>=', $min);
                    if ($max) $qv->where('price', '<=', $max);
                });
            });
        }

        // Lọc theo thuộc tính kỹ thuật: thuộc tính cấp sản phẩm (vd CPU, màn hình) áp lên bảng
        // product_attributes; thuộc tính cấp biến thể (vd màu sắc, dung lượng) áp lên biến thể
        // thông qua product_variant_attributes — dựa vào cờ Attribute::is_variant.
        foreach ($resolvedAttrs['product'] as $attributeId => $values) {
            $query->whereHas('attributes', function ($q) use ($attributeId, $values) {
                $q->where('attribute_id', $attributeId);
                $q->where(function ($qv) use ($values) {
                    foreach ($values as $v) {
                        $qv->orWhere('value', 'like', '%' . $v . '%');
                    }
                });
            });
        }
        foreach ($resolvedAttrs['variant'] as $attributeId => $values) {
            $query->whereHas('variants', function ($q) use ($attributeId, $values) {
                $q->where('is_active', 1)
                  ->whereHas('variantAttributes', function ($qa) use ($attributeId, $values) {
                      $qa->where('attribute_id', $attributeId);
                      $qa->where(function ($qv) use ($values) {
                          foreach ($values as $v) {
                              $qv->orWhere('value', 'like', '%' . $v . '%');
                          }
                      });
                  });
            });
        }

        $sort = $intent['sort'] ?? null;
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderByDesc('id');
        }

        return $query->limit($limit)->get();
    }

    /**
     * Lấy N sản phẩm khác nhau vừa được nhắc tới gần đây nhất trong phiên chat
     * (dựa trên AiSession::product_interactions), mới nhất trước — dùng khi khách so sánh
     * kiểu "máy nào chơi game tốt hơn" mà không lặp lại tên sản phẩm.
     */
    protected function recentDistinctProducts(AiSession $session, int $count = 2)
    {
        $productIds = collect($session->product_interactions ?? [])
            ->reverse()
            ->pluck('product_id')
            ->unique()
            ->take($count)
            ->values();

        if ($productIds->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->where('is_active', 1)
            ->whereIn('id', $productIds)
            ->with([
                'category', 'brand', 'attributes.attribute',
                'variants' => fn ($q) => $q->where('is_active', 1)->with('variantAttributes.attribute'),
            ])
            ->get();

        // Giữ lại đúng thứ tự "gần nhất trước" như trong lịch sử tương tác
        return $productIds->map(fn ($id) => $products->firstWhere('id', $id))->filter()->values();
    }

    /**
     * Ánh xạ tên thuộc tính (do AI trả về, vd "Màu sắc") sang attribute_id thật trong DB,
     * đồng thời tách riêng thuộc tính cấp sản phẩm và cấp biến thể dựa vào Attribute::is_variant.
     *
     * @return array{product: array<int,array<int,string>>, variant: array<int,array<int,string>>}
     */
    protected function resolveAttributeFilters(array $attributes): array
    {
        $result = ['product' => [], 'variant' => []];

        if (empty($attributes)) {
            return $result;
        }

        $models = Attribute::whereIn('name', array_keys($attributes))->get()->keyBy('name');

        foreach ($attributes as $name => $value) {
            $attribute = $models->get($name);
            if (!$attribute) {
                continue; // AI trả về tên thuộc tính lạ (ngoài danh sách cho phép) -> bỏ qua an toàn
            }

            $values = is_array($value)
                ? array_values(array_filter($value, fn ($v) => $v !== null && $v !== ''))
                : (($value !== null && $value !== '') ? [(string) $value] : []);

            if (empty($values)) {
                continue;
            }

            $bucket = $attribute->is_variant ? 'variant' : 'product';
            $result[$bucket][$attribute->id] = $values;
        }

        return $result;
    }

    /**
     * Dựng bảng so sánh thông số kỹ thuật giữa các SẢN PHẨM KHÁC NHAU (compare_target=products).
     * Kèm khoảng giá (giá thấp nhất - cao nhất theo biến thể, nếu có) để AI so sánh giá chính xác
     * thay vì chỉ dùng giá gốc của sản phẩm.
     */
    protected function buildCompareTable($products): array
    {
        $table = [];

        foreach ($products as $product) {
            $prices = $product->variants->isNotEmpty()
                ? $product->variants->pluck('price')->push($product->price)
                : collect([$product->price]);

            $row = [
                'name'       => $product->name,
                'price'      => $product->variants->isNotEmpty()
                    ? number_format((float) $prices->min()) . 'đ - ' . number_format((float) $prices->max()) . 'đ'
                    : number_format((float) $product->price) . 'đ',
                'tồn kho'    => (int) $product->stock,
            ];

            foreach ($product->attributes as $attr) {
                $attrName = $attr->attribute->name ?? null;
                if ($attrName) {
                    $row[$attrName] = $attr->value;
                }
            }

            if ($product->variants->isNotEmpty()) {
                $row['các phiên bản'] = $product->variants->map(fn ($v) => $v->label)->implode(', ');
            }

            $table[] = $row;
        }

        return $table;
    }

    /**
     * Dựng bảng so sánh các PHIÊN BẢN/MÀU/DUNG LƯỢNG khác nhau của CÙNG 1 sản phẩm
     * (compare_target=variants). Nếu $labelFilter có >1 phần tử (phần tử [0] là tên sản phẩm),
     * chỉ so sánh đúng những phiên bản khách nêu tên; ngược lại so sánh toàn bộ.
     */
    protected function buildVariantCompareTable(Product $product, array $labelFilter = []): ?array
    {
        $variants = $product->variants;

        $wantedLabels = array_slice($labelFilter, 1); // [0] là tên sản phẩm, phần còn lại là nhãn phiên bản
        if (!empty($wantedLabels)) {
            $variants = $variants->filter(function ($v) use ($wantedLabels) {
                foreach ($wantedLabels as $label) {
                    if (Str::contains(Str::lower($v->label), Str::lower($label))) {
                        return true;
                    }
                }
                return false;
            });

            // Không khớp nhãn nào -> vẫn so sánh toàn bộ để bot có gì đó để trả lời thay vì rỗng
            if ($variants->isEmpty()) {
                $variants = $product->variants;
            }
        }

        if ($variants->isEmpty()) {
            return null;
        }

        $table = [];
        foreach ($variants as $variant) {
            $row = [
                'sản phẩm'  => $product->name,
                'phiên bản' => $variant->label,
                'giá'       => number_format((float) $variant->price) . 'đ',
                'tồn kho'   => (int) $variant->stock,
            ];

            if ((float) $variant->list_price > (float) $variant->price) {
                $row['giá niêm yết'] = number_format((float) $variant->list_price) . 'đ';
            }

            foreach ($variant->variantAttributes as $attr) {
                $attrName = $attr->attribute->name ?? null;
                if ($attrName) {
                    $row[$attrName] = $attr->value;
                }
            }

            $table[] = $row;
        }

        return $table;
    }

    /**
     * Voucher đang khả dụng thật trong DB (điều kiện giống VoucherController::index — active,
     * còn hạn, chưa hết lượt dùng, và là voucher chung hoặc gán riêng cho khách đang đăng nhập).
     * Trước đây GeminiChatService không hề đụng tới bảng voucher nên chip "Khuyến mãi" luôn trả
     * lời chung chung/không chính xác.
     *
     * Không hardcode tên cột giảm giá cụ thể (vì chưa xác nhận chắc schema Voucher) — giữ
     * nguyên toàn bộ field còn lại dạng JSON để Gemini tự đọc và diễn giải đúng theo dữ liệu
     * thật, tránh phải sửa code này mỗi khi model Voucher thêm/đổi cột.
     */
    protected function activeVouchersForPrompt(): string
    {
        $userId = Auth::id();

        $query = Voucher::where('is_active', true)
            ->where(function ($q) use ($userId) {
                $q->whereNull('assigned_user_id')
                  ->orWhere('assigned_user_id', $userId);
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->whereColumn('used_count', '<', 'usage_limit')
            ->orderBy('expires_at')
            ->limit(10);

        // DEBUG TẠM THỜI: log SQL/bindings/giờ hệ thống thật lúc chạy để chẩn đoán vì sao
        // activeVouchersForPrompt() trả về rỗng dù DB có voucher còn hạn. Xoá sau khi debug xong.
        \Illuminate\Support\Facades\Log::info('activeVouchersForPrompt debug', [
            'now_app_tz'       => now()->toDateTimeString(),
            'app_timezone'     => config('app.timezone'),
            'db_timezone'      => config('database.connections.' . config('database.default') . '.timezone'),
            'sql'              => $query->toSql(),
            'bindings'         => $query->getBindings(),
            'auth_user_id'     => $userId,
            'matched_count'    => (clone $query)->count(),
            'raw_voucher_rows' => Voucher::withoutGlobalScopes()->get(['id', 'code', 'is_active', 'starts_at', 'expires_at', 'used_count', 'usage_limit', 'assigned_user_id', 'deleted_at'])->toArray(),
        ]);

        $vouchers = $query->get();

        if ($vouchers->isEmpty()) {
            return '';
        }

        $safe = $vouchers->map(fn ($v) => collect($v->getAttributes())
            ->except(array_merge(['id', 'user_id', 'assigned_user_id', 'created_at', 'updated_at'], self::SENSITIVE_FIELDS_EXCLUDE))
            ->all())->values();

        return json_encode($safe, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Sự kiện/chương trình khuyến mãi đang thực sự diễn ra (banner trang chủ, flash sale...).
     * Dùng scope active() + ongoing() có sẵn trên Event model. Trả về JSON dạng generic
     * (dump toàn bộ attribute còn lại, giống activeVouchersForPrompt) để không phải đoán
     * bừa những cột nào là "quan trọng" — Gemini tự đọc và diễn giải theo dữ liệu thật.
     */
    protected function ongoingEventsForPrompt(): string
    {
        $events = Event::query()
            ->active()
            ->ongoing()
            ->ordered()
            ->limit(10)
            ->get();

        if ($events->isEmpty()) {
            return '';
        }

        $safe = $events->map(fn ($e) => collect($e->getAttributes())
            ->except(array_merge(['id', 'created_at', 'updated_at', 'deleted_at', 'sort_order'], self::SENSITIVE_FIELDS_EXCLUDE))
            ->all())->values();

        return json_encode($safe, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Tin tức mới nhất đang hiển thị công khai — chỉ lấy các trường cần thiết để trả lời
     * (không cần content đầy đủ, quá dài và tốn token; excerpt là đủ để tóm tắt).
     */
    protected function latestNewsForPrompt(int $limit = 6): string
    {
        $news = News::query()
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['title', 'slug', 'excerpt', 'published_at']);

        if ($news->isEmpty()) {
            return '';
        }

        $safe = $news->map(fn ($n) => [
            'tiêu đề'      => $n->title,
            'slug'         => $n->slug,
            'tóm tắt'      => $n->excerpt,
            'ngày đăng'    => optional($n->published_at)->format('d/m/Y'),
        ])->values();

        return json_encode($safe, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Đơn hàng thật của khách ĐANG ĐĂNG NHẬP (không bao giờ trả đơn của người khác — luôn
     * lọc where('user_id', Auth::id())). Dump generic attribute (giống voucher) vì chưa biết
     * chắc toàn bộ cột của OrderItem — tránh đoán sai tên cột dẫn tới lỗi hoặc dữ liệu bịa.
     */
    protected function currentUserOrdersForPrompt(int $limit = 5): string
    {
        if (!Auth::check()) {
            return '';
        }

        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['items.product:id,name', 'voucher:id,code'])
            ->latest('created_at')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            return '';
        }

        $safe = $orders->map(function ($order) {
            $data = collect($order->getAttributes())
                ->except(array_merge(['user_id', 'address_id', 'voucher_id', 'deleted_at'], self::SENSITIVE_FIELDS_EXCLUDE))
                ->all();

            $data['created_at'] = optional($order->created_at)->format('d/m/Y H:i');
            $data['voucher_code'] = $order->voucher->code ?? null;

            $data['items'] = $order->items->map(function ($item) {
                $itemData = collect($item->getAttributes())
                    ->except(array_merge(['order_id', 'product_id', 'variant_id'], self::SENSITIVE_FIELDS_EXCLUDE))
                    ->all();
                $itemData['product_name'] = $item->product->name ?? '(sản phẩm không còn tồn tại)';
                return $itemData;
            })->values()->all();

            return $data;
        })->values();

        return json_encode($safe, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gọi Gemini lần 2: sinh câu trả lời tự nhiên dựa trên dữ liệu sản phẩm thật.
     */
    protected function generateAnswer(string $userMessage, array $history, array $intent, $products, ?array $compareTable): string
    {
        $context = $products->isEmpty()
            ? 'Không tìm thấy sản phẩm phù hợp trong cơ sở dữ liệu.'
            : $this->formatProductsForPrompt($products);

        $compareText = $compareTable
            ? "\n\nBảng thông số so sánh (JSON):\n" . json_encode($compareTable, JSON_UNESCAPED_UNICODE)
            : '';

        $compareTargetNote = ($intent['intent'] === 'compare' && ($intent['compare_target'] ?? null) === 'variants')
            ? "\nLưu ý: đây là so sánh giữa các PHIÊN BẢN/MÀU/DUNG LƯỢNG khác nhau của CÙNG MỘT sản phẩm, không phải so sánh giữa các sản phẩm khác nhau."
            : '';

        $consultNote = '';
        if ($intent['intent'] === 'consult') {
            $budget = $intent['consult_budget'] ?? null;
            $priority = $intent['consult_priority'] ?? null;

            if (!$budget) {
                $consultNote = "\nĐây là tư vấn theo nhu cầu nhưng khách CHƯA nêu ngân sách. Đừng liệt kê sản phẩm, "
                    . "hãy hỏi lịch sự khách cần điện thoại/sản phẩm khoảng bao nhiêu tiền.";
            } elseif (!$priority) {
                $consultNote = "\nĐây là tư vấn theo nhu cầu, khách đã nêu ngân sách khoảng " . number_format((float) $budget)
                    . "đ nhưng CHƯA nêu ưu tiên. Đừng liệt kê sản phẩm, hãy hỏi lịch sự khách ưu tiên điều gì nhất: "
                    . "chơi game, camera, pin, hiệu năng, màn hình, hay giá tốt — có thể gợi ý bằng emoji ngắn gọn.";
            } else {
                $priorityLabel = match ($priority) {
                    'gaming'      => 'chơi game (ưu tiên chip/CPU/GPU/RAM mạnh)',
                    'camera'      => 'chụp ảnh/quay phim (ưu tiên thông số camera nếu có trong dữ liệu)',
                    'battery'     => 'thời lượng pin (ưu tiên dung lượng pin lớn)',
                    'performance' => 'hiệu năng tổng thể mượt mà (ưu tiên chip/RAM)',
                    'display'     => 'màn hình đẹp (ưu tiên kích thước/loại màn hình)',
                    'value'       => 'giá tốt, tiết kiệm trong tầm ngân sách',
                    default       => $priority,
                };
                $consultNote = "\nĐây là tư vấn theo nhu cầu: ngân sách khoảng " . number_format((float) $budget)
                    . "đ, ưu tiên {$priorityLabel}. Từ \"Dữ liệu sản phẩm\" bên dưới, CHỌN RA 2-3 sản phẩm phù hợp"
                    . " nhất với ưu tiên đó (chỉ dựa vào thông số thực có trong dữ liệu, không suy đoán thêm) và"
                    . " giải thích ngắn gọn vì sao từng sản phẩm phù hợp. Nếu dữ liệu sản phẩm rỗng, báo là chưa"
                    . " tìm thấy sản phẩm phù hợp trong tầm giá này và gợi ý khách nới ngân sách.";
            }
        }

        $policyNote = '';
        if (in_array($intent['intent'], ['support', 'buy'])) {
            $policies = config('chatbot.policies', []);
            if (!empty($policies)) {
                $lines = collect($policies)->map(fn ($v, $k) => "- {$k}: {$v}")->implode("\n");
                $policyNote = "\n\nThông tin chính sách cửa hàng (do quản trị viên cấu hình, dùng để trả lời chính xác"
                    . " thay vì chung chung — CHỈ dùng đúng nội dung này, không bịa thêm):\n{$lines}";
            }

            $vouchersJson = $this->activeVouchersForPrompt();
            $policyNote .= $vouchersJson !== ''
                ? "\n\nVoucher/khuyến mãi đang khả dụng thật (JSON, chỉ dùng đúng dữ liệu này khi khách hỏi về"
                    . " khuyến mãi/giảm giá/mã giảm giá, không tự bịa thêm voucher khác):\n{$vouchersJson}"
                : "\n\nHiện không có voucher/khuyến mãi nào đang khả dụng trong hệ thống — nếu khách hỏi về"
                    . " khuyến mãi, trả lời thật là hiện chưa có chương trình nào, đừng bịa ra voucher.";

            $eventsJson = $this->ongoingEventsForPrompt();
            $policyNote .= $eventsJson !== ''
                ? "\n\nSự kiện/chương trình đang diễn ra thật (JSON, chỉ dùng đúng dữ liệu này khi khách hỏi về"
                    . " sự kiện/chương trình/deal đang diễn ra, không tự bịa thêm sự kiện khác):\n{$eventsJson}"
                : "\n\nHiện không có sự kiện/chương trình nào đang diễn ra trong hệ thống — nếu khách hỏi, trả lời"
                    . " thật là hiện chưa có sự kiện nào, đừng bịa ra.";
        }

        $newsNote = '';
        if ($intent['intent'] === 'news') {
            $newsJson = $this->latestNewsForPrompt();
            $newsNote = $newsJson !== ''
                ? "\n\nTin tức mới nhất thật trong hệ thống (JSON, CHỈ dùng đúng dữ liệu này khi khách hỏi về tin"
                    . " tức/bài viết, không tự bịa thêm bài viết khác. Có thể tóm tắt 3-5 tin nổi bật và gợi ý khách"
                    . " bấm vào xem chi tiết):\n{$newsJson}"
                : "\n\nHiện chưa có tin tức nào được đăng trong hệ thống — nếu khách hỏi về tin tức, trả lời thật"
                    . " là hiện chưa có bài viết nào, đừng bịa.";
        }

        $orderNote = '';
        if ($intent['intent'] === 'order') {
            if (!Auth::check()) {
                $orderNote = "\n\nKhách CHƯA đăng nhập nên hệ thống không thể tra cứu đơn hàng. Hãy lịch sự đề nghị"
                    . " khách đăng nhập vào tài khoản để xem đơn hàng của mình, KHÔNG được bịa ra bất kỳ thông tin"
                    . " đơn hàng nào.";
            } else {
                $ordersJson = $this->currentUserOrdersForPrompt();
                $orderNote = $ordersJson !== ''
                    ? "\n\nĐơn hàng thật của khách đang đăng nhập (JSON, CHỈ dùng đúng dữ liệu này để trả lời, tuyệt"
                        . " đối không bịa thêm đơn hàng khác hoặc đơn của người khác. Diễn giải trạng thái đơn hàng,"
                        . " sản phẩm, số lượng, tổng tiền một cách rõ ràng, dễ hiểu; nếu khách hỏi 1 đơn cụ thể mà có"
                        . " nhiều đơn trong danh sách, hỏi lại mã đơn nếu chưa rõ khách muốn hỏi đơn nào):\n{$ordersJson}"
                    : "\n\nKhách hiện chưa có đơn hàng nào trong hệ thống — nếu khách hỏi về đơn hàng, trả lời thật"
                        . " là chưa tìm thấy đơn hàng nào, đừng bịa.";
            }
        }

        $system = <<<PROMPT
Bạn là trợ lý tư vấn bán hàng cho một cửa hàng điện tử online tại Việt Nam, trả lời bằng tiếng Việt,
giọng văn thân thiện, ngắn gọn, đúng trọng tâm, không bịa thông tin sản phẩm hay giá ngoài dữ liệu được cung cấp.

Nguyên tắc:
- CHỈ nhắc tới sản phẩm có trong "Dữ liệu sản phẩm" bên dưới. Nếu danh sách rỗng, lịch sự báo không tìm thấy
  và gợi ý khách mô tả lại nhu cầu (loại sản phẩm, tầm giá, mục đích sử dụng).
- Nếu là so sánh, nêu rõ điểm khác biệt chính (giá, hiệu năng, pin, camera, màu sắc, dung lượng...) và đưa ra
  gợi ý phù hợp theo nhu cầu.{$compareTargetNote}{$consultNote}
- Một sản phẩm có thể có nhiều "phiên bản/biến thể" (vd dung lượng, màu sắc) với giá và tồn kho riêng,
  liệt kê trong mục "Các phiên bản/biến thể". Khi khách hỏi về dung lượng, màu sắc, các lựa chọn, hoặc giá
  của một phiên bản cụ thể, PHẢI dựa vào danh sách biến thể này để trả lời chính xác (không chỉ dùng giá gốc sản phẩm).
  Nếu khách hỏi 1 màu/phiên bản cụ thể mà không thấy trong danh sách biến thể của sản phẩm đó, trả lời thẳng là
  hiện chưa có phiên bản đó, đồng thời liệt kê các phiên bản đang có để khách chọn thay thế.
- Khi khách nói muốn mua (vd "cho tôi mua iPhone 16 128GB màu đen"), dựa vào "Dữ liệu sản phẩm" xác nhận lại
  CHÍNH XÁC tên sản phẩm + phiên bản (dung lượng/màu) + giá trước khi chốt. Nếu khách chưa nêu đủ dung
  lượng/màu và sản phẩm có nhiều phiên bản, hỏi khách chọn phiên bản nào trước, đừng tự chọn thay. Sau khi
  đã xác nhận đủ thông tin, mời khách bấm nút "Thêm vào giỏ hàng" trên trang sản phẩm hoặc trong khung chat
  để hoàn tất — bot chỉ tư vấn/xác nhận, không tự thực hiện thao tác đặt hàng.
- Nếu khách hỏi về chính sách/bảo hành/vận chuyển/đổi trả mà không có dữ liệu cụ thể, trả lời chung chung hợp lý
  và đề nghị khách liên hệ CSKH để biết chi tiết chính xác.
- Nếu khách báo lỗi kỹ thuật (không sạc được, máy nóng, không nhận SIM, wifi không kết nối, camera lỗi...),
  đưa ra 2-4 bước khắc phục cơ bản phổ biến (vd kiểm tra cáp/sạc khác, khởi động lại máy, vệ sinh khay SIM,
  quên rồi kết nối lại wifi...), sau đó đề nghị mang máy ra cửa hàng hoặc liên hệ CSKH nếu vẫn chưa xử lý được.
- Nếu khách hỏi về tin tức/bài viết, CHỈ dùng đúng dữ liệu tin tức thật được cung cấp, không bịa tiêu đề hay
  nội dung bài viết nào khác.
- Nếu khách hỏi về đơn hàng của họ, CHỈ dùng đúng dữ liệu đơn hàng thật được cung cấp (nếu có), không bịa mã đơn,
  trạng thái, sản phẩm hay số tiền. Nếu khách chưa đăng nhập, không được suy đoán hay bịa ra bất kỳ đơn hàng nào.
- Giá hiển thị dạng "xxx.xxx.xxx đ". Nếu có "giá niêm yết" cao hơn giá bán, có thể nhắc là sản phẩm đang giảm giá.
- Trả lời súc tích, có thể dùng gạch đầu dòng khi liệt kê nhiều sản phẩm hoặc nhiều phiên bản, không lan man.
PROMPT;

        $userPrompt = "Lịch sử hội thoại gần đây:\n" . $this->formatHistoryShort($history) .
            "\n\nÝ định nhận diện: {$intent['intent']}" .
            "\n\nDữ liệu sản phẩm:\n{$context}{$compareText}{$policyNote}{$newsNote}{$orderNote}" .
            "\n\nCâu hỏi của khách: \"{$userMessage}\"";

        return trim($this->callGemini($system, $userPrompt));
    }

    protected function formatProductsForPrompt($products): string
    {
        return $products->map(function ($p) {
            $specs = $p->attributes->map(fn ($a) => ($a->attribute->name ?? '') . ': ' . $a->value)->implode(', ');

            // Giá bán ("price") đã là giá thực bán; "list_price" (giá niêm yết) chỉ hiển thị
            // thêm khi đang cao hơn giá bán để thể hiện mức giảm giá — khớp với logic thật của
            // Product::getIsOnSaleAttribute() thay vì trường discount_percent không tồn tại.
            $base = "- {$p->name} | Danh mục: " . ($p->category->name ?? '-') . " | Hãng: " . ($p->brand->name ?? '-') .
                " | Giá: " . number_format((float) $p->price) . "đ" .
                ((float) $p->list_price > (float) $p->price
                    ? " (giá niêm yết " . number_format((float) $p->list_price) . "đ)"
                    : '') .
                " | Tồn kho: {$p->stock} | Thông số: {$specs}";

            if ($p->variants->isNotEmpty()) {
                $variantLines = $p->variants->map(function ($v) {
                    $vSpecs = $v->variantAttributes->map(fn ($a) => ($a->attribute->name ?? '') . ': ' . $a->value)->implode(', ');

                    return "    + {$v->label}: Giá " . number_format((float) $v->price) . "đ" .
                        ((float) $v->list_price > (float) $v->price
                            ? " (giá niêm yết " . number_format((float) $v->list_price) . "đ)"
                            : '') .
                        " | Tồn kho: {$v->stock}" .
                        ($vSpecs !== '' ? " | Thông số riêng: {$vSpecs}" : '');
                })->implode("\n");

                $base .= "\n  Các phiên bản/biến thể:\n{$variantLines}";
            }

            return $base;
        })->implode("\n");
    }

    protected function productSummary(Product $p): array
    {
        $isOnSale = (float) $p->list_price > (float) $p->price;
        $hasVariants = $p->relationLoaded('variants') && $p->variants->isNotEmpty();

        return [
            'id'               => $p->id,
            'name'             => $p->name,
            'slug'             => $p->slug,
            'price'            => (float) $p->price,
            'list_price'       => (float) $p->list_price,
            'is_on_sale'       => $isOnSale,
            'min_price'        => (float) ($hasVariants
                ? $p->variants->push($p)->min(fn ($item) => (float) $item->price)
                : $p->price),
            'has_price_range'  => $hasVariants,
            'thumbnail'        => $p->thumbnail,
            // Khi eager-load biến thể đã được thu hẹp đúng theo màu/dung lượng khách hỏi (xem
            // narrowVariantsToAttributes trong searchProducts), mảng này sẽ chỉ chứa (các) biến
            // thể khớp — front-end có thể dùng variant_id để gọi API "thêm vào giỏ" đúng phiên bản.
            'matched_variants' => $hasVariants
                ? $p->variants->map(fn ($v) => [
                    'variant_id' => $v->id,
                    'label'      => $v->label,
                    'price'      => (float) $v->price,
                    'stock'      => (int) $v->stock,
                ])->values()->all()
                : [],
        ];
    }

    protected function formatHistoryShort(array $history, int $maxTurns = 10): string
    {
        if (empty($history)) {
            return '(chưa có lịch sử)';
        }

        $slice = array_slice($history, -$maxTurns);

        return collect($slice)->map(fn ($m) => ($m['role'] === 'user' ? 'Khách' : 'Bot') . ': ' . $m['content'])->implode("\n");
    }

    protected function loadSession(string $sessionToken): AiSession
    {
        $session = AiSession::firstOrNew(['session_token' => $sessionToken]);

        if (!$session->exists) {
            $session->user_id = Auth::id();
            $session->messages = [];
            $session->search_keywords = [];
            $session->product_interactions = [];
            $session->total_messages = 0;
            $session->tokens_used = 0;

            if (Auth::check()) {
                $profile = CustomerAiProfile::where('user_id', Auth::id())->first();
                $session->profile_id = $profile?->id;
            }

            $session->save();
        }

        return $session;
    }

    protected function saveTurn(AiSession $session, string $userMessage, string $reply, array $intent, $products): void
    {
        $messages = $session->messages ?? [];
        $messages[] = ['role' => 'user', 'content' => $userMessage, 'at' => now()->toIso8601String()];
        $messages[] = ['role' => 'bot', 'content' => $reply, 'at' => now()->toIso8601String()];

        // CHỈ lưu keywords khi intent thực sự liên quan sản phẩm (search/buy/compare/consult).
        // Trước đây lưu vô điều kiện cho MỌI intent — nếu khách hỏi voucher/tin tức/đơn hàng xen
        // giữa, keywords của lượt đó (vd "mã giảm giá", "tin tức mới nhất") sẽ đè lên làm từ khóa
        // "gần nhất", khiến fallback ở handle() tìm nhầm khi khách quay lại hỏi sản phẩm mơ hồ.
        $productIntents = ['search', 'buy', 'compare', 'consult'];
        $keywords = $session->search_keywords ?? [];
        if (in_array($intent['intent'], $productIntents, true) && !empty($intent['keywords'])) {
            $keywords[] = $intent['keywords'];
            $keywords = array_slice(array_unique($keywords), -30);
        }

        $interactions = $session->product_interactions ?? [];
        foreach ($products as $p) {
            $interactions[] = ['product_id' => $p->id, 'intent' => $intent['intent'], 'at' => now()->toIso8601String()];
        }

        $session->fill([
            'intent_label'         => in_array($intent['intent'], ['search', 'compare', 'consult', 'support', 'buy', 'news', 'order']) ? $intent['intent'] : 'unknown',
            'messages'             => array_slice($messages, -60),
            'search_keywords'      => $keywords,
            'product_interactions' => array_slice($interactions, -50),
            'total_messages'       => $session->total_messages + 2,
            'tokens_used'          => $session->tokens_used + (int) (mb_strlen($userMessage . $reply) / 4),
        ])->save();
    }
}