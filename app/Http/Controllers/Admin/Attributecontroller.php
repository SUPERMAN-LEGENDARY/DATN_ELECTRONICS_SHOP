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
            ->get(['id', 'name']);

        return response()->json($attrs);
    }

    // ─── Thêm mới (AJAX) ─────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:attributes,name',
        ]);

        $attr = Attribute::create(['name' => trim($request->name)]);

        return response()->json([
            'id'         => $attr->id,
            'name'       => $attr->name,
            'used_count' => 0,
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