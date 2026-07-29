<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // ─── Danh sách đơn hàng ──────────────────────────────────
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
            $query->where(function ($sub) use ($request) {
                $sub->where('id', $request->q)
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->q}%"));
            });
        }

        $orders = $query->latest('created_at')->paginate(10)->withQueryString();
        $trashedCount = Order::onlyTrashed()->count();

        return view('admin.orders.index', compact('orders', 'trashedCount'));
    }

    // ─── Form tạo đơn hàng mới ──────────────────────────────
    public function create()
    {
        $users    = User::all();
        $vouchers = Voucher::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $addresses = Address::all();

        return view('admin.orders.create', compact('users', 'vouchers', 'products', 'addresses'));
    }

    // ─── Lưu đơn hàng mới ────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'customer_phone'   => 'required|string|max:20',
            'customer_name'    => 'required|string|max:255',
            'user_id'          => 'nullable|exists:users,id',
            'address_id'       => 'nullable|exists:addresses,id',
            'address_name'     => 'required_without:address_id|nullable|string|max:255',
            'address_phone'    => 'required_without:address_id|nullable|string|max:20',
            'address_detail'   => 'required_without:address_id|nullable|string|max:500',
            'address_ward'     => 'required_without:address_id|nullable|string|max:100',
            'address_district' => 'required_without:address_id|nullable|string|max:100',
            'address_province' => 'required_without:address_id|nullable|string|max:100',
            'voucher_id'       => 'nullable|exists:vouchers,id',
            'status'           => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_method'   => 'required|in:cod,momo',
            'payment_status'   => 'required|in:unpaid,paid,refunded',
            'note'             => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        // 0. Xác định khách hàng: nếu SĐT đã khớp tài khoản có sẵn (JS đã tự điền user_id)
        // thì dùng lại tài khoản đó; ngược lại tạo khách hàng mới từ SĐT + tên đã nhập.
        if ($request->filled('user_id')) {
            $userId = $request->user_id;
        } else {
            $user = User::create([
                'name'     => $request->customer_name,
                'email'    => 'guest_' . uniqid() . '@noemail.local',
                'phone'    => $request->customer_phone,
                'role'     => 'user',
                'password' => bcrypt(Str::random(24)),
            ]);
            $userId = $user->id;
        }

        // 1. Xử lý địa chỉ
        if ($request->address_id) {
            $address = Address::where('id', $request->address_id)->where('user_id', $userId)->first();
            if (!$address) {
                return back()->with('error', 'Địa chỉ không hợp lệ.')->withInput();
            }
            $addressId = $request->address_id;
        } else {
            $address = Address::create([
                'user_id'   => $userId,
                'full_name' => $request->address_name,
                'phone'     => $request->address_phone,
                'street'    => $request->address_detail,
                'ward'      => $request->address_ward,
                'district'  => $request->address_district,
                'province'  => $request->address_province,
                'is_default'=> false,
            ]);
            $addressId = $address->id;
        }

        // 2. Tính subtotal
        $subtotal = 0;
        $itemsData = [];
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unitPrice = $item['unit_price'] ?? $product->price;
            $totalPrice = $unitPrice * $item['quantity'];
            $subtotal += $totalPrice;
            $itemsData[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $item['quantity'],
                'unit_price'   => $unitPrice,
                'total_price'  => $totalPrice,
            ];
        }

        // 3. Tính discount (nếu có voucher)
        $discount = 0;
        if ($request->voucher_id) {
            $voucher = Voucher::find($request->voucher_id);
            if ($voucher && $voucher->is_active) {
                // Kiểm tra điều kiện đơn tối thiểu
                if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) {
                    $discount = 0;
                } else {
                    $discount = $subtotal * ($voucher->discount_percent / 100);
                }
            }
        }
        // Đảm bảo discount là số
        $discount = (float) $discount;
        $total = $subtotal - $discount;

        // 4. Lưu vào DB (transaction)
        $order = null;
        DB::transaction(function () use ($request, $userId, $addressId, $itemsData, $subtotal, $discount, $total, &$order) {
            $order = Order::create([
                'user_id'        => $userId,
                'address_id'     => $addressId,
                'voucher_id'     => $request->voucher_id,
                'status'         => $request->status,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'subtotal'       => $subtotal,
                'discount_amount'=> $discount,
                'total'          => $total,
                'note'           => $request->note,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);
                // Trừ tồn kho vì đơn đã giao
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }
        });

        return redirect()->route('admin.orders.show', $order)
                         ->with('success', 'Đã tạo đơn hàng mới (đã giao).');
    }

    // ─── Chi tiết đơn hàng ────────────────────────────────────
    public function show(Order $order)
    {
        $order->load(['items.product', 'items.attributes.attribute', 'user', 'address', 'voucher']);
        return view('admin.orders.show', compact('order'));
    }

    // ─── Form chỉnh sửa ──────────────────────────────────────
    public function edit(Order $order)
    {
        $addresses = $order->user->addresses ?? collect();
        $vouchers = Voucher::where('is_active', 1)->get();
        return view('admin.orders.edit', compact('order', 'addresses', 'vouchers'));
    }

    // ─── Cập nhật đơn hàng ──────────────────────────────────
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
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

    // ─── Cập nhật trạng thái (nhanh) ────────────────────────
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'         => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'sometimes|in:unpaid,paid,refunded',
        ]);

        if (in_array($order->status, ['cancelled', 'returned'])) {
            return back()->with('error', 'Đơn hàng đã kết thúc, không thể chỉnh sửa.');
        }

        $currentStatus  = $order->status;
        $currentPayment = $order->payment_status;
        $newStatus  = $request->status;
        $newPayment = $request->payment_status;

        if ($newStatus && $newStatus !== $currentStatus) {
            $allowedTransitions = [
                'pending'    => ['confirmed', 'cancelled'],
                'confirmed'  => ['processing', 'cancelled'],
                'processing' => ['shipped', 'cancelled'],
                'shipped'    => ['delivered', 'cancelled'],
                'delivered'  => [], // Không cho phép hoàn trả sau khi đã giao
            ];

            if (!isset($allowedTransitions[$currentStatus]) || !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                return back()->with('error', "Không thể chuyển từ {$currentStatus} sang {$newStatus}.");
            }

            if ($newStatus === 'cancelled' && $currentPayment === 'paid') {
                $order->payment_status = 'refunded';
            }
            if ($newStatus === 'returned') {
                $order->payment_status = 'refunded';
            }
            $order->status = $newStatus;
        }

        if ($newPayment && $newPayment !== $currentPayment) {
            if ($currentPayment === 'unpaid' && $newPayment === 'paid') {
                $order->payment_status = 'paid';
            } else {
                return back()->with('error', 'Không thể thay đổi trạng thái thanh toán theo cách này.');
            }
        }

        $order->save();
        return back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    // ─── Hủy đơn hàng (hoàn lại tồn kho) ────────────────────
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

    // ─── Xóa mềm ─────────────────────────────────────────────
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Đã chuyển đơn hàng vào thùng rác.');
    }

    // ─── Thùng rác ────────────────────────────────────────────
    public function trash(Request $request)
    {
        $query = Order::onlyTrashed()->with(['user', 'address', 'voucher']);
        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('id', $request->q)
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->q}%"));
            });
        }
        $orders = $query->latest('deleted_at')->paginate(20)->withQueryString();
        $trashedCount = Order::onlyTrashed()->count();
        return view('admin.orders.trash', compact('orders', 'trashedCount'));
    }

    // ─── Khôi phục ────────────────────────────────────────────
    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->route('admin.orders.trash')
            ->with('success', "Đã khôi phục đơn hàng #{$order->id}.");
    }

    public function restoreAll()
    {
        $count = Order::onlyTrashed()->count();
        Order::onlyTrashed()->restore();
        return redirect()->route('admin.orders.trash')
            ->with('success', "Đã khôi phục {$count} đơn hàng.");
    }

    // ─── Xóa vĩnh viễn ────────────────────────────────────────
    public function forceDelete($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->route('admin.orders.trash')
            ->with('success', 'Đã xóa vĩnh viễn đơn hàng.');
    }

    public function emptyTrash()
    {
        Order::onlyTrashed()->forceDelete();
        return redirect()->route('admin.orders.trash')
            ->with('success', 'Đã dọn sạch thùng rác.');
    }
}