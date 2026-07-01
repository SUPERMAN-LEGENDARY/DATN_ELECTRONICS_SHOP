<?php

namespace App\Services;

use App\Models\AiSession;
use App\Models\CustomerAiProfile;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiChatService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.key');
        $this->model    = config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Điểm vào chính: xử lý 1 tin nhắn của khách và trả về phản hồi.
     */
    public function handle(string $sessionToken, string $userMessage): array
    {
        $session = $this->loadSession($sessionToken);
        $history = $session->messages ?? [];

        // Bước 1: dùng Gemini để hiểu ý định + trích từ khóa tìm kiếm có cấu trúc
        $intent = $this->extractIntent($userMessage, $history);

        // Bước 2: tùy ý định, truy vấn dữ liệu sản phẩm thật trong DB
        $products = collect();
        $compareTable = null;

        if (in_array($intent['intent'], ['search', 'buy'])) {
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
        } elseif ($intent['intent'] === 'compare') {
            $products = $this->searchProducts($intent, limit: 6);
            $compareTable = $this->buildCompareTable($products);
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

        $system = <<<PROMPT
Bạn là bộ phân tích ý định cho chatbot bán hàng điện tử. Đọc tin nhắn mới nhất của khách
(và lịch sử hội thoại ngắn nếu có) rồi CHỈ trả về một JSON object hợp lệ, không thêm chữ nào khác,
không markdown, không giải thích. Cấu trúc JSON bắt buộc:

{
  "intent": "search" | "compare" | "support" | "buy" | "unknown",
  "keywords": string,            // từ khóa chính, tiếng Việt tự nhiên, vd "điện thoại pin trâu"
  "category": string|null,       // một trong: dien-thoai, laptop, tai-nghe, may-tinh-bang, phu-kien, hoặc null
  "brand": string|null,          // vd apple, samsung, xiaomi, sony, dell, hoặc null
  "price_min": number|null,
  "price_max": number|null,
  "sort": "price_asc" | "price_desc" | null,  // "rẻ nhất"/"giá thấp nhất" -> price_asc; "đắt nhất"/"giá cao nhất" -> price_desc
  "compare_names": string[]      // tên/model sản phẩm khách muốn so sánh, rỗng nếu không phải compare
}

Quy tắc:
- "so sánh", "vs", "khác nhau" -> intent = compare
- hỏi giá, tìm, gợi ý, "có ... không" -> intent = search
- muốn mua, đặt hàng, thêm giỏ -> intent = buy
- hỏi chính sách, bảo hành, vận chuyển, đổi trả, thanh toán -> intent = support
- Khoảng giá: "dưới 10 triệu" -> price_max=10000000; "trên 20 triệu" -> price_min=20000000; "15-20 triệu" -> price_min=15000000, price_max=20000000.
- "rẻ nhất", "giá thấp nhất", "tiết kiệm nhất" -> sort=price_asc, keywords để rỗng hoặc rất ngắn (không lặp lại cụm "rẻ nhất" vào keywords).
- "đắt nhất", "giá cao nhất", "cao cấp nhất" -> sort=price_desc, keywords để rỗng hoặc rất ngắn.
- Nếu không chắc, đặt giá trị null thay vì đoán bừa.
- QUAN TRỌNG - xử lý câu hỏi nối tiếp: nếu tin nhắn mới KHÔNG nêu rõ tên/loại sản phẩm (vd chỉ hỏi "phiên bản nào tốt",
  "có nên mua không", "còn hàng không", "giá bao nhiêu", dùng từ "nó"/"sản phẩm này"/"phiên bản 128gb"...), hãy đọc
  lịch sử hội thoại gần nhất để xác định khách đang nói về sản phẩm nào, rồi đặt "keywords" là TÊN sản phẩm đó
  (lấy nguyên từ lịch sử, không bịa), không phải chỉ riêng cụm từ trong câu hỏi mới (vd "120gb").
PROMPT;

        $userPrompt = "Lịch sử gần đây:\n{$historyText}\n\nTin nhắn mới của khách: \"{$userMessage}\"";

        $raw = $this->callGemini($system, $userPrompt, jsonMode: true);

        $parsed = json_decode($this->stripJsonFence($raw), true);

        if (!is_array($parsed)) {
            return [
                'intent' => 'unknown', 'keywords' => $userMessage, 'category' => null,
                'brand' => null, 'price_min' => null, 'price_max' => null, 'sort' => null, 'compare_names' => [],
            ];
        }

        return array_merge([
            'intent' => 'unknown', 'keywords' => $userMessage, 'category' => null,
            'brand' => null, 'price_min' => null, 'price_max' => null, 'sort' => null, 'compare_names' => [],
        ], $parsed);
    }

    /**
     * Tìm sản phẩm thật trong DB dựa trên bộ lọc đã trích xuất (không để AI tự bịa sản phẩm).
     */
    protected function searchProducts(array $intent, int $limit = 8)
    {
        $query = Product::query()
            ->where('is_active', 1)
            ->with(['category', 'brand', 'attributes.attribute', 'variants' => function ($q) {
                $q->where('is_active', 1)->with('variantAttributes.attribute');
            }]);

        $keywords = trim((string) ($intent['keywords'] ?? ''));
        $names    = $intent['compare_names'] ?? [];

        if (!empty($names)) {
            $query->where(function ($q) use ($names) {
                foreach ($names as $name) {
                    $q->orWhere('name', 'like', '%' . $name . '%');
                }
            });
        } elseif ($keywords !== '') {
            $terms = array_filter(preg_split('/\s+/', $keywords));
            $query->where(function ($q) use ($terms, $keywords) {
                $q->where('name', 'like', '%' . $keywords . '%')
                  ->orWhere('description', 'like', '%' . $keywords . '%');
                foreach ($terms as $term) {
                    if (mb_strlen($term) >= 3) {
                        $q->orWhere('name', 'like', '%' . $term . '%');
                    }
                }
            });
        }

        if (!empty($intent['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $intent['category']));
        }
        if (!empty($intent['brand'])) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $intent['brand']));
        }
        if (!empty($intent['price_min'])) {
            $query->where('price', '>=', $intent['price_min']);
        }
        if (!empty($intent['price_max'])) {
            $query->where('price', '<=', $intent['price_max']);
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
     * Dựng bảng so sánh thông số kỹ thuật giữa các sản phẩm.
     */
    protected function buildCompareTable($products): array
    {
        $table = [];

        foreach ($products as $product) {
            $row = [
                'name'  => $product->name,
                'price' => $product->price,
            ];
            foreach ($product->attributes as $attr) {
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

        $system = <<<PROMPT
Bạn là trợ lý tư vấn bán hàng cho một cửa hàng điện tử online tại Việt Nam, trả lời bằng tiếng Việt,
giọng văn thân thiện, ngắn gọn, đúng trọng tâm, không bịa thông tin sản phẩm hay giá ngoài dữ liệu được cung cấp.

Nguyên tắc:
- CHỈ nhắc tới sản phẩm có trong "Dữ liệu sản phẩm" bên dưới. Nếu danh sách rỗng, lịch sự báo không tìm thấy
  và gợi ý khách mô tả lại nhu cầu (loại sản phẩm, tầm giá, mục đích sử dụng).
- Nếu là so sánh, nêu rõ điểm khác biệt chính (giá, hiệu năng, pin, camera...) và đưa ra gợi ý phù hợp theo nhu cầu.
- Một sản phẩm có thể có nhiều "phiên bản/biến thể" (vd dung lượng, màu sắc) với giá và tồn kho riêng,
  liệt kê trong mục "Các phiên bản/biến thể". Khi khách hỏi về dung lượng, màu sắc, các lựa chọn, hoặc giá
  của một phiên bản cụ thể, PHẢI dựa vào danh sách biến thể này để trả lời chính xác (không chỉ dùng giá gốc sản phẩm).
- Nếu khách hỏi về chính sách/bảo hành/vận chuyển/đổi trả mà không có dữ liệu cụ thể, trả lời chung chung hợp lý
  và đề nghị khách liên hệ CSKH để biết chi tiết chính xác.
- Giá hiển thị dạng "xxx.xxx.xxx đ".
- Trả lời súc tích, có thể dùng gạch đầu dòng khi liệt kê nhiều sản phẩm, không lan man.
PROMPT;

        $userPrompt = "Lịch sử hội thoại gần đây:\n" . $this->formatHistoryShort($history) .
            "\n\nÝ định nhận diện: {$intent['intent']}" .
            "\n\nDữ liệu sản phẩm:\n{$context}{$compareText}" .
            "\n\nCâu hỏi của khách: \"{$userMessage}\"";

        return trim($this->callGemini($system, $userPrompt));
    }

    protected function formatProductsForPrompt($products): string
    {
        return $products->map(function ($p) {
            $specs = $p->attributes->map(fn ($a) => ($a->attribute->name ?? '') . ': ' . $a->value)->implode(', ');
            $finalPrice = $p->price - ($p->price * $p->discount_percent / 100);

            $base = "- {$p->name} | Danh mục: " . ($p->category->name ?? '-') . " | Hãng: " . ($p->brand->name ?? '-') .
                " | Giá: " . number_format($p->price) . "đ" .
                ($p->discount_percent > 0 ? " (giảm {$p->discount_percent}%, còn " . number_format($finalPrice) . "đ)" : '') .
                " | Tồn kho: {$p->stock} | Thông số: {$specs}";

            if ($p->variants->isNotEmpty()) {
                $variantLines = $p->variants->map(function ($v) {
                    $vSpecs = $v->variantAttributes->map(fn ($a) => ($a->attribute->name ?? '') . ': ' . $a->value)->implode(', ');
                    $vFinal = $v->price - ($v->price * $v->discount_percent / 100);

                    return "    + {$v->label}: Giá " . number_format($v->price) . "đ" .
                        ($v->discount_percent > 0 ? " (giảm {$v->discount_percent}%, còn " . number_format($vFinal) . "đ)" : '') .
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
        return [
            'id'    => $p->id,
            'name'  => $p->name,
            'slug'  => $p->slug,
            'price' => $p->price,
            'discount_percent' => $p->discount_percent,
            'thumbnail' => $p->thumbnail,
        ];
    }

    /**
     * Gọi Gemini REST API.
     */
    protected function callGemini(string $systemPrompt, string $userPrompt, bool $jsonMode = false): string
    {
        $body = [
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
            ],
            'generationConfig' => [
                'temperature' => $jsonMode ? 0.1 : 0.6,
                'maxOutputTokens' => 1024,
            ],
        ];

        if ($jsonMode) {
            $body['generationConfig']['responseMimeType'] = 'application/json';
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint . '?key=' . $this->apiKey, $body);

            if ($response->failed()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                return $jsonMode ? '{}' : 'Xin lỗi, hệ thống tư vấn đang gặp sự cố, bạn vui lòng thử lại sau ít phút.';
            }

            return data_get($response->json(), 'candidates.0.content.parts.0.text', $jsonMode ? '{}' : '');
        } catch (\Throwable $e) {
            Log::error('Gemini call failed: ' . $e->getMessage());
            return $jsonMode ? '{}' : 'Xin lỗi, hệ thống tư vấn đang gặp sự cố, bạn vui lòng thử lại sau ít phút.';
        }
    }

    protected function stripJsonFence(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```json\s*|^```\s*|```$/m', '', $text);
        return trim($text);
    }

    protected function formatHistoryShort(array $history, int $maxTurns = 6): string
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

        $keywords = $session->search_keywords ?? [];
        if (!empty($intent['keywords'])) {
            $keywords[] = $intent['keywords'];
            $keywords = array_slice(array_unique($keywords), -30);
        }

        $interactions = $session->product_interactions ?? [];
        foreach ($products as $p) {
            $interactions[] = ['product_id' => $p->id, 'intent' => $intent['intent'], 'at' => now()->toIso8601String()];
        }

        $session->fill([
            'intent_label'         => in_array($intent['intent'], ['search', 'compare', 'support', 'buy']) ? $intent['intent'] : 'unknown',
            'messages'             => array_slice($messages, -60),
            'search_keywords'      => $keywords,
            'product_interactions' => array_slice($interactions, -50),
            'total_messages'       => $session->total_messages + 2,
            'tokens_used'          => $session->tokens_used + (int) (mb_strlen($userMessage . $reply) / 4),
        ])->save();
    }
}