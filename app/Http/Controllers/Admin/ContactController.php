<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /* ───── DANH SÁCH ───── */
    public function index(Request $request): View
    {
        $query = Contact::latest();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên / email / chủ đề
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',    'like', "%$s%")
                  ->orWhere('email',   'like', "%$s%")
                  ->orWhere('subject', 'like', "%$s%");
            });
        }

        $contacts = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => Contact::count(),
            'new'        => Contact::where('status', 'new')->count(),
            'processing' => Contact::where('status', 'processing')->count(),
            'done'       => Contact::where('status', 'done')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'stats'));
    }

    /* ───── CHI TIẾT + FORM REPLY ───── */
    public function show(Contact $contact): View
    {
        // Tự động chuyển sang "processing" khi admin mở
        if ($contact->status === 'new') {
            $contact->update([
                'status'       => 'processing',
                'processed_at' => now(),
            ]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    /* ───── GỬI PHẢN HỒI ───── */
    public function reply(Request $request, Contact $contact): RedirectResponse
    {
        $data = $request->validate([
            'reply_message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'reply_message.required' => 'Vui lòng nhập nội dung phản hồi.',
            'reply_message.min'      => 'Nội dung phản hồi phải ít nhất 10 ký tự.',
        ]);

        // Gửi email phản hồi
        try {
            Mail::send(new ContactReplyMail($contact, $data['reply_message']));
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Không thể gửi email, vui lòng kiểm tra cấu hình mail. Lỗi: ' . $e->getMessage());
        }

        // Cập nhật trạng thái
        $contact->update([
            'reply_message' => $data['reply_message'],
            'replied_at'    => now(),
            'status'        => 'done',
        ]);

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', "Đã gửi phản hồi tới {$contact->email} thành công!");
    }

    /* ───── CẬP NHẬT TRẠNG THÁI THỦ CÔNG ───── */
    public function updateStatus(Request $request, Contact $contact): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:new,processing,done'],
        ]);

        $contact->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }

    /* ───── XOÁ ───── */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();
        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Đã xoá liên hệ.');
    }
}
