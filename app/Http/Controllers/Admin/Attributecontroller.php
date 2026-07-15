<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    // ─── Trả JSON danh sách (dùng cho modal AJAX) ─────────────────

    public function list()
    {
        $attrs = Attribute::orderBy('name')
            ->withCount('productAttributes as used_count')
            ->get(['id', 'name', 'is_variant']);

        return response()->json($attrs);
    }

    // ─── Thêm mới (AJAX) ─────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100|unique:attributes,name',
            'is_variant' => 'nullable|boolean',
        ]);

        $attr = Attribute::create([
            'name'       => trim($request->name),
            'is_variant' => $request->boolean('is_variant', true),
        ]);

        return response()->json([
            'id'         => $attr->id,
            'name'       => $attr->name,
            'is_variant' => $attr->is_variant,
            'used_count' => 0,
        ]);
    }

    // ─── Đổi "Chính" ⇄ "Phụ" (AJAX) ────────────────────────────────
    // Chính  = tạo nút chọn lựa chọn mua hàng (vd: Màu sắc, Dung lượng)
    // Phụ    = chỉ hiện trong bảng "Thông số kỹ thuật"

    public function toggleVariant(Attribute $attribute)
    {
        $attribute->is_variant = ! $attribute->is_variant;
        $attribute->save();

        return response()->json([
            'id'         => $attribute->id,
            'is_variant' => $attribute->is_variant,
        ]);
    }

    // ─── Xóa (AJAX) ──────────────────────────────────────────────

    public function destroy(Attribute $attribute)
    {
        $usedCount = $attribute->productAttributes()->count();

        if ($usedCount > 0) {
            return response()->json([
                'error' => "Không thể xóa: \"{$attribute->name}\" đang được dùng bởi {$usedCount} sản phẩm.",
            ], 422);
        }

        $attribute->delete();

        return response()->json(['success' => true]);
    }
}
