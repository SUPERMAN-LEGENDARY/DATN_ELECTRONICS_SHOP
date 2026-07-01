<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // ─── Lấy giỏ hàng từ session (giống CartController) ──────────
    private function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Tái tạo lại danh sách sản phẩm + tổng tiền từ session cart.
     * Luôn tính lại giá ở server để đảm bảo bảo mật (không tin dữ liệu từ client).
     */
    private function buildCartItems(): array
    {
        $cart  = $this->getCart();
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $row) {
            if (empty($row['product_id'])) continue;

            $product = Product::find($row['product_id']);
            if (!$product) continue;

            $variant = null;
            if (!empty($row['variant_id'])) {
                $variant = ProductVariant::find($row['variant_id']);
                if (!$variant) continue;
            }

            $price = $variant ? $variant->final_price : $product->sale_price;
            $stock = $variant ? $variant->stock : $product->stock;
            $qty   = (int) $row['quantity'];

            if ($qty < 1) continue;

            $lineTotal = $price * $qty;
            $subtotal += $lineTotal;

            $items[] = [
                'key'        => $key,
                'product'    => $product,
                'variant'    => $variant,
                'quantity'   => $qty,
                'price'      => $price,
                'stock'      => $stock,
                'line_total' => $lineTotal,
            ];
        }

        return [$items, $subtotal];
    }

    // ─── Trang Checkout ────────────────────────────────────────────
    public function index(Request $request)
    {
        [$items, $subtotal] = $this->buildCartItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống, không thể thanh toán.');
        }

        $user      = $request->user();
        $addresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->get();

        return view('checkout.index', [
            'items'     => $items,
            'subtotal'  => $subtotal,
            'addresses' => $addresses,
        ]);
    }

    // ─── Áp dụng mã giảm giá (AJAX hoặc submit lại form) ──────────
    private function applyVoucher(?string $code, float $subtotal, ?int $userId = null): array
    {
        if (!$code) return [null, 0];

        $voucher = Voucher::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) return [null, 0];

        // Voucher cá nhân hoá (được admin tặng riêng) chỉ đúng khách đó mới dùng được
        if ($voucher->assigned_user_id && $voucher->assigned_user_id !== $userId) {
            return [null, 0];
        }

        if ($voucher->expires_at && $voucher->expires_at->isPast()) return [null, 0];
        if ($voucher->starts_at && $voucher->starts_at->isFuture()) return [null, 0];
        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) return [null, 0];
        if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) return [null, 0];

        $discount = $subtotal * ($voucher->discount_percent / 100);

        return [$voucher, $discount];
    }

    // ─── Xử lý đặt hàng (COD hoặc MoMo) ────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'address_id'     => 'nullable|exists:addresses,id',
            'full_name'      => 'required_without:address_id|nullable|string|max:255',
            'phone'          => 'required_without:address_id|nullable|string|max:20',
            'province'       => 'required_without:address_id|nullable|string|max:100',
            'district'       => 'required_without:address_id|nullable|string|max:100',
            'ward'           => 'required_without:address_id|nullable|string|max:100',
            'street'         => 'required_without:address_id|nullable|string|max:255',
            'payment_method' => 'required|in:cod,momo',
            'voucher_code'   => 'nullable|string|max:50',
            'note'           => 'nullable|string|max:500',
            'save_address'   => 'nullable|boolean',
        ]);

        $user = $request->user();

        [$items, $subtotal] = $this->buildCartItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống, không thể thanh toán.');
        }

        // Kiểm tra tồn kho lần cuối trước khi đặt hàng
        foreach ($items as $it) {
            if ($it['quantity'] > $it['stock']) {
                return back()->with('error', "Sản phẩm \"{$it['product']->name}\" không đủ tồn kho.")->withInput();
            }
        }

        // 1. Xác định địa chỉ giao hàng
        if ($request->address_id) {
            $address = Address::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$address) {
                return back()->with('error', 'Địa chỉ không hợp lệ.')->withInput();
            }
        } else {
            $address = Address::create([
                'user_id'    => $user->id,
                'full_name'  => $request->full_name,
                'phone'      => $request->phone,
                'province'   => $request->province,
                'district'   => $request->district,
                'ward'       => $request->ward,
                'street'     => $request->street,
                'is_default' => $request->boolean('save_address') ? false : false,
            ]);
        }

        // 2. Áp dụng voucher (nếu có)
        [$voucher, $discount] = $this->applyVoucher($request->voucher_code, $subtotal, $user->id);
        $total = $subtotal - $discount;

        $paymentMethod = $request->payment_method;

        // 3. Đóng gói dữ liệu đơn hàng để dùng chung cho COD / MoMo
        $orderPayload = [
            'user_id'         => $user->id,
            'address_id'      => $address->id,
            'voucher_id'      => $voucher?->id,
            'subtotal'        => $subtotal,
            'discount_amount' => $discount,
            'total'           => $total,
            'note'            => $request->note,
            'payment_method'  => $paymentMethod,
            'cart_keys'       => array_column($items, 'key'),
        ];

        if ($paymentMethod === 'momo') {
            // Lưu tạm thông tin đơn hàng vào session để tạo đơn sau khi MoMo callback về
            session(['pending_order' => $orderPayload]);

            return $this->momoPayment($total, $orderPayload);
        }

        // ─── COD: tạo đơn hàng ngay ───────────────────────────────
        $order = $this->createOrder($orderPayload, $items, 'unpaid');

        if (!$order) {
            return back()->with('error', 'Đặt hàng thất bại. Vui lòng thử lại!')->withInput();
        }

        $this->clearPurchasedItems($orderPayload['cart_keys']);

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Đặt hàng thành công!');
    }

    /**
     * Tạo đơn hàng + chi tiết đơn hàng + trừ tồn kho (transaction).
     */
    private function createOrder(array $orderPayload, array $items, string $paymentStatus): ?Order
    {
        try {
            return DB::transaction(function () use ($orderPayload, $items, $paymentStatus) {
                $order = Order::create([
                    'user_id'         => $orderPayload['user_id'],
                    'address_id'      => $orderPayload['address_id'],
                    'voucher_id'      => $orderPayload['voucher_id'],
                    'status'          => 'pending',
                    'payment_method'  => $orderPayload['payment_method'],
                    'payment_status'  => $paymentStatus,
                    'subtotal'        => $orderPayload['subtotal'],
                    'discount_amount' => $orderPayload['discount_amount'],
                    'total'           => $orderPayload['total'],
                    'note'            => $orderPayload['note'],
                ]);

                foreach ($items as $it) {
                    $order->items()->create([
                        'product_id'   => $it['product']->id,
                        'product_name' => $it['product']->name . ($it['variant'] ? ' - ' . $it['variant']->label : ''),
                        'quantity'     => $it['quantity'],
                        'unit_price'   => $it['price'],
                        'total_price'  => $it['line_total'],
                    ]);

                    // Trừ tồn kho theo variant nếu có, ngược lại theo product
                    if ($it['variant']) {
                        $it['variant']->decrement('stock', $it['quantity']);
                    } else {
                        $it['product']->decrement('stock', $it['quantity']);
                    }
                }

                if ($orderPayload['voucher_id']) {
                    Voucher::where('id', $orderPayload['voucher_id'])->increment('used_count');
                }

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /** Xóa các dòng đã mua khỏi giỏ hàng (giữ lại các dòng chưa mua) */
    private function clearPurchasedItems(array $keys): void
    {
        $cart = $this->getCart();
        foreach ($keys as $key) {
            unset($cart[$key]);
        }
        session(['cart' => $cart]);
    }

    // ─── Gọi sang cổng thanh toán MoMo ─────────────────────────────
    private function momoPayment(float $amount, array $orderPayload)
    {
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');

        $orderId   = 'DH' . time();
        $requestId = (string) time();
        $orderInfo = 'Thanh toan don hang ' . $orderId . ' - ElectronicShop';
        $amount    = (string) (int) round($amount);

        $redirectUrl = route('checkout.momo.return');
        $ipnUrl      = route('checkout.momo.return');
        $requestType = 'payWithATM';

        // Lưu mapping momo_order_id -> dữ liệu đơn hàng (vì MoMo redirect không giữ session đáng tin cậy 100%,
        // nhưng ta vẫn ưu tiên session, momo_order_id chỉ để đối soát/log)
        session(['momo_order_id' => $orderId]);

        $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData=&ipnUrl={$ipnUrl}&orderId={$orderId}"
            . "&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}"
            . "&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'ElectronicShop',
            'storeId'     => 'ElectronicShopStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => '',
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        try {
            $response = Http::timeout(10)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint, $data);

            $result = $response->json();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Không thể kết nối tới cổng thanh toán MoMo. Vui lòng thử lại sau.')->withInput();
        }

        if (!empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        return back()->with('error', 'Lỗi MoMo: ' . ($result['message'] ?? 'Không xác định'))->withInput();
    }

    // ─── MoMo redirect/IPN trả về sau khi thanh toán ───────────────
    public function momoReturn(Request $request)
    {
        $code = $request->input('resultCode', $request->input('errorCode', -1));

        $orderPayload = session('pending_order');

        if ($code != '0' || !$orderPayload) {
            session()->forget(['pending_order', 'momo_order_id']);

            $message = $request->input('message', 'Giao dịch bị từ chối hoặc đã hết hạn.');
            return redirect()->route('cart.index')->with('error', 'Thanh toán MoMo thất bại: ' . $message);
        }

        // Tái tạo lại danh sách sản phẩm từ giỏ hàng hiện tại theo các key đã lưu
        [$allItems] = $this->buildCartItems();
        $items = array_values(array_filter($allItems, fn($it) => in_array($it['key'], $orderPayload['cart_keys'])));

        if (empty($items)) {
            session()->forget(['pending_order', 'momo_order_id']);
            return redirect()->route('cart.index')->with('error', 'Thanh toán thành công nhưng giỏ hàng đã thay đổi, vui lòng liên hệ hỗ trợ.');
        }

        $order = $this->createOrder($orderPayload, $items, 'paid');

        session()->forget(['pending_order', 'momo_order_id']);

        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Thanh toán MoMo thành công nhưng lưu đơn hàng thất bại. Vui lòng liên hệ hỗ trợ.');
        }

        $this->clearPurchasedItems($orderPayload['cart_keys']);

        return redirect()->route('checkout.success', $order->id)
            ->with('success', 'Thanh toán MoMo thành công!');
    }

    // ─── Trang đặt hàng thành công ──────────────────────────────────
    public function success(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load('items', 'address');

        return view('checkout.success', compact('order'));
    }
}