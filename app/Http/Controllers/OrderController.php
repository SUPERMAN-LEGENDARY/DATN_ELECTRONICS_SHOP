<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng của khách hàng đang đăng nhập
     */
    public function index(Request $request)
    {
        $status  = $request->get('status', 'all');
        $keyword = trim((string) $request->get('keyword', ''));

        $query = Order::with(['items.product', 'address', 'voucher'])
            ->where('user_id', auth()->id());

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                if (is_numeric($keyword)) {
                    $q->orWhere('id', $keyword);
                }

                $q->orWhereHas('items', function ($item) use ($keyword) {
                    $item->where('product_name', 'like', "%{$keyword}%");
                });
            });
        }

        $orders = $query->latest('created_at')->paginate(10)->withQueryString();

        // Đếm số lượng đơn theo từng trạng thái
        $counts = Order::where('user_id', auth()->id())
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'pending') as pending,
                SUM(status = 'confirmed') as confirmed,
                SUM(status = 'processing') as processing,
                SUM(status = 'shipped') as shipped,
                SUM(status = 'delivered') as delivered,
                SUM(status = 'cancelled') as cancelled,
                SUM(status = 'returned') as returned
            ")
            ->first();

        return view('profile.order', compact('orders', 'status', 'keyword', 'counts'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show(Request $request, Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        $order->load(['items.product', 'address', 'voucher']);

        if ($request->routeIs('profile.*')) {
            return view('profile.order_detail', compact('order'));
        }

        return view('order.order_detail', compact('order'));
    }

    /**
     * Hủy đơn hàng (chỉ khi đơn còn ở trạng thái pending / confirmed / processing)
     */
    public function cancel(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy đơn hàng.');
    }

    /**
     * Xác nhận đã nhận hàng (chuyển từ shipped -> delivered)
     */
    public function received(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Không thể xác nhận nhận hàng ở trạng thái hiện tại.');
        }

        $order->update(['status' => 'delivered']);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng.');
    }

    /**
     * Mua lại đơn hàng cũ: thêm toàn bộ sản phẩm vào giỏ hàng hiện tại
     */
    public function reorder(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        $cart = session()->get('cart', []);

        foreach ($order->items as $item) {
            // Đơn cũ không lưu variant_id nên mặc định gộp theo product_id (variant = 0)
            $key = $item->product_id . '-0';

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $item->quantity;
            } else {
                $cart[$key] = [
                    'product_id' => $item->product_id,
                    'variant_id' => null,
                    'quantity'   => $item->quantity,
                ];
            }
        }

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }
}
