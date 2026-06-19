<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ─── Danh sách đơn hàng (có phân trang 20) ──────────────────
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address', 'voucher']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('q')) {
            $query->where('id', $request->q)
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->q}%"));
        }

        $orders = $query->latest('created_at')->paginate(20)->withQueryString();
        $trashedCount = Order::onlyTrashed()->count();

        return view('admin.orders.index', compact('orders', 'trashedCount'));
    }

    // ─── Chi tiết đơn hàng ──────────────────────────────────────
    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'address', 'voucher']);
        return view('admin.orders.show', compact('order'));
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────────
    public function edit(Order $order)
    {
        $addresses = $order->user->addresses ?? collect();
        $vouchers = Voucher::where('is_active', 1)->get();
        return view('admin.orders.edit', compact('order', 'addresses', 'vouchers'));
    }

    // ─── Cập nhật đơn hàng ──────────────────────────────────────
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'status'         => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'payment_method' => 'required|in:cod,momo',
            'voucher_id'     => 'nullable|exists:vouchers,id',
            'note'           => 'nullable|string|max:500',
        ]);

        $order->update([
            'address_id'     => $request->address_id,
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'voucher_id'     => $request->voucher_id,
            'note'           => $request->note,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Đã cập nhật đơn hàng.');
    }

    // ─── Cập nhật trạng thái (nhanh) ──────────────────────────
public function updateStatus(Request $request, Order $order)
{
    $request->validate([
        'status'         => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
        'payment_status' => 'sometimes|in:unpaid,paid,refunded',
    ]);

    // Không cho sửa nếu đơn đã hủy hoặc trả hàng (đơn đã kết thúc)
    if (in_array($order->status, ['cancelled', 'returned'])) {
        return back()->with('error', 'Đơn hàng đã kết thúc, không thể chỉnh sửa.');
    }

    $currentStatus  = $order->status;
    // Lưu lại payment_status GỐC trước khi bị logic hủy/trả hàng làm thay đổi bên dưới,
    // để dùng so sánh ở bước xử lý thanh toán (tránh tự so sánh với giá trị mình vừa đổi)
    $currentPayment = $order->payment_status;

    $newStatus  = $request->status;
    $newPayment = $request->payment_status;

    // ===== XỬ LÝ TRẠNG THÁI ĐƠN HÀNG (không cho nhảy bậc) =====
    if ($newStatus && $newStatus !== $currentStatus) {

        $allowedTransitions = [
            'pending'    => ['confirmed', 'cancelled'],
            'confirmed'  => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped'    => ['delivered', 'cancelled'],
            'delivered'  => ['returned'],
        ];

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($newStatus, $allowedTransitions[$currentStatus])
        ) {
            return back()->with(
                'error',
                "Không thể chuyển từ {$currentStatus} sang {$newStatus}."
            );
        }

        // Hủy đơn
        if ($newStatus === 'cancelled') {
            // Nếu đã thanh toán thì hoàn tiền, chưa thanh toán thì giữ nguyên unpaid
            if ($currentPayment === 'paid') {
                $order->payment_status = 'refunded';
            }
        }

        // Trả hàng (chỉ xảy ra khi đang delivered)
        if ($newStatus === 'returned') {
            $order->payment_status = 'refunded';
        }

        $order->status = $newStatus;
    }

    // ===== XỬ LÝ TRẠNG THÁI THANH TOÁN =====
    // So sánh với $currentPayment (giá trị gốc) chứ không phải $order->payment_status,
    // vì $order->payment_status có thể đã bị đổi thành 'refunded' ở bước trên (do hủy/trả hàng).
    // Nếu so với $order->payment_status, request hủy đơn đã thanh toán sẽ luôn bị từ chối ở đây (lỗi cũ).
    if ($newPayment && $newPayment !== $currentPayment) {

        // Chỉ cho phép user chủ động đổi unpaid -> paid
        if ($currentPayment === 'unpaid' && $newPayment === 'paid') {
            $order->payment_status = 'paid';
        } else {
            return back()->with(
                'error',
                'Không thể thay đổi trạng thái thanh toán theo cách này.'
            );
        }
    }

    $order->save();

    return back()->with(
        'success',
        'Cập nhật trạng thái thành công.'
    );
}

    // ─── Hủy đơn hàng (hoàn lại tồn kho) ──────────────────────
    public function cancel(Order $order)
    {
        if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'Không thể hủy đơn hàng đã vận chuyển hoặc hoàn thành.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->product()->increment('stock', $item->quantity);
            }
            $order->status = 'cancelled';
            $order->payment_status = 'refunded';
            $order->save();
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Đã hủy đơn hàng và hoàn lại tồn kho.');
    }

    // ─── Xóa mềm (chuyển vào thùng rác) ────────────────────────
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Đã chuyển đơn hàng vào thùng rác.');
    }

    // ─── Thùng rác ──────────────────────────────────────────────
    public function trash(Request $request)
    {
        $query = Order::onlyTrashed()->with(['user', 'address', 'voucher']);

        if ($request->filled('q')) {
            $query->where('id', $request->q)
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->q}%"));
        }

        $orders = $query->latest('deleted_at')->paginate(20)->withQueryString();
        $trashedCount = Order::onlyTrashed()->count();

        return view('admin.orders.trash', compact('orders', 'trashedCount'));
    }

    // ─── Khôi phục 1 đơn hàng ──────────────────────────────────
    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.trash')
            ->with('success', "Đã khôi phục đơn hàng #{$order->id}.");
    }

    // ─── Khôi phục tất cả ──────────────────────────────────────
    public function restoreAll()
    {
        $count = Order::onlyTrashed()->count();
        Order::onlyTrashed()->restore();

        return redirect()->route('admin.orders.trash')
            ->with('success', "Đã khôi phục {$count} đơn hàng.");
    }

    // ─── Xóa vĩnh viễn 1 đơn hàng ─────────────────────────────
    public function forceDelete($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin.orders.trash')
            ->with('success', 'Đã xóa vĩnh viễn đơn hàng.');
    }

    // ─── Dọn sạch thùng rác ────────────────────────────────────
    public function emptyTrash()
    {
        Order::onlyTrashed()->forceDelete();

        return redirect()->route('admin.orders.trash')
            ->with('success', 'Đã dọn sạch thùng rác.');
    }
}