<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use App\Mail\OrderPaymentConfirmedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

            $basePrice = $variant ? $variant->final_price : (float) $product->price;
            $discountPercent = $variant ? $variant->discount_percent : $product->discount_percent;
            $price = $basePrice * (1 - $discountPercent / 100);
            
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

    // ─── Kiểm tra mã giảm giá qua AJAX ───────────────────────────────────
    public function checkVoucher(Request $request)
    {
        $code = trim($request->input('voucher_code', ''));

        if (!$code) {
            return response()->json([
                'valid'   => false,
                'message' => 'Vui lòng nhập mã giảm giá.',
            ]);
        }

        [$items, $subtotal] = $this->buildCartItems();

        if (empty($items)) {
            return response()->json([
                'valid'   => false,
                'message' => 'Giỏ hàng trống.',
            ]);
        }

        $user = $request->user();

        // Kiểm tra voucher có tồn tại
        $voucher = Voucher::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hiệu lực.']);
        }

        if ($voucher->assigned_user_id && $voucher->assigned_user_id !== $user->id) {
            return response()->json(['valid' => false, 'message' => 'Mã này không áp dụng được cho tài khoản của bạn.']);
        }

        if ($voucher->expires_at && $voucher->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
        }

        if ($voucher->starts_at && $voucher->starts_at->isFuture()) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá chưa đến ngày áp dụng.']);
        }

        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'Mã giảm giá đã được sử dụng hết số lần cho phép.']);
        }

        if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) {
            return response()->json([
                'valid'   => false,
                'message' => 'Giá trị đơn hàng tối thiểu để áp mã là ' . number_format($voucher->min_order_value) . 'đ.',
            ]);
        }

        $discount = $subtotal * ($voucher->discount_percent / 100);
        $total    = $subtotal - $discount;

        return response()->json([
            'valid'            => true,
            'message'          => 'Mã <strong>' . e($code) . '</strong> giảm ' . $voucher->discount_percent . '% — tiết kiệm ' . number_format($discount) . 'đ!',
            'discount_percent' => $voucher->discount_percent,
            'discount_amount'  => $discount,
            'subtotal'         => $subtotal,
            'total'            => $total,
            'subtotal_fmt'     => number_format($subtotal) . 'đ',
            'discount_fmt'     => '-' . number_format($discount) . 'đ',
            'total_fmt'        => number_format($total) . 'đ',
        ]);
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
     * Tạo đơn hàng + chi tiết đơn hàng + bản ghi thanh toán + trừ tồn kho (transaction).
     */
    private function createOrder(array $orderPayload, array $items, string $paymentStatus, array $paymentInfo = []): ?Order
    {
        try {
            return DB::transaction(function () use ($orderPayload, $items, $paymentStatus, $paymentInfo) {
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
                        'variant_id'   => $it['variant']->id ?? null,
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

                // Ghi lại giao dịch thanh toán (COD: chờ thu tiền khi giao;
                // MoMo: đã thanh toán thành công ngay tại thời điểm tạo đơn)
                $payment = $order->payments()->create([
                    'gateway'        => $orderPayload['payment_method'],
                    'transaction_id' => $paymentInfo['transaction_id'] ?? null,
                    'amount'         => $orderPayload['total'],
                    'status'         => $paymentInfo['status'] ?? ($paymentStatus === 'paid' ? 'success' : 'pending'),
                    'paid_at'        => $paymentInfo['paid_at'] ?? ($paymentStatus === 'paid' ? now() : null),
                ]);

                // Đơn được thanh toán thành công ngay khi tạo (MoMo) => xuất CSV
                if ($paymentStatus === 'paid') {
                    $this->logPaymentToCsv($order, $payment);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Ghi lại 1 dòng giao dịch thanh toán thành công vào file CSV
     * (storage/app/private/payments/payments-{Y-m}.csv). Mỗi tháng 1 file,
     * dòng mới được append vào cuối, có ghi header nếu file chưa tồn tại.
     * Dùng để đối soát / export cho kế toán, không thay thế bảng `payments`.
     */
    private function logPaymentToCsv(Order $order, $payment): void
    {
        try {
            $order->loadMissing('user');

            $disk = Storage::disk('local'); // storage/app/private (Laravel 11+) hoặc storage/app
            $path = 'payments/payments-' . now()->format('Y-m') . '.csv';

            $isNewFile = !$disk->exists($path);

            $absolutePath = $disk->path($path);
            if (!is_dir(dirname($absolutePath))) {
                mkdir(dirname($absolutePath), 0755, true);
            }

            $handle = fopen($absolutePath, 'a');
            if (!$handle) {
                throw new \RuntimeException("Không thể mở file CSV: {$absolutePath}");
            }

            // Khóa file khi ghi để tránh xung đột khi nhiều request cùng ghi
            flock($handle, LOCK_EX);

            if ($isNewFile) {
                fputcsv($handle, [
                    'order_id',
                    'user_id',
                    'customer_name',
                    'gateway',
                    'transaction_id',
                    'amount',
                    'status',
                    'paid_at',
                ]);
            }

            fputcsv($handle, [
                $order->id,
                $order->user_id,
                $order->user->name ?? '',
                $payment->gateway,
                $payment->transaction_id,
                $payment->amount,
                $payment->status,
                optional($payment->paid_at)->format('Y-m-d H:i:s'),
            ]);

            flock($handle, LOCK_UN);
            fclose($handle);
        } catch (\Throwable $e) {
            // Ghi CSV chỉ là log phụ trợ — lỗi ở đây không được làm hỏng
            // luồng thanh toán chính, chỉ cần report lại để theo dõi.
            report($e);
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

    // ─── Gọi sang cổng thanh toán MoMo (dùng chung cho đặt hàng mới & thanh toán lại) ──
    private function momoPaymentRequest(float $amount, string $orderInfo)
    {
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $partnerCode = config('services.momo.partner_code');
        $accessKey   = config('services.momo.access_key');
        $secretKey   = config('services.momo.secret_key');

        $orderId   = 'DH' . time();
        $requestId = (string) time();
        $amount    = (string) (int) round($amount);

        $redirectUrl = route('checkout.momo.return');
        $ipnUrl      = route('checkout.momo.return');
        $requestType = 'payWithMethod';

        // momo_order_id chỉ để đối soát/log, không dùng để xác định luồng xử lý ở momoReturn().
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
            return back()->with('error', 'Không thể kết nối tới cổng thanh toán MoMo. Vui lòng thử lại sau.');
        }

        if (!empty($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        return back()->with('error', 'Lỗi MoMo: ' . ($result['message'] ?? 'Không xác định'));
    }

    // ─── Gọi MoMo cho đơn hàng MỚI (từ giỏ hàng, lúc checkout) ──────
    private function momoPayment(float $amount, array $orderPayload)
    {
        // orderInfo lúc này chỉ mang tính mô tả (đơn thật sự chưa được tạo,
        // chỉ được tạo sau khi MoMo callback thành công ở momoReturn()).
        return $this->momoPaymentRequest($amount, 'Thanh toan don hang moi - ElectronicShop');
    }

    /**
     * Thanh toán lại MoMo cho một đơn hàng ĐÃ TỒN TẠI (khách bấm nút
     * "Thanh toán ngay" trong email nhắc thanh toán). Khác với momoPayment()
     * ở trên (tạo đơn mới từ giỏ hàng), luồng này KHÔNG tạo đơn mới —
     * momoReturn() sẽ chỉ cập nhật payment_status của đơn có sẵn.
     */
    public function retryMomoPayment(Request $request, Order $order)
    {
        // Không còn kiểm tra $request->user() ở đây: route này không bắt
        // buộc đăng nhập (khách bấm link từ email khi chưa/không đăng nhập).
        // Quyền truy cập đã được đảm bảo bởi middleware 'signed' trên route
        // (xem routes/web.php) — chỉ link do hệ thống tự sinh (trong
        // OrderPaymentReminderMail) và còn hạn mới hợp lệ.
        //
        // Các nhánh return sớm bên dưới cũng điều hướng sang trang
        // "checkout.success" — trang đó yêu cầu link có chữ ký hoặc đăng
        // nhập đúng chủ đơn (xem success()), nên ở đây phải tự sinh signed
        // URL thay vì dùng route() thường, nếu không khách chưa đăng nhập
        // sẽ bị 403 khi bấm link từ email.
        $successUrl = URL::temporarySignedRoute('checkout.success', now()->addMinutes(30), ['order' => $order->id]);

        if ($order->payment_method !== 'momo') {
            return redirect()->to($successUrl)
                ->with('error', 'Đơn hàng này không sử dụng phương thức thanh toán MoMo.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->to($successUrl)
                ->with('success', 'Đơn hàng đã được thanh toán trước đó.');
        }

        if (in_array($order->status, ['cancelled', 'returned'])) {
            return redirect()->to($successUrl)
                ->with('error', 'Đơn hàng đã hủy/hoàn trả, không thể thanh toán.');
        }

        // Đánh dấu đây là luồng thanh toán lại cho đơn CÓ SẴN, để momoReturn()
        // biết cần cập nhật đúng đơn này thay vì tạo đơn mới từ giỏ hàng.
        session(['momo_retry_order_id' => $order->id]);

        return $this->momoPaymentRequest(
            (float) $order->total,
            "Thanh toan lai don hang #{$order->id} - ElectronicShop"
        );
    }

    // ─── MoMo redirect/IPN trả về sau khi thanh toán ───────────────
    public function momoReturn(Request $request)
    {
        $code = $request->input('resultCode', $request->input('errorCode', -1));

        // ── Trường hợp 1: THANH TOÁN LẠI cho đơn đã tồn tại (từ email nhắc) ──
        $retryOrderId = session('momo_retry_order_id');
        if ($retryOrderId) {
            session()->forget(['momo_retry_order_id', 'momo_order_id']);

            $order = Order::find($retryOrderId);

            if (!$order) {
                return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
            }

            // Trang "thanh-cong" yêu cầu đăng nhập đúng chủ đơn HOẶC link có
            // chữ ký hợp lệ. Vì đây là luồng khách bấm link từ email (có thể
            // chưa đăng nhập), ta tự sinh 1 link đã ký (hạn ngắn) để họ xem
            // được kết quả thanh toán ngay sau khi MoMo redirect về.
            $successUrl = URL::temporarySignedRoute('checkout.success', now()->addMinutes(30), ['order' => $order->id]);

            if ($code != '0') {
                $message = $request->input('message', 'Giao dịch bị từ chối hoặc đã hết hạn.');
                return redirect()->to($successUrl)
                    ->with('error', 'Thanh toán MoMo thất bại: ' . $message);
            }

            // Tránh ghi trùng payment nếu MoMo gọi callback nhiều lần (redirect + IPN)
            if ($order->payment_status !== 'paid') {
                DB::transaction(function () use ($order, $request) {
                    $order->payment_status = 'paid';
                    $order->save();

                    $payment = $order->payments()->create([
                        'gateway'        => 'momo',
                        'transaction_id' => $request->input('transId'),
                        'amount'         => $order->total,
                        'status'         => 'success',
                        'paid_at'        => now(),
                    ]);

                    // Yêu cầu: khi thanh toán (thành công) thì tạo file CSV
                    $this->logPaymentToCsv($order, $payment);
                });

                // Gửi mail xác nhận đã ghi nhận thanh toán. Đây đúng là tình
                // huống nêu trong docblock của OrderPaymentConfirmedMail:
                // payment_status chuyển unpaid -> paid mà KHÔNG kèm theo việc
                // đổi `status` đơn hàng trong cùng request (đơn ở đây đã
                // confirmed/processing/shipped từ trước, request này chỉ xử
                // lý thanh toán). Đặt ngoài transaction để tránh trường hợp
                // gửi mail bị rollback theo nếu mail thất bại; và nếu gửi
                // mail lỗi cũng không ảnh hưởng tới việc đã lưu thanh toán.
                //
                // QUAN TRỌNG: phải dùng $order->contact_email (accessor trong
                // Order model), KHÔNG dùng $order->user->email trực tiếp.
                // Với đơn khách vãng lai do admin tạo (Admin\OrderController::
                // store()), user_id trỏ tới 1 tài khoản "giả" có email dạng
                // guest_xxx@noemail.local — email THẬT của khách nằm ở cột
                // customer_email. contact_email ưu tiên customer_email trước,
                // chỉ fallback sang user->email khi customer_email trống.
                $order->loadMissing('user');
                $contactEmail = $order->contact_email;

                if ($contactEmail && !str_ends_with($contactEmail, '@noemail.local')) {
                    try {
                        Mail::to($contactEmail)->send(new OrderPaymentConfirmedMail($order));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }

            return redirect()->to($successUrl)
                ->with('success', 'Thanh toán MoMo thành công!');
        }

        // ── Trường hợp 2: ĐẶT HÀNG MỚI từ giỏ hàng (luồng checkout gốc) ──
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

        $order = $this->createOrder($orderPayload, $items, 'paid', [
            'status'         => 'success',
            'transaction_id' => $request->input('transId'),
            'paid_at'        => now(),
        ]);

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
        $user = $request->user();

        $isOwner  = $user && $order->user_id === $user->id;
        // Truy cập qua link có chữ ký hợp lệ (được sinh ra ngay sau khi MoMo
        // xác nhận thanh toán ở momoReturn()) — cho phép khách chưa đăng
        // nhập vẫn xem được kết quả thanh toán khi bấm link từ email.
        $isSigned = $request->hasValidSignature();

        if (!$isOwner && !$isSigned) {
            abort(403);
        }

        $order->load('items', 'address');

        return view('checkout.success', compact('order'));
    }
}