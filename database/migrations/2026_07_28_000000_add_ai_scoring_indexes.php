<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * customer:score-ai-profiles chạy 1 query riêng theo user_id cho MỖI khách hàng
 * trên các bảng ai_sessions / product_views. Bảng ai_sessions hiện KHÔNG có index
 * nào trên user_id -> mỗi khách = 1 lần full table scan toàn bộ ai_sessions.
 * Đây là nguyên nhân chính khiến "chấm điểm" chạy chậm khi dữ liệu lớn dần.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Trước đây không có index nào ngoài session_token -> full scan mỗi lần
        // where('user_id', ...)->where('updated_at', ...)
        Schema::table('ai_sessions', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'ai_sessions_user_id_updated_at_index');
        });

        // Index hiện có là (user_id, product_id) — tốt cho lọc theo sản phẩm nhưng
        // không tối ưu cho where('user_id')->where('created_at', '>=', ...) mà job
        // đang dùng. Thêm (user_id, created_at) để MySQL dùng index-range-scan
        // thay vì lọc created_at bằng cách đọc từng dòng sau khi lookup user_id.
        Schema::table('product_views', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'product_views_user_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('ai_sessions', function (Blueprint $table) {
            $table->dropIndex('ai_sessions_user_id_updated_at_index');
        });

        Schema::table('product_views', function (Blueprint $table) {
            $table->dropIndex('product_views_user_id_created_at_index');
        });
    }
};
