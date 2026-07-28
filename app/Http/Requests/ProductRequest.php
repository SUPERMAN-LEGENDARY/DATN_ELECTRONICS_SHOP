<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bảo vệ bằng middleware admin ở route
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:200',
            'category_id'      => 'required|exists:categories,id',
            'brand_id'         => 'required|exists:categories,id',
            'description'      => 'nullable|string',
            'cost_price'       => 'required|numeric|min:1',
            'list_price'       => 'required|numeric|min:0',
            'price'            => 'required|numeric|min:0',
            'stock'            => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
            'images'           => 'nullable|array|max:6',
            'images.*'         => 'image|mimes:jpg,jpeg,png,webp|max:3072',
            'attributes'       => 'nullable|array',
            'attributes.*'     => 'nullable|string|max:255',
            // Biến thể
            'has_variants'            => 'nullable|boolean',
            'variants'                => 'nullable|array',
            'variants.*.id'           => 'nullable|integer|exists:product_variants,id',
            'variants.*.cost_price'   => 'required_with:variants|numeric|min:1',
            'variants.*.list_price'   => 'required_with:variants|numeric|min:0',
            'variants.*.price'        => 'required_with:variants|numeric|min:0',
            'variants.*.stock'        => 'nullable|integer|min:0',
            'variants.*.is_active'    => 'nullable|boolean',
            'variants.*.attrs'        => 'nullable|array',
            'variants.*.attrs.*'      => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Tên sản phẩm không được bỏ trống.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists'   => 'Danh mục không hợp lệ.',
            'brand_id.required'    => 'Vui lòng chọn thương hiệu.',
            'brand_id.exists'      => 'Thương hiệu không hợp lệ.',
            'cost_price.required'  => 'Giá vốn không được bỏ trống.',
            'cost_price.numeric'   => 'Giá vốn phải là số.',
            'list_price.required'  => 'Giá niêm yết không được bỏ trống.',
            'list_price.numeric'   => 'Giá niêm yết phải là số.',
            'price.required'       => 'Giá sản phẩm không được bỏ trống.',
            'price.numeric'        => 'Giá phải là số.',
            'images.*.image'       => 'File tải lên phải là ảnh.',
            'images.*.max'         => 'Mỗi ảnh không được vượt quá 3MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active'        => $this->boolean('is_active'),
            'stock'            => $this->input('stock', 0),
            'has_variants'     => $this->boolean('has_variants'),
        ]);

        // Đặt stock mặc định = 0 cho từng variant nếu không truyền
        $variants = $this->input('variants', []);
        foreach ($variants as $idx => $v) {
            if (!isset($v['stock'])) {
                $variants[$idx]['stock'] = 0;
            }
        }
        if (!empty($variants)) {
            $this->merge(['variants' => $variants]);
        }
    }
}