<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Product;

class CompareController extends Controller
{
    /**
     * Trang so sánh
     */
    public function index()
    {
        $ids = session('compare', []);

        $products = Product::with(['attributes.attribute', 'category', 'brand'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(function ($product) use ($ids) {
                return array_search($product->id, $ids);
            });

        $attributes = Attribute::orderBy('id')->get();

        return view('compare.index', [
            'products'   => $products,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Thêm sản phẩm vào so sánh
     */
    public function add(Product $product)
    {
        $compare = session()->get('compare', []);

        if (!in_array($product->id, $compare)) {
            $compare[] = $product->id;
        }

        // Chỉ giữ tối đa 3 sản phẩm
        if (count($compare) > 3) {
            array_shift($compare);
        }

        session()->put('compare', $compare);

        return redirect()
            ->route('compare')
            ->with('success', 'Đã thêm sản phẩm vào danh sách so sánh.');
    }

    /**
     * Xóa sản phẩm khỏi so sánh
     */
    public function remove(Product $product)
    {
        $compare = session()->get('compare', []);

        $compare = array_values(array_filter($compare, function ($id) use ($product) {
            return $id != $product->id;
        }));

        session()->put('compare', $compare);

        return redirect()
            ->route('compare')
            ->with('success', 'Đã xóa sản phẩm khỏi danh sách so sánh.');
    }
}
