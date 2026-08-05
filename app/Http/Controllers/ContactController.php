<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Trang liên hệ
     */
    public function index()
    {
    // Lấy thương hiệu để hiển thị ở khu vực icon
    // "Chúng tôi có thể giúp gì cho bạn?"
    $brands = Category::brands()
        ->active()
        ->orderBy('id')
        ->take(6)
        ->get();

        return view('contact.index', compact('brands'));
    }

    /**
     * Gửi liên hệ
     */
    public function send(ContactRequest $request)
    {
        $contact = Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        try {
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@electronicshop.test');
            Mail::to($adminEmail)->send(new ContactMail($contact));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('contact.index')
            ->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất.');
    }
}