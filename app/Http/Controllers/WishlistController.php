<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm yêu thích của user hiện tại.
     */
    public function index()
    {
        $products = Auth::user()
            ->wishlistProducts()
            ->with(['category', 'variants'])
            ->withCount('reviews')
            ->paginate(12);

        return view('profile.wishlist', compact('products'));
    }

    /**
     * Toggle thêm / bỏ sản phẩm khỏi wishlist.
     * Trả về JSON { wishlisted: bool, count: int } cho AJAX.
     */
    public function toggle(Product $product)
    {
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
                            ->where('product_id', $product->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $wishlisted = true;
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        return response()->json([
            'wishlisted' => $wishlisted,
            'count'      => $count,
        ]);
    }
}
