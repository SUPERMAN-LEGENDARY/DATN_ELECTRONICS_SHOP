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

    /**
     * Số biến thể tối đa hiển thị cho MỖI sản phẩm trong prompt khi KHÔNG lọc được theo
     * attribute cụ thể khách hỏi (vd khách hỏi chung chung, không nêu màu/dung lượng).
     * Tránh nhồi hết 10-15+ biến thể vào prompt gây tốn token vô ích khi khách chưa cần
     * xem hết — ưu tiên hiển thị các biến thể còn hàng và đa dạng mức giá.
     */
    protected const MAX_VARIANTS_IN_PROMPT = 6;

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

        $looksLikeProductQuery = trim((string) ($intent['keywords'] ?? '')) !== ''
            || !empty($intent['compare_names'])
            || !empty($intent['demand'])
            || !empty($intent['consult_priority'])
            || !empty($intent['category'])
            || !empty($intent['brand'])
            || !empty($intent['attributes']);

        $shouldSearchProducts = in_array($intent['intent'], ['search', 'buy', 'consult'])
            || ($looksLikeProductQuery && in_array($intent['intent'], ['support', 'unknown']));

        if ($intent['intent'] === 'compare') {
            if (($intent['compare_target'] ?? 'products') === 'variants') {
                // So sánh các phiên bản/màu/dung lượng... của CÙNG 1 sản phẩm
                $baseIntent = $intent;
                $baseIntent['attributes'] = []; // không lọc, cần lấy đủ variant để so sánh
                $targetName = $intent['compare_names'][0] ?? $intent['keywords'] ?? '';

                $matched = collect();
                if ($targetName !== '') {
                    $found = $this->searchProductByNameTokens($targetName);
                    if ($found) {
                        $matched->push($found);
                    }
                }

                if ($matched->isEmpty()) {
                    $baseIntent['compare_names'] = array_slice($intent['compare_names'] ?? [], 0, 1);
                    $matched = $this->searchProducts($baseIntent, limit: 1, narrowVariantsToAttributes: false);
                }

                if ($matched->isEmpty() && !empty($history)) {
                    $matched = $this->recentDistinctProducts($session, 1);
                }

                $products = $matched;
                $compareTable = $matched->isNotEmpty()
                    ? $this->buildVariantCompareTable($matched->first(), $intent['compare_names'] ?? [])
                    : null;
            } elseif (empty($intent['compare_names'])) {
                // Khách so sánh kiểu "máy nào chơi game/camera/pin tốt hơn" mà không nêu lại tên máy
                // -> lấy 2-3 sản phẩm vừa được nhắc tới gần nhất trong phiên chat để so sánh.
                $products = $this->recentDistinctProducts($session, 2);
                $compareTable = $products->isNotEmpty() ? $this->buildCompareTable($products) : null;
            } else {
                // So sánh các sản phẩm cụ thể (cùng thương hiệu hoặc KHÁC thương hiệu)
                $matchedProducts = collect();
                foreach ($intent['compare_names'] as $name) {
                    $found = $this->searchProductByNameTokens($name);
                    if ($found) {
                        $matchedProducts->push($found);
                    }
                }

                // Nếu chưa tìm đủ hết các tên so sánh, thử tìm thêm qua searchProducts chung
                if ($matchedProducts->count() < count($intent['compare_names'])) {
                    $extraIntent = $intent;
                    $extraIntent['brand'] = null; // không khóa brand khi so sánh đa thương hiệu
                    $generalFound = $this->searchProducts($extraIntent, limit: 6, narrowVariantsToAttributes: false);
                    $matchedProducts = $matchedProducts->merge($generalFound)->unique('id')->values();
                }

                // Vẫn không ra sản phẩm nào -> lấy các sản phẩm vừa nhắc tới gần nhất trong phiên chat
                if ($matchedProducts->isEmpty() && !empty($history)) {
                    $matchedProducts = $this->recentDistinctProducts($session, 2);
                }

                $products = $matchedProducts;
                $compareTable = $products->isNotEmpty() ? $this->buildCompareTable($products) : null;
            }
        } elseif ($shouldSearchProducts) {
            $products = $this->searchProducts($intent, narrowVariantsToAttributes: empty($intent['demand']));

            // Fallback 1: nếu không ra kết quả và đây có vẻ là câu hỏi nối tiếp, thử lại bằng từ khóa gần nhất
            if ($products->isEmpty() && !empty($history)) {
                $lastKeyword = collect($session->search_keywords ?? [])->last();
                if ($lastKeyword && trim((string) ($intent['keywords'] ?? '')) !== $lastKeyword) {
                    $fallbackIntent = $intent;
                    $fallbackIntent['keywords'] = $lastKeyword;
                    $fallbackIntent['compare_names'] = [];
                    $products = $this->searchProducts($fallbackIntent);
                }
            }

            // Fallback 2: nới lỏng bỏ thuộc tính để AI còn dữ liệu gốc giải thích
            if ($products->isEmpty() && !empty($intent['attributes'])) {
                $relaxedIntent = $intent;
                $relaxedIntent['attributes'] = [];
                $products = $this->searchProducts($relaxedIntent, narrowVariantsToAttributes: false);
            }

            // Fallback 3: bỏ category + brand nếu AI đoán sai slug
            if ($products->isEmpty() && (!empty($intent['category']) || !empty($intent['brand']))) {
                $relaxedIntent2 = $intent;
                $relaxedIntent2['category']   = null;
                $relaxedIntent2['brand']      = null;
                $relaxedIntent2['attributes'] = [];
                $products = $this->searchProducts($relaxedIntent2, narrowVariantsToAttributes: false);
            }

            // Fallback 4: lấy sản phẩm nhắc tới gần nhất trong phiên chat
            if ($products->isEmpty() && !empty($history)) {
                $products = $this->recentDistinctProducts($session, 2);
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
  "keywords": string,            // tên sản phẩm/dòng máy cụ thể (vd "iPhone 16", "S24 Ultra", "MacBook Air").
                                  // TUYỆT ĐỐI KHÔNG chứa các từ chỉ nhu cầu chung chung như "chơi game", "chụp ảnh", "pin trâu", "học tập", "văn phòng", "giá rẻ" trong keywords.
  "category": string|null,       // CHỈ dùng đúng slug có trong danh sách category thật sau: {$categoryList}
                                  // Nếu không có slug nào khớp rõ ràng, để null.
  "brand": string|null,          // CHỈ dùng đúng slug có trong danh sách brand thật sau: {$brandList}
                                  // Nếu đang so sánh giữa nhiều hãng khác nhau (vd Apple vs Samsung), ĐỂ NULL để không loại bỏ sản phẩm của hãng kia.
  "price_min": number|null,
  "price_max": number|null,
  "sort": "price_asc" | "price_desc" | null,  // "rẻ nhất"/"giá thấp nhất" -> price_asc; "đắt nhất"/"giá cao nhất" -> price_desc
  "attributes": object,          // cặp thuộc tính kỹ thuật khách nêu ra, CHỈ dùng đúng tên trong danh sách:
                                  // {$attributeList}
                                  // vd {"RAM":"8GB","Màu sắc":"đen","Bộ nhớ trong":"256GB"}. Rỗng {} nếu khách không nêu thuộc tính cụ thể.
  "demand": "gaming" | "camera" | "battery" | "office_study" | "performance" | "graphic" | "value" | null,
                                  // Nhu cầu/mục đích sử dụng của khách:
                                  // "gaming": chơi game, đồ họa game nặng, fps cao, liên quân, pubg, genshin
                                  // "camera": chụp ảnh đẹp, quay phim, camera sắc nét, selfie, sống ảo
                                  // "battery": pin trâu, pin khủng, pin dùng lâu, pin bền
                                  // "office_study": học tập, sinh viên, văn phòng, mỏng nhẹ, làm việc
                                  // "performance": cấu hình mạnh, hiệu năng cao, chip mạnh, mượt mà
                                  // "graphic": đồ họa, chỉnh sửa ảnh/video, render
                                  // "value": giá rẻ, tiết kiệm, bình dân, ngon bổ rẻ
  "compare_target": "products" | "variants" | null,
                                  // "products" khi so sánh 2+ sản phẩm KHÁC tên/dòng với nhau (kể cả cùng hãng hay khác hãng);
                                  // "variants" khi so sánh các PHIÊN BẢN/MÀU/DUNG LƯỢNG khác nhau của CÙNG MỘT sản phẩm (vd "so sánh bản 256GB và 512GB của iPhone 16", "iPhone 15 có các màu nào khác nhau gì");
                                  // null nếu không phải compare.
  "compare_names": string[],     // nếu compare_target=products: mảng chứa tên/model từng sản phẩm muốn so sánh (vd ["iPhone 16 Pro Max", "Samsung Galaxy S24 Ultra"]).
                                  // nếu compare_target=variants: [0]=tên sản phẩm, các phần tử sau là nhãn phiên bản/màu/dung lượng muốn so sánh (vd ["iPhone 16", "128GB", "256GB"]).
                                  // Nếu khách so sánh kiểu "máy nào chơi game tốt hơn" mà KHÔNG nêu lại tên máy, để compare_names=[].
  "consult_budget": number|null, // ngân sách khách nêu (vd "khoảng 10 triệu" -> 10000000)
  "consult_priority": "gaming" | "camera" | "battery" | "performance" | "display" | "value" | null
}

Quy tắc:
- "so sánh", "vs", "khác nhau", "máy nào ... hơn" -> intent = compare.
- hỏi giá, tìm, gợi ý, "có ... không", "có màu gì", "có bản nào", "tư vấn điện thoại chơi game/chụp ảnh" -> intent = search hoặc consult.
- Nếu khách nêu mục đích sử dụng (chơi game, chụp ảnh, pin trâu, văn phòng...), LUÔN điền trường "demand", và LOẠI BỎ cụm từ chỉ nhu cầu đó ra khỏi "keywords".
- So sánh giữa 2 thương hiệu khác nhau (vd "so sánh iPhone với Samsung", "iPhone 15 vs S24 Ultra"): compare_target="products", compare_names=["iPhone 15", "Samsung S24 Ultra"], brand=null.
- Khoảng giá: "dưới 10 triệu" -> price_max=10000000; "trên 20 triệu" -> price_min=20000000; "15-20 triệu" -> price_min=15000000, price_max=20000000; "khoảng 5 triệu" -> price_min=4000000, price_max=6000000.
- Thuộc tính biến thể (Màu sắc, Dung lượng/Bộ nhớ trong, RAM) tách vào "attributes", CHỈ dùng đúng tên có trong danh sách thuộc tính cho phép.
PROMPT;

        $userPrompt = "Lịch sử gần đây:\n{$historyText}\n\nTin nhắn mới của khách: \"{$userMessage}\"";

        $raw = $this->callGemini($system, $userPrompt, jsonMode: true);

        $parsed = json_decode($this->stripJsonFence($raw), true);

        $defaults = [
            'intent' => 'unknown', 'keywords' => $userMessage, 'category' => null,
            'brand' => null, 'price_min' => null, 'price_max' => null, 'sort' => null,
            'attributes' => [], 'demand' => null, 'compare_target' => null, 'compare_names' => [],
            'consult_budget' => null, 'consult_priority' => null,
        ];

        if (!is_array($parsed)) {
            return $defaults;
        }

        $result = array_merge($defaults, $parsed);
        $result['attributes'] = is_array($result['attributes'] ?? null)
            ? array_filter($result['attributes'], fn ($v) => $v !== null && $v !== '')
            : [];

        // Đồng bộ demand và consult_priority
        if (empty($result['demand']) && !empty($result['consult_priority'])) {
            $result['demand'] = $result['consult_priority'];
        } elseif (!empty($result['demand']) && empty($result['consult_priority'])) {
            $result['consult_priority'] = $result['demand'];
        }

        // An toàn: kiểm tra category/brand hợp lệ
        if (!empty($result['category']) && !in_array($result['category'], $categorySlugs, true)) {
            $result['category'] = null;
        }
        if (!empty($result['brand']) && !in_array($result['brand'], $brandSlugs, true)) {
            $result['brand'] = null;
        }

        return $result;
    }

    /**
     * Tách và làm sạch từ khóa tìm kiếm: loại bỏ từ chỉ danh mục/nhu cầu chung chung
     * để không làm gãy truy vấn SQL LIKE trên tên sản phẩm.
     */
    protected function cleanSearchKeywords(string $keywords): array
    {
        $keywords = trim($keywords);
        if ($keywords === '') {
            return ['clean' => '', 'category_hint' => null, 'demand_hint' => null];
        }

        $categoryHint = null;
        $demandHint = null;

        // Nhận diện category hint
        if (preg_match('/(điện thoại|smartphone|đt|iphone|galaxy)/ui', $keywords)) {
            $categoryHint = 'dien-thoai';
        } elseif (preg_match('/(laptop|máy tính xách tay|macbook)/ui', $keywords)) {
            $categoryHint = 'laptop';
        } elseif (preg_match('/(tai nghe|headphone|earphone|airpods)/ui', $keywords)) {
            $categoryHint = 'tai-nghe';
        } elseif (preg_match('/(máy tính bảng|tablet|ipad)/ui', $keywords)) {
            $categoryHint = 'may-tinh-bang';
        }

        // Nhận diện demand hint
        if (preg_match('/(chơi game|gaming|game|liên quân|pubg|genshin)/ui', $keywords)) {
            $demandHint = 'gaming';
        } elseif (preg_match('/(chụp ảnh|chụp hình|camera|quay phim|quay video|selfie)/ui', $keywords)) {
            $demandHint = 'camera';
        } elseif (preg_match('/(pin trâu|pin khủng|pin lớn|pin lâu|pin khỏe)/ui', $keywords)) {
            $demandHint = 'battery';
        } elseif (preg_match('/(văn phòng|học tập|sinh viên|học sinh|mỏng nhẹ|gọn nhẹ)/ui', $keywords)) {
            $demandHint = 'office_study';
        } elseif (preg_match('/(hiệu năng|cấu hình mạnh|mượt mà|chip mạnh)/ui', $keywords)) {
            $demandHint = 'performance';
        } elseif (preg_match('/(giá rẻ|tiết kiệm|rẻ nhất|bình dân)/ui', $keywords)) {
            $demandHint = 'value';
        }

        // Loại bỏ các từ dừng (stopwords)
        $stripPatterns = [
            '/\b(điện thoại|smartphone|máy tính xách tay|laptop|tai nghe|máy tính bảng|tablet)\b/ui',
            '/\b(chơi game|gaming|game|liên quân|pubg|genshin)\b/ui',
            '/\b(chụp ảnh|chụp hình|camera đẹp|quay phim|quay video|selfie đẹp|sống ảo)\b/ui',
            '/\b(pin trâu|pin khủng|pin lớn|pin lâu|pin khỏe|dung lượng pin)\b/ui',
            '/\b(văn phòng|học tập|sinh viên|học sinh|mỏng nhẹ|gọn nhẹ)\b/ui',
            '/\b(cấu hình mạnh|hiệu năng cao|mượt mà|chip mạnh|ram khủng)\b/ui',
            '/\b(giá rẻ|tiết kiệm|rẻ nhất|bình dân|ngon bổ rẻ)\b/ui',
            '/\b(tư vấn|tìm|mua|xem|cần|cho mình|giúp mình|có|bán)\b/ui',
        ];

        $clean = $keywords;
        foreach ($stripPatterns as $pattern) {
            $clean = preg_replace($pattern, ' ', $clean);
        }
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        return [
            'clean'         => $clean,
            'category_hint' => $categoryHint,
            'demand_hint'   => $demandHint,
        ];
    }

    /**
     * Tìm 1 sản phẩm cụ thể theo chuỗi tên hoặc các token từ khóa (dùng cho compare và direct lookup).
     */
    protected function searchProductByNameTokens(string $name): ?Product
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        // 1. Khớp chính xác hoặc chuỗi con liên tục
        $exact = Product::query()
            ->where('is_active', 1)
            ->where('name', 'like', '%' . $name . '%')
            ->with([
                'category', 'brand', 'attributes.attribute',
                'variants' => fn ($q) => $q->where('is_active', 1)->with('variantAttributes.attribute'),
            ])
            ->first();

        if ($exact) {
            return $exact;
        }

        // 2. Tách từ khóa token (bỏ từ phụ)
        $cleanName = preg_replace('/(điện thoại|laptop|máy tính|tai nghe|bản|phiên bản)/ui', '', $name);
        $terms = array_values(array_filter(preg_split('/\s+/', trim($cleanName))));
        if (empty($terms)) {
            $terms = array_values(array_filter(preg_split('/\s+/', $name)));
        }

        if (empty($terms)) {
            return null;
        }

        // Tìm sản phẩm mà tên chứa TẤT CẢ các token từ khóa (AND)
        $query = Product::query()
            ->where('is_active', 1)
            ->with([
                'category', 'brand', 'attributes.attribute',
                'variants' => fn ($q) => $q->where('is_active', 1)->with('variantAttributes.attribute'),
            ]);

        $query->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                if (mb_strlen($term) >= 2) {
                    $q->where('name', 'like', '%' . $term . '%');
                }
            }
        });

        $result = $query->first();
        if ($result) {
            return $result;
        }

        // 3. Fallback: tìm trong tên hoặc nhãn biến thể
        return Product::query()
            ->where('is_active', 1)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    if (mb_strlen($term) >= 3) {
                        $q->orWhere('name', 'like', '%' . $term . '%')
                          ->orWhereHas('variants', fn ($qv) => $qv->where('label', 'like', '%' . $term . '%'));
                    }
                }
            })
            ->with([
                'category', 'brand', 'attributes.attribute',
                'variants' => fn ($q) => $q->where('is_active', 1)->with('variantAttributes.attribute'),
            ])
            ->first();
    }

    /**
     * Tìm sản phẩm thật trong DB dựa trên bộ lọc đã trích xuất.
     */
    protected function searchProducts(array $intent, int $limit = 8, bool $narrowVariantsToAttributes = true)
    {
        $resolvedAttrs = $this->resolveAttributeFilters($intent['attributes'] ?? []);
        $rawKeywords   = trim((string) ($intent['keywords'] ?? ''));
        $parsed        = $this->cleanSearchKeywords($rawKeywords);
        $cleanKeywords = $parsed['clean'];

        $categorySlug = $intent['category'] ?? $parsed['category_hint'];
        $demand       = $intent['demand'] ?? $intent['consult_priority'] ?? $parsed['demand_hint'];

        $query = Product::query()
            ->where('is_active', 1)
            ->with([
                'category',
                'brand',
                'attributes.attribute',
                'variants' => function ($q) use ($resolvedAttrs, $narrowVariantsToAttributes) {
                    $q->where('is_active', 1)->with('variantAttributes.attribute');

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

        $names = $intent['compare_names'] ?? [];

        if (!empty($names)) {
            $query->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $nameTerms = array_values(array_filter(preg_split('/\s+/', trim($name))));
                    if (count($nameTerms) > 1) {
                        $q->orWhere(function ($qSub) use ($nameTerms) {
                            foreach ($nameTerms as $term) {
                                if (mb_strlen($term) >= 2) {
                                    $qSub->where('name', 'like', '%' . $term . '%');
                                }
                            }
                        });
                    } else {
                        $q->orWhere('name', 'like', '%' . $name . '%');
                    }
                }
            });
        } elseif ($cleanKeywords !== '') {
            $terms = array_values(array_filter(preg_split('/\s+/', $cleanKeywords)));
            $query->where(function ($q) use ($terms, $cleanKeywords) {
                $q->where('name', 'like', '%' . $cleanKeywords . '%')
                  ->orWhere('description', 'like', '%' . $cleanKeywords . '%')
                  ->orWhereHas('variants', fn ($qv) => $qv->where('label', 'like', '%' . $cleanKeywords . '%'));

                if (count($terms) > 0) {
                    $q->orWhere(function ($qAll) use ($terms) {
                        foreach ($terms as $term) {
                            if (mb_strlen($term) >= 2) {
                                $qAll->where(function ($qTerm) use ($term) {
                                    $qTerm->where('name', 'like', '%' . $term . '%')
                                          ->orWhere('description', 'like', '%' . $term . '%')
                                          ->orWhereHas('variants', function ($qv) use ($term) {
                                              $qv->where('is_active', 1)
                                                 ->where(function ($qqv) use ($term) {
                                                     $qqv->where('label', 'like', '%' . $term . '%')
                                                         ->orWhereHas('variantAttributes', function ($qva) use ($term) {
                                                             $qva->where('value', 'like', '%' . $term . '%');
                                                         });
                                                 });
                                          })
                                          ->orWhereHas('attributes', function ($qa) use ($term) {
                                              $qa->where('value', 'like', '%' . $term . '%');
                                          });
                                });
                            }
                        }
                    });
                }
            });
        }

        if (empty($names)) {
            if (!empty($categorySlug)) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            }
            if (!empty($intent['brand'])) {
                $query->whereHas('brand', fn ($q) => $q->where('slug', $intent['brand']));
            }
        }

        // Khoảng giá
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

        // Lọc theo thuộc tính kỹ thuật
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

        // Sắp xếp
        $sort = $intent['sort'] ?? null;
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($demand === 'value') {
            $query->orderBy('price', 'asc');
        } elseif (in_array($demand, ['gaming', 'performance', 'camera'])) {
            $query->orderBy('price', 'desc');
        } else {
            $query->orderByDesc('id');
        }

        return $query->limit($limit)->get();
    }

    /**
     * Lấy N sản phẩm khác nhau vừa được nhắc tới gần đây nhất trong phiên chat.
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

        return $productIds->map(fn ($id) => $products->firstWhere('id', $id))->filter()->values();
    }

    /**
     * Ánh xạ tên thuộc tính sang attribute_id thật trong DB.
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
                continue;
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
     * Dựng bảng so sánh thông số kỹ thuật giữa các SẢN PHẨM KHÁC NHAU (cùng hoặc khác thương hiệu).
     */
    protected function buildCompareTable($products): array
    {
        $table = [];

        foreach ($products as $product) {
            $prices = $product->variants->isNotEmpty()
                ? $product->variants->pluck('price')->push($product->price)
                : collect([$product->price]);

            $row = [
                'tên sản phẩm' => $product->name,
                'thương hiệu'  => $product->brand->name ?? '-',
                'danh mục'     => $product->category->name ?? '-',
                'khoảng giá'   => $product->variants->isNotEmpty()
                    ? number_format((float) $prices->min()) . 'đ - ' . number_format((float) $prices->max()) . 'đ'
                    : number_format((float) $product->price) . 'đ',
                'tồn kho'      => (int) $product->stock,
            ];

            foreach ($product->attributes as $attr) {
                $attrName = $attr->attribute->name ?? null;
                if ($attrName) {
                    $row[$attrName] = $attr->value;
                }
            }

            if ($product->variants->isNotEmpty()) {
                $row['các phiên bản'] = $product->variants->map(fn ($v) => $v->label . ' (' . number_format((float) $v->price) . 'đ)')->implode(', ');
            }

            $table[] = $row;
        }

        return $table;
    }

    /**
     * Dựng bảng so sánh các PHIÊN BẢN/MÀU/DUNG LƯỢNG khác nhau của CÙNG 1 sản phẩm.
     */
    protected function buildVariantCompareTable(Product $product, array $labelFilter = []): ?array
    {
        $variants = $product->variants;

        $wantedLabels = array_slice($labelFilter, 1);
        if (!empty($wantedLabels)) {
            $variants = $variants->filter(function ($v) use ($wantedLabels) {
                foreach ($wantedLabels as $label) {
                    if (Str::contains(Str::lower($v->label), Str::lower($label))) {
                        return true;
                    }
                }
                return false;
            });

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
                'sản phẩm'      => $product->name,
                'thương hiệu'   => $product->brand->name ?? '-',
                'phiên bản'     => $variant->label,
                'giá bán'       => number_format((float) $variant->price) . 'đ',
                'tồn kho'       => (int) $variant->stock,
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

            foreach ($product->attributes as $attr) {
                $attrName = $attr->attribute->name ?? null;
                if ($attrName && !isset($row[$attrName])) {
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

        $compareTargetNote = '';
        if ($intent['intent'] === 'compare') {
            if (($intent['compare_target'] ?? null) === 'variants') {
                $compareTargetNote = "\nLưu ý: đây là so sánh giữa các PHIÊN BẢN/MÀU/DUNG LƯỢNG/RAM khác nhau của CÙNG MỘT sản phẩm. Hãy làm nổi bật sự khác nhau về giá bán, dung lượng bộ nhớ, RAM, màu sắc và gợi ý phiên bản đáng mua nhất theo nhu cầu.";
            } else {
                $compareTargetNote = "\nLưu ý: đây là so sánh giữa các SẢN PHẨM KHÁC NHAU (kể cả cùng thương hiệu hoặc KHÁC THƯƠNG HIỆU như Apple vs Samsung, Oppo vs Vivo...). Hãy so sánh đa chiều: Thiết kế & Màn hình, Hiệu năng & Chip xử lý (chơi game, tác vụ), Camera, Pin & Sạc, Hệ điều hành và Khoảng giá. Cuối cùng, đưa ra kết luận rõ ràng: 'Nên chọn máy A nếu bạn cần..., nên chọn máy B nếu bạn thích...'.";
            }
        }

        $demandNote = '';
        $demand = $intent['demand'] ?? $intent['consult_priority'] ?? null;
        if ($demand) {
            $demandLabel = match ($demand) {
                'gaming'       => 'chơi game (ưu tiên chip CPU, GPU, RAM mạnh, màn hình tần số quét cao 120Hz/90Hz, pin)',
                'camera'       => 'chụp ảnh và quay video (ưu tiên cảm biến camera, độ sắc nét, màn hình hiển thị đẹp)',
                'battery'      => 'thời lượng pin (ưu tiên dung lượng pin lớn từ 5000mAh trở lên)',
                'office_study' => 'học tập, sinh viên, văn phòng, mỏng nhẹ, pin tốt',
                'performance'  => 'hiệu năng tổng thể mượt mà, chip mạnh mẽ, đa nhiệm mượt',
                'graphic'      => 'đồ họa, thiết kế, xử lý hình ảnh/video',
                'value'        => 'giá rẻ, tối ưu chi phí, ngon bổ rẻ',
                default        => $demand,
            };

            $demandNote = "\nKhách hàng đang có nhu cầu: {$demandLabel}."
                . " Dựa vào 'Dữ liệu sản phẩm' thật được cung cấp, hãy phân tích chi tiết 2-3 sản phẩm phù hợp nhất với nhu cầu này, nêu rõ thông số chip, RAM, pin, camera thực tế để thuyết phục khách."
                . " Nếu khách chưa nêu ngân sách, có thể nêu mức giá cụ thể của từng gợi ý và lịch sự hỏi xem khách muốn tầm giá khoảng bao nhiêu để lọc chuẩn hơn.";
        }

        $consultNote = '';
        if ($intent['intent'] === 'consult' && empty($demand)) {
            $budget = $intent['consult_budget'] ?? null;
            if ($budget) {
                $consultNote = "\nKhách đã nêu ngân sách khoảng " . number_format((float) $budget) . "đ. Hãy giới thiệu 2-3 sản phẩm nổi bật nhất trong tầm giá này và hỏi khách ưu tiên chơi game, chụp ảnh, pin trâu hay làm việc để tư vấn sâu hơn.";
            } else {
                $consultNote = "\nKhách cần tư vấn chung. Hãy giới thiệu 2-3 sản phẩm bán chạy/tiêu biểu và gợi ý khách cho biết thêm ngân sách hoặc nhu cầu chính (chơi game, chụp ảnh, làm việc, pin trâu) để tư vấn chính xác nhất.";
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
                    . " thật là hiện chưa có sự kiện nào, đừng bịa.";
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
Bạn là trợ lý tư vấn bán hàng chuyên nghiệp, am hiểu công nghệ cho một cửa hàng điện tử online tại Việt Nam. Trả lời bằng tiếng Việt, giọng văn thân thiện, ngắn gọn, đúng trọng tâm, tôn trọng dữ liệu thực tế và không bịa thông tin hay giá bán ngoài dữ liệu được cung cấp.

Nguyên tắc:
- CHỈ nhắc tới các sản phẩm có trong "Dữ liệu sản phẩm" bên dưới. Nếu danh sách rỗng, lịch sự thông báo hiện cửa hàng chưa có sản phẩm khớp hoàn toàn và gợi ý khách xem các dòng sản phẩm liên quan.
- Khi tư vấn theo nhu cầu (chơi game, chụp ảnh, pin trâu, văn phòng...), phân tích rõ ràng vì sao từng máy phù hợp dựa trên thông số thực tế (Chip CPU/GPU, RAM, Pin mAh, Màn hình Hz/tấm nền...).
- Khi so sánh sản phẩm (cùng hoặc khác thương hiệu), so sánh khách quan các yếu tố cốt lõi (Màn hình, Hiệu năng & Chip, Camera, Pin, Hệ điều hành, Giá bán) và đưa ra lời khuyên lựa chọn phù hợp.{$compareTargetNote}{$demandNote}{$consultNote}
- Một sản phẩm có thể có nhiều "phiên bản/biến thể" (dung lượng bộ nhớ, màu sắc, RAM) với giá và tồn kho riêng. Khi khách hỏi về phiên bản/màu/dung lượng, PHẢI dựa vào danh sách biến thể để trả lời chi tiết và chính xác. Nếu ghi chú "(còn N phiên bản khác, hỏi cụ thể màu/dung lượng để xem đầy đủ)" xuất hiện sau danh sách biến thể của 1 sản phẩm, nghĩa là danh sách chỉ hiển thị một phần — nếu khách hỏi cụ thể về phiên bản không có trong danh sách, hãy lịch sự đề nghị khách nêu rõ màu/dung lượng để tra cứu chính xác thay vì đoán bừa.
- Khi khách nói muốn mua, dựa vào "Dữ liệu sản phẩm" xác nhận lại chính xác tên máy, phiên bản, giá và mời khách bấm nút "Thêm vào giỏ hàng" hoặc xem chi tiết sản phẩm.
- Giá hiển thị dạng "xxx.xxx.xxx đ". Sử dụng gạch đầu dòng rõ ràng, mạch lạc, dễ đọc trên điện thoại và máy tính.
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
                $allVariants = $p->variants;
                $totalVariants = $allVariants->count();

                // Cap số biến thể đưa vào prompt khi danh sách chưa được lọc gọn theo attribute
                // cụ thể (vd khách hỏi chung chung "có màu gì" mà không nêu rõ) — tránh nhồi hết
                // 10-15+ biến thể tốn token vô ích. Ưu tiên hiển thị biến thể còn hàng trước,
                // rồi tới biến thể trải đều theo giá (thấp -> cao) để khách vẫn thấy được khoảng giá.
                if ($totalVariants > self::MAX_VARIANTS_IN_PROMPT) {
                    $displayVariants = $allVariants
                        ->sortByDesc(fn ($v) => (int) $v->stock > 0 ? 1 : 0)
                        ->sortBy('price')
                        ->take(self::MAX_VARIANTS_IN_PROMPT)
                        ->values();
                } else {
                    $displayVariants = $allVariants;
                }

                $variantLines = $displayVariants->map(function ($v) {
                    $vSpecs = $v->variantAttributes->map(fn ($a) => ($a->attribute->name ?? '') . ': ' . $a->value)->implode(', ');

                    return "    + {$v->label}: Giá " . number_format((float) $v->price) . "đ" .
                        ((float) $v->list_price > (float) $v->price
                            ? " (giá niêm yết " . number_format((float) $v->list_price) . "đ)"
                            : '') .
                        " | Tồn kho: {$v->stock}" .
                        ($vSpecs !== '' ? " | Thông số riêng: {$vSpecs}" : '');
                })->implode("\n");

                $base .= "\n  Các phiên bản/biến thể:\n{$variantLines}";

                if ($totalVariants > self::MAX_VARIANTS_IN_PROMPT) {
                    $remaining = $totalVariants - self::MAX_VARIANTS_IN_PROMPT;
                    $base .= "\n    (còn {$remaining} phiên bản khác, hỏi cụ thể màu/dung lượng để xem đầy đủ)";
                }
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
            // LƯU Ý: mảng này KHÔNG áp dụng cap MAX_VARIANTS_IN_PROMPT (đó chỉ là cap riêng cho
            // text gửi vào AI, front-end vẫn nhận đủ toàn bộ biến thể để hiển thị/chọn mua đúng).
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

    /**
     * Lấy tối đa $maxTurns lượt hội thoại GẦN NHẤT để đưa vào prompt. Giảm từ 10 xuống 4 —
     * đủ để AI hiểu ngữ cảnh câu hỏi nối tiếp gần nhất (vd "còn màu khác không", "loại rẻ hơn
     * thì sao") mà không tốn token cho các lượt đã quá xa, thường không còn liên quan trực
     * tiếp tới câu hỏi hiện tại. Nếu sau này thấy AI "quên" ngữ cảnh quá nhanh trong hội thoại
     * dài, có thể tăng lại (đánh đổi token).
     */
    protected function formatHistoryShort(array $history, int $maxTurns = 4): string
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

        $dbIntentLabel = match ($intent['intent']) {
            'search', 'consult'        => 'search',
            'compare'                  => 'compare',
            'buy'                      => 'buy',
            'support', 'news', 'order' => 'support',
            default                    => 'unknown',
        };

        $session->fill([
            'intent_label'         => $dbIntentLabel,
            'messages'             => array_slice($messages, -60),
            'search_keywords'      => $keywords,
            'product_interactions' => array_slice($interactions, -50),
            'total_messages'       => $session->total_messages + 2,
            'tokens_used'          => $session->tokens_used + (int) (mb_strlen($userMessage . $reply) / 4),
        ])->save();
    }
}