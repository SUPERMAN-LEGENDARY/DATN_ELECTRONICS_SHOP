<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /**
     * Đăng ký nhận tin (dùng chung cho form ở footer và trang tin tức).
     * Trả về JSON để JS hiển thị thông báo ngay tại form, không load lại trang.
     */
    public function subscribe(Request $request)
    {
        try {
            $data = $request->validate([
                'email' => 'required|email:rfc,dns|max:255',
            ], [
                'email.required' => 'Vui lòng nhập email.',
                'email.email'    => 'Email không hợp lệ.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }

        $existing = NewsletterSubscriber::where('email', $data['email'])->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'message' => 'Email này đã đăng ký nhận tin rồi.',
                ], 422);
            }

            // Từng hủy đăng ký trước đó → kích hoạt lại
            $existing->update([
                'is_active'     => true,
                'subscribed_at' => now(),
            ]);
            $subscriber = $existing;
        } else {
            $subscriber = NewsletterSubscriber::create([
                'email'         => $data['email'],
                'source'        => $request->input('source', 'website'),
                'is_active'     => true,
                'subscribed_at' => now(),
            ]);
        }

        try {
            Mail::to($subscriber->email)->send(new NewsletterWelcomeMail($subscriber));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Đăng ký thành công! Cảm ơn bạn đã đồng hành cùng ElectronicShop.',
        ]);
    }
}
