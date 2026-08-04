<?php

/**
 * Thông tin chính sách cửa hàng dùng để chatbot trả lời các câu hỏi về vận chuyển,
 * thanh toán, bảo hành, đổi trả, cửa hàng... một cách CHÍNH XÁC thay vì chỉ nói chung
 * chung "liên hệ CSKH". Sửa nội dung bên dưới cho đúng với thực tế shop của bạn.
 *
 * GeminiChatService sẽ đưa nguyên văn các dòng này vào prompt mỗi khi khách hỏi ở
 * intent "support" hoặc "buy" (chỉ trả lời đúng những gì có ở đây, không tự bịa thêm).
 * Key có thể đặt tên tự do bằng tiếng Việt, Claude/Gemini sẽ đọc trực tiếp.
 */

return [
    'policies' => [
        'Vận chuyển' => 'Giao hàng toàn quốc qua đối tác vận chuyển, thời gian dự kiến 2-5 ngày làm việc tuỳ khu vực. Phí ship tính theo địa chỉ khi đặt hàng.',
        'Giao hỏa tốc' => 'Hỗ trợ giao hỏa tốc trong ngày với nội thành các thành phố lớn (Hà Nội, TP.HCM, Đà Nẵng), phụ phí hỏa tốc hiển thị khi checkout.',
        'Thanh toán' => 'Hỗ trợ COD (thanh toán khi nhận hàng), chuyển khoản ngân hàng, và ví điện tử. Có thể xem đầy đủ phương thức tại bước thanh toán.',
        'Trả góp' => 'Hỗ trợ trả góp qua các đối tác tài chính/thẻ tín dụng liên kết, cần CMND/CCCD và thông tin thu nhập cơ bản. Nhân viên tư vấn sẽ hỗ trợ hồ sơ cụ thể.',
        'Đổi trả' => 'Đổi trả trong 7 ngày kể từ khi nhận hàng nếu máy còn nguyên seal/phụ kiện/hộp và lỗi do nhà sản xuất; không áp dụng nếu do người dùng làm hư hỏng.',
        'Bảo hành' => 'Bảo hành chính hãng 12 tháng theo phiếu bảo hành đi kèm máy; bảo hành 1 đổi 1 trong 30 ngày đầu nếu lỗi phần cứng do nhà sản xuất.',
        'Xuất xứ / hàng chính hãng' => 'Toàn bộ sản phẩm là hàng chính hãng, có tem/phiếu bảo hành chính hãng đi kèm; thông tin xuất xứ cụ thể xem trong mô tả từng sản phẩm.',
        'Hotline hỗ trợ' => 'Gọi hotline CSKH hoặc chat với nhân viên qua fanpage/website để được hỗ trợ chi tiết theo từng đơn hàng.',
        // Thêm các mục khác nếu cần, ví dụ: 'Địa chỉ cửa hàng', 'Giờ mở cửa', 'Kiểm tra hàng trước khi nhận'...
    ],
];