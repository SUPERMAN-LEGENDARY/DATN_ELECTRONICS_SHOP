<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Trả JSON danh sách 15 thông báo mới nhất + số chưa đọc (cho AJAX dropdown).
     */
    public function list()
    {
        $user = Auth::user();

        $notifications = Notification::forUser($user->id)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'url'        => $n->url,
                'image'      => $n->image,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at?->diffForHumans(),
            ]);

        $unreadCount = Notification::forUser($user->id)->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Đánh dấu một thông báo đã đọc → trả JSON redirect URL.
     */
    public function markRead(int $id)
    {
        $notification = Notification::where('user_id', Auth::id())
                                    ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return response()->json([
            'success'       => true,
            'url'           => $notification->url,
            'unread_count'  => Notification::forUser(Auth::id())->unread()->count(),
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo của user là đã đọc.
     */
    public function markAllRead()
    {
        Notification::forUser(Auth::id())
                    ->unread()
                    ->update(['is_read' => true]);

        return response()->json([
            'success'      => true,
            'unread_count' => 0,
        ]);
    }
}
