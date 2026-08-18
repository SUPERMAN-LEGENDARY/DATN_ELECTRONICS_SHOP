<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BehaviorLogger;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Lấy giỏ hàng từ session
    private function getCart(): array
    {
        return session('cart', []);
    }

    // Lưu giỏ hàng vào session
    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Sinh key duy nhất cho 1 dòng trong giỏ.
     * Cùng product nhưng khác variant -> key khác nhau.
     * variant_id = null (sản phẩm không có biến thể) -> dùng 0.
     */
    private function makeKey(int $productId, ?int $variantId): string
    {
        return $productId . '-' . ($variantId ?? 0);
    }

    // ─── Xem giỏ hàng ────────────────────────────────────────────
    public function index()
    {
        $cart     = $this->getCart();
        $products = [];
        $total    = 0;

        foreach ($cart as $key => $item) {
            // Bỏ qua item lỗi format (session cũ)
            if (empty($item['product_id'])) {
                unset($cart[$key]);
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $variant = null;
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
                // Nếu variant đã bị xóa thì bỏ qua dòng này
                if (!$variant) continue;
            }

            // Giá & tồn kho lấy theo variant nếu có, ngược lại lấy theo product
            $basePrice = $variant ? $variant->final_price : (float) $product->price;
            $discountPercent = $variant ? $variant->discount_percent : $product->discount_percent;
            $price = $basePrice * (1 - $discountPercent / 100);
            $stock = $variant ? $variant->stock : $product->stock;

            $subtotal = $price * $item['quantity'];
            $total   += $subtotal;

            $products[] = [
                'key'      => $key,
                'product'  => $product,
                'variant'  => $variant,
                'quantity' => $item['quantity'],
                'price'    => $price,
                'stock'    => $stock,
                'subtotal' => $subtotal,
            ];
        }

        // ── Gợi ý "Có thể bạn cũng thích": cùng category với sp trong giỏ ──
        $crossSell = collect();
        if (!empty($products)) {
            $cartProductIds   = collect($products)->pluck('product.id')->all();
            $categoryIds      = collect($products)->pluck('product.category_id')->unique()->filter()->all();

            if (!empty($categoryIds)) {
                // Eager-load 'variants' để accessor min_price (giá thấp nhất hiển thị)
                // không phát sinh N+1 query cho từng sản phẩm cross-sell.
                $crossSell = Product::with('variants')
                    ->active()
                    ->whereIn('category_id', $categoryIds)
                    ->whereNotIn('id', $cartProductIds)
                    ->inRandomOrder()
                    ->limit(6)
                    ->get();
            }
        }

        return view('cart.index', compact('products', 'total', 'crossSell'));
    }

    // ─── Thêm vào giỏ ────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $productId = (int) $request->product_id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $qty       = $request->quantity ?? 1;

        // Nếu có variant_id thì phải đúng là variant của product này
        if ($variantId) {
            $belongs = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->exists();
            if (!$belongs) {
                return back()->with('error', 'Biến thể không hợp lệ.');
            }
        }

        $key  = $this->makeKey($productId, $variantId);
        $cart = $this->getCart();

        $cart[$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => ($cart[$key]['quantity'] ?? 0) + $qty,
        ];

        $this->saveCart($cart);

        // ── Ghi log hành vi: khách thêm sản phẩm vào giỏ ─────────
        BehaviorLogger::log($productId, 'add_to_cart');

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // ─── Mua ngay (thêm rồi redirect sang giỏ) ───────────────────
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id,deleted_at,NULL',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $productId = (int) $request->product_id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;

        if ($variantId) {
            $belongs = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->exists();
            if (!$belongs) {
                return back()->with('error', 'Biến thể không hợp lệ.');
            }
        }

        $key  = $this->makeKey($productId, $variantId);
        $cart = $this->getCart();

        $cart[$key] = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => ($cart[$key]['quantity'] ?? 0) + 1,
        ];

        $this->saveCart($cart);

        // ── Ghi log hành vi: khách bấm mua ngay (tín hiệu mua mạnh) ─
        BehaviorLogger::log($productId, 'add_to_cart', 'buy_now');

        return redirect()->route('cart.index');
    }

    // ─── Cập nhật số lượng ───────────────────────────────────────
    public function update(Request $request, string $key)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $cart = $this->getCart();
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $request->quantity;
            $this->saveCart($cart);
        }

        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    // ─── Xóa sản phẩm khỏi giỏ ──────────────────────────────────
    public function remove(string $key)
    {
        $cart = $this->getCart();
        unset($cart[$key]);
        $this->saveCart($cart);

        return back()->with('success', 'Đã xóa khỏi giỏ hàng.');
    }

    // ─── Xóa toàn bộ giỏ ────────────────────────────────────────
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Đã xóa giỏ hàng.');
    }
}