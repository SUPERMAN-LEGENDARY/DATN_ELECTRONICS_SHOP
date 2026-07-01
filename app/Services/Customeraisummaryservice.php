<?php

namespace App\Services;

use App\Models\CustomerAiProfile;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Dùng Gemini để viết 1 đoạn nhận định ngắn (2-4 câu, tiếng Việt) cho admin đọc nhanh
 * về 1 khách hàng tiềm năng, dựa HOÀN TOÀN trên số liệu đã tính sẵn trong
 * customer_ai_profiles + ai_sessions (không để AI tự suy đoán hành vi ngoài dữ liệu).
 *
 * Khác với GeminiChatService (chat với khách) và AiSearchParser (parse thanh search):
 * service này chỉ chạy khi admin bấm "Tính lại điểm" cho 1 khách cụ thể, không chạy
 * hàng loạt trong job/cron để tránh tốn quota Gemini không cần thiết.
 */
class CustomerAiSummaryService
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
     * Sinh đoạn tóm tắt cho 1 khách hàng. Trả về null nếu chưa đủ dữ liệu để phân tích
     * hoặc gọi Gemini thất bại (không chặn luồng tính điểm chính nếu AI lỗi).
     */
    public function generate(User $user, CustomerAiProfile $profile): ?string
    {
        if ($profile->total_score <= 0) {
            return null; // Chưa có hành vi gì đáng kể thì không cần AI viết nhận định
        }

        $facts = $this->buildFactsBlock($user, $profile);

        $system = <<<PROMPT
Bạn là trợ lý phân tích dữ liệu bán hàng nội bộ cho quản trị viên một cửa hàng điện tử online.
Nhiệm vụ: đọc số liệu hành vi khách hàng đã được hệ thống tính sẵn bên dưới, rồi viết một đoạn
NHẬN ĐỊNH ngắn gọn (tối đa 4 câu, dưới 500 ký tự) bằng tiếng Việt để admin đọc lướt là hiểu ngay
tình trạng khách hàng này và nên làm gì tiếp theo.

Quy tắc bắt buộc:
- CHỈ dùng đúng số liệu được cung cấp bên dưới, TUYỆT ĐỐI không bịa thêm hành vi, sản phẩm,
  hay lý do nào không có trong dữ liệu.
- Văn phong nghiệp vụ, súc tích, đi thẳng vào nhận định + gợi ý hành động (vd nên chăm sóc gì,
  có nên chủ động liên hệ không, có nên gửi voucher không).
- Không chào hỏi, không mở đầu kiểu "Dựa trên dữ liệu...", viết thẳng vào nội dung.
- Không dùng markdown, không xuống dòng, trả về đúng 1 đoạn văn thuần.
PROMPT;

        $raw = $this->callGemini($system, $facts);
        $summary = trim($raw);

        if ($summary === '') {
            return null;
        }

        // Cắt an toàn theo giới hạn cột TEXT/hiển thị, tránh câu bị cụt giữa từ
        if (mb_strlen($summary) > 600) {
            $summary = mb_substr($summary, 0, 600);
        }

        return $summary;
    }

    protected function buildFactsBlock(User $user, CustomerAiProfile $profile): string
    {
        $keywords = collect($profile->keywords_history ?? [])->take(10)->implode(', ');
        $categories = collect($profile->interest_categories ?? [])->implode(', ');

        $lines = [
            "Khách hàng: {$user->name}",
            "Nhãn tiềm năng: {$profile->lead_label}",
            "Giai đoạn hành trình mua hàng: " . ($profile->lead_stage ?? 'chưa rõ'),
            "Tổng điểm AI: {$profile->total_score} (điểm xem SP: {$profile->score_view}, điểm chat: {$profile->score_chat}, điểm đơn hàng: {$profile->score_order})",
            "Sản phẩm quan tâm nhất: " . ($profile->topInterestProduct?->name ?? 'chưa xác định'),
            "Danh mục quan tâm: " . ($categories ?: 'chưa rõ'),
            "Từ khoá tìm kiếm/chat gần đây: " . ($keywords ?: 'không có'),
            "Xác suất mua lại: " . ($profile->repurchase_probability !== null ? round($profile->repurchase_probability * 100) . '%' : 'chưa đủ dữ liệu'),
            "Ngày dự đoán mua lại: " . ($profile->predicted_repurchase_at?->format('d/m/Y') ?? 'chưa đủ dữ liệu'),
            "Voucher đang được hệ thống gợi ý: " . ($profile->voucher_recommended ? ($profile->voucher_reason ?? 'có') : 'không'),
        ];

        return implode("\n", $lines);
    }

    protected function callGemini(string $systemPrompt, string $userPrompt): string
    {
        $body = [
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents'           => [['role' => 'user', 'parts' => [['text' => $userPrompt]]]],
            'generationConfig'   => [
                'temperature'     => 0.4,
                'maxOutputTokens' => 400,
            ],
        ];

        try {
            $response = Http::timeout(20)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint . '?key=' . $this->apiKey, $body);

            if ($response->failed()) {
                Log::error('CustomerAiSummaryService Gemini error', ['status' => $response->status(), 'body' => $response->body()]);
                return '';
            }

            return data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        } catch (\Throwable $e) {
            Log::error('CustomerAiSummaryService Gemini call failed: ' . $e->getMessage());
            return '';
        }
    }
}