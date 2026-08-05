<?php

namespace App\Services\Concerns;

use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

/**
 * Danh sách tên thuộc tính / slug category / slug brand THẬT trong DB — lấy động thay vì
 * hardcode trong prompt, để AI tự hiểu đúng dữ liệu hiện có mà không cần sửa code mỗi khi
 * thêm category/brand/thuộc tính mới. Cache 6 tiếng vì các bảng này gần như không đổi.
 *
 * Dùng CHUNG cho mọi service gọi AI để lọc/parse sản phẩm (GeminiChatService, AiSearchParser).
 * Trước đây AiSearchParser (thanh search header) hardcode 5 slug category cố định trong khi
 * GeminiChatService (chatbot) đã lấy động từ DB — khiến 2 nơi lọc category khác nhau và search
 * trên header bị sai với sản phẩm ngoài 5 slug cũ. Gộp về đây để chỉ có DUY NHẤT một nguồn sự thật.
 */
trait HasStoreTaxonomy
{
    protected function allowedAttributeNames(): array
    {
        return Cache::remember('chatbot:attribute_names', now()->addHours(6), function () {
            return Attribute::orderBy('name')->pluck('name')->all();
        });
    }

    protected function allowedCategorySlugs(): array
    {
        return Cache::remember('chatbot:category_slugs', now()->addHours(6), function () {
            return Category::categories()->active()->pluck('slug')->all();
        });
    }

    protected function allowedBrandSlugs(): array
    {
        return Cache::remember('chatbot:brand_slugs', now()->addHours(6), function () {
            return Category::brands()->active()->pluck('slug')->all();
        });
    }
}