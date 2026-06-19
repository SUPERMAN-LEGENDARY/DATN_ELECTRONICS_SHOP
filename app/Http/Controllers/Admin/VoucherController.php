<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    // ─── Danh sách voucher (có phân trang) ─────────────────────
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($request->filled('q')) {
            $query->where('code', 'like', "%{$request->q}%");
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $vouchers = $query->latest('id')->paginate(20)->withQueryString();
        $trashedCount = Voucher::onlyTrashed()->count(); // Đếm số lượng trong thùng rác

        return view('admin.vouchers.index', compact('vouchers', 'trashedCount'));
    }

    // ─── Form tạo mới ──────────────────────────────────────────
    public function create()
    {
        return view('admin.vouchers.create');
    }

    // ─── Lưu voucher mới ───────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers',
            'discount_percent' => 'required|integer|min:1|max:100',
            'min_order_value' => 'required|numeric|min:0',
            'usage_limit' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['used_count'] = 0;

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher đã được tạo.');
    }

    // ─── Form chỉnh sửa ────────────────────────────────────────
    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    // ─── Cập nhật voucher ──────────────────────────────────────
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_percent' => 'required|integer|min:1|max:100',
            'min_order_value' => 'required|numeric|min:0',
            'usage_limit' => 'required|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher đã được cập nhật.');
    }

    // ─── Xóa mềm (chuyển vào thùng rác) ────────────────────────
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Voucher đã được chuyển vào thùng rác.');
    }

    // ─── Bật/tắt trạng thái ────────────────────────────────────
    public function toggleActive(Voucher $voucher)
    {
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        $status = $voucher->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return redirect()->route('admin.vouchers.index')
            ->with('success', "Đã {$status} voucher \"{$voucher->code}\".");
    }

    // ─── Thùng rác ──────────────────────────────────────────────
    public function trash(Request $request)
    {
        $query = Voucher::onlyTrashed();

        if ($request->filled('q')) {
            $query->where('code', 'like', "%{$request->q}%");
        }

        $vouchers = $query->latest('deleted_at')->paginate(20)->withQueryString();
        $trashedCount = Voucher::onlyTrashed()->count();

        return view('admin.vouchers.trash', compact('vouchers', 'trashedCount'));
    }

    // ─── Khôi phục 1 voucher ──────────────────────────────────
    public function restore($id)
    {
        $voucher = Voucher::onlyTrashed()->findOrFail($id);
        $voucher->restore();

        return redirect()->route('admin.vouchers.trash')
            ->with('success', "Đã khôi phục voucher \"{$voucher->code}\".");
    }

    // ─── Khôi phục tất cả ──────────────────────────────────────
    public function restoreAll()
    {
        $count = Voucher::onlyTrashed()->count();
        Voucher::onlyTrashed()->restore();

        return redirect()->route('admin.vouchers.trash')
            ->with('success', "Đã khôi phục {$count} voucher.");
    }

    // ─── Xóa vĩnh viễn 1 voucher ─────────────────────────────
    public function forceDelete($id)
    {
        $voucher = Voucher::onlyTrashed()->findOrFail($id);
        $voucher->forceDelete();

        return redirect()->route('admin.vouchers.trash')
            ->with('success', 'Đã xóa vĩnh viễn voucher.');
    }

    // ─── Dọn sạch thùng rác ────────────────────────────────────
    public function emptyTrash()
    {
        Voucher::onlyTrashed()->forceDelete();

        return redirect()->route('admin.vouchers.trash')
            ->with('success', 'Đã dọn sạch thùng rác.');
    }
}