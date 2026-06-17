<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

    // ─── Xem giỏ hàng ────────────────────────────────────────────
    public function index()
    {
        $cart     = $this->getCart();
        $products = [];
        $total    = 0;

        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $subtotal   = $product->sale_price * $item['quantity'];
                $total     += $subtotal;
                $products[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('cart.index', compact('products', 'total'));
    }

    // ─── Thêm vào giỏ ────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $id  = $request->product_id;
        $qty = $request->quantity ?? 1;

        $cart = $this->getCart();
        $cart[$id]['quantity'] = ($cart[$id]['quantity'] ?? 0) + $qty;
        $this->saveCart($cart);

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    // ─── Mua ngay (thêm rồi redirect sang giỏ) ───────────────────
    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $id   = $request->product_id;
        $cart = $this->getCart();
        $cart[$id]['quantity'] = ($cart[$id]['quantity'] ?? 0) + 1;
        $this->saveCart($cart);

        return redirect()->route('cart.index');
    }

    // ─── Cập nhật số lượng ───────────────────────────────────────
    public function update(Request $request, int $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);

        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            $this->saveCart($cart);
        }

        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    // ─── Xóa sản phẩm khỏi giỏ ──────────────────────────────────
    public function remove(int $productId)
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
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
