<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ElectronicShop (Preview Mode, không cần Controller/DB)
|--------------------------------------------------------------------------
*/

/* ===================== TRANG CHỦ ===================== */
Route::get('/', function () {
    return view('pages.home');
})->name('home');

/* ===================== SẢN PHẨM ===================== */
Route::get('/san-pham', function () {
    return view('pages.products');
})->name('products.index');

Route::get('/san-pham/{slug}', function (string $slug) {
    return view('pages.product-detail');
})->name('products.show');

/* ===================== GIỎ HÀNG ===================== */
Route::get('/gio-hang', function () {
    return view('pages.cart');
})->name('cart.index');

Route::post('/gio-hang/them', function () {
    return redirect()->route('cart.index');
})->name('cart.add');

Route::get('/gio-hang/xoa/{id}', function (int $id) {
    return redirect()->route('cart.index');
})->name('cart.remove');

/* ===================== THANH TOÁN ===================== */
Route::get('/thanh-toan', function () {
    return view('pages.checkout');
})->name('checkout');

Route::post('/thanh-toan/xu-ly', function () {
    return redirect()->route('thank-you');
})->name('checkout.process');

/* ===================== CẢM ƠN ===================== */
Route::get('/cam-on', function () {
    return view('pages.thank-you');
})->name('thank-you');

/* ===================== TIN TỨC ===================== */
Route::get('/tin-tuc', function () {
    return view('pages.news');
})->name('news.index');

Route::get('/tin-tuc/{slug}', function (string $slug) {
    $news = (object)[
        'title'      => 'So sánh iPhone 15 và iPhone 14: Nâng cấp đáng giá?',
        'author'     => 'ElectronicShop Team',
        'created_at' => now(),
        'views'      => 12500,
        'likes'      => 128,
        'dislikes'   => 12,
        'thumbnail'  => null,
        'content'    => null,
        'category'   => (object)['name' => 'CÔNG NGHỆ'],
    ];
    return view('pages.news-detail', compact('news'));
})->name('news.show');

/* ===================== LIÊN HỆ ===================== */
Route::get('/lien-he', function () {
    return view('pages.contact');
})->name('contact');

Route::post('/lien-he', function () {
    return redirect()->route('contact')
        ->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong vòng 24 giờ.');
})->name('contact.send');

/* ===================== ĐĂNG NHẬP / ĐĂNG KÝ ===================== */
Route::get('/dang-nhap', function () {
    return view('pages.auth');
})->name('login');

// POST đăng nhập — giả lập thành công, chuyển về trang chủ
Route::post('/dang-nhap', function () {
    return redirect()->route('home');
});

Route::get('/dang-ky', function () {
    return view('pages.auth');
})->name('register');

// POST đăng ký — giả lập thành công, chuyển về trang chủ
Route::post('/dang-ky', function () {
    return redirect()->route('home');
});

Route::post('/dang-xuat', function () {
    return redirect()->route('home');
})->name('logout');

// Quên mật khẩu — trỏ về trang auth để tránh lỗi route not found
Route::get('/quen-mat-khau', function () {
    return view('pages.forgot-password');
})->name('password.request');

Route::post('/quen-mat-khau', function () {
    return redirect()->route('password.request')
        ->with('success', 'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn!');
})->name('password.send');

// Social login — trỏ về trang auth (chưa tích hợp thật)
Route::get('/auth/google', function () {
    return redirect()->route('home');
})->name('auth.google');

Route::get('/auth/facebook', function () {
    return redirect()->route('home');
})->name('auth.facebook');

/* ===================== TÀI KHOẢN ===================== */
Route::get('/tai-khoan', function () {
    return view('pages.account.profile');
})->name('account.profile');

Route::put('/tai-khoan', function () {
    return redirect()->route('account.profile')
        ->with('success', 'Cập nhật thông tin thành công!');
})->name('account.profile.update');

Route::get('/tai-khoan/don-hang', function () {
    return view('pages.account.profile');
})->name('account.orders');

/* ===================== YÊU THÍCH ===================== */
Route::get('/yeu-thich', function () {
    return view('pages.products');
})->name('wishlist');
Route::post('/gio-hang/mua-ngay', function () {
    return redirect()->route('checkout');
})->name('cart.buy-now');

Route::get('/so-sanh', function () {
    return view('pages.compare');
})->name('products.compare');