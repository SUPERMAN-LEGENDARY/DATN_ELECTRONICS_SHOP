<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'reference_id',
        'title',
        'body',
        'url',
        'image',
        'is_read',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Tạo thông báo tin tức mới cho tất cả user đã subscribe.
     * Tránh duplicate: chỉ tạo nếu chưa có thông báo nào cho reference_id này.
     */
    public static function dispatchNewsNotification(News $news): int
    {
        // Kiểm tra xem đã tạo thông báo cho bài này chưa
        $alreadySent = static::where('type', 'news')
                             ->where('reference_id', $news->id)
                             ->exists();

        if ($alreadySent) {
            return 0;
        }

        // Lấy tất cả email đang subscribe active
        $subscriberEmails = NewsletterSubscriber::where('is_active', true)
                                                ->pluck('email');

        if ($subscriberEmails->isEmpty()) {
            return 0;
        }

        // Lấy user có email trùng với subscriber
        $users = User::whereIn('email', $subscriberEmails)->get();

        if ($users->isEmpty()) {
            return 0;
        }

        $url   = route('news.show', $news->slug);
        $now   = now();
        $rows  = [];

        foreach ($users as $user) {
            $rows[] = [
                'user_id'      => $user->id,
                'type'         => 'news',
                'reference_id' => $news->id,
                'title'        => 'Tin tức mới: ' . $news->title,
                'body'         => $news->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($news->content), 120),
                'url'          => $url,
                'image'        => $news->thumbnail
                                    ? \Illuminate\Support\Facades\Storage::url($news->thumbnail)
                                    : null,
                'is_read'      => false,
                'created_at'   => $now,
            ];
        }

        static::insert($rows);

        return count($rows);
    }
}
