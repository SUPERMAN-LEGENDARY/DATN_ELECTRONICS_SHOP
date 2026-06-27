<?php

namespace App\Services;

/**
 * BadWordDetector
 *
 * Phát hiện từ ngữ không chuẩn mực trong nội dung đánh giá.
 * Hỗ trợ tiếng Việt (có/không dấu) và tiếng Anh.
 *
 * Cách dùng:
 *   $result = BadWordDetector::check($text);
 *   $result['found']   // bool
 *   $result['words']   // mảng các từ bị phát hiện
 */
class BadWordDetector
{
    /**
     * Danh sách từ/cụm từ không chuẩn mực.
     * - Thêm từ mới vào đây để mở rộng danh sách.
     * - Hỗ trợ regex nếu bắt đầu bằng dấu ~, ví dụ: '~đ[uú].*mẹ'
     */
    protected static array $wordList = [
        // ── Tiếng Việt – chửi thề / tục tĩu ──────────────────────────────
        'đụ', 'đù', 'đú',
        'đéo', 'đ éo', 'đ.éo',
        'địt', 'dit',
        'cặc', 'c.ặc', 'cac',
        'lồn', 'lon', 'l.ồn',
        'đmm', 'đm', 'dm',
        'vcl', 'vl', 'vãi lồn', 'vai lon',
        'đcm', 'đkm', 'dkm',
        'mẹ kiếp', 'me kiep',
        'thằng chó', 'thang cho',
        'con chó', 'con cho',
        'đồ chó', 'do cho',
        'con điếm', 'con diem',
        'đĩ', 'di', 'cave',
        'khốn nạn', 'khon nan',
        'mả mẹ', 'ma me',
        'mả cha', 'ma cha',
        'súc vật', 'suc vat',
        'ngu vãi', 'ngu vai',
        'ngu như chó',
        'óc chó',
        'đầu bò', 'dau bo',
        'thần kinh', // dùng theo nghĩa chửi
        'chó má', 'cho ma',
        'bố láo', 'bo lao',
        'mẹ mày', 'me may',
        'cha mày', 'cha may',
        'cút đi', 'cut di',
        'biến đi',
        'tởm',
        'ghê tởm',
        'hãm',
        'hãm vl',
        'trash',
        'rác rưởi', 'rac ruoi',
        'đồ rác',
        'shit',       // Anh nhưng hay dùng trong tiếng Việt
        'fuck',
        'wtf',

        // ── Tiếng Anh ─────────────────────────────────────────────────────
        'asshole', 'ass hole',
        'bastard',
        'bitch',
        'bullshit', 'bull shit',
        'cock',
        'cunt',
        'damn',
        'dick',
        'dumbass', 'dumb ass',
        'faggot',
        'fucker', 'fucking', 'fucked',
        'idiot',
        'moron',
        'motherfucker', 'mf',
        'nigger', 'nigga',
        'penis',
        'pussy',
        'retard',
        'slut',
        'son of a bitch', 'sob',
        'stupid',
        'whore',
    ];

    /**
     * Kiểm tra văn bản xem có chứa từ không chuẩn mực không.
     *
     * @param  string|null  $text
     * @return array{found: bool, words: array<string>}
     */
    public static function check(?string $text): array
    {
        if (empty($text)) {
            return ['found' => false, 'words' => []];
        }

        // Chuẩn hoá: lowercase, bỏ khoảng trắng thừa
        $normalised = mb_strtolower(trim($text), 'UTF-8');

        $detected = [];

        foreach (static::$wordList as $entry) {
            // Từ hỗ trợ regex (bắt đầu bằng ~)
            if (str_starts_with($entry, '~')) {
                $pattern = '/' . substr($entry, 1) . '/ui';
                if (preg_match($pattern, $normalised)) {
                    $detected[] = substr($entry, 1); // trả pattern không có ~
                }
                continue;
            }

            // Tìm kiếm chuỗi bình thường (không yêu cầu word-boundary vì TV hay ghép từ)
            if (str_contains($normalised, mb_strtolower($entry, 'UTF-8'))) {
                $detected[] = $entry;
            }
        }

        // Loại trùng lặp
        $detected = array_values(array_unique($detected));

        return [
            'found' => count($detected) > 0,
            'words' => $detected,
        ];
    }

    /**
     * Trả về true nếu văn bản có từ không chuẩn mực.
     */
    public static function hasBadWords(?string $text): bool
    {
        return static::check($text)['found'];
    }
}
