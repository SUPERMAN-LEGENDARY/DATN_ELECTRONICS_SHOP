<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\AttributeController as AdminAttributeController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── TRANG CHỦ ────────────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ─── STOREFRONT: SẢN PHẨM ────────────────────────────────────────
Route::prefix('san-pham')->name('products.')->group(function () {
    Route::get('/',               [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}',         [ProductController::class, 'show'])->name('show');
    Route::post('/{id}/danh-gia', [ProductController::class, 'storeReview'])
        ->name('review')
        ->middleware('auth');
});

// ─── SO SÁNH SẢN PHẨM ─────────────────────────────────────────────
Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::post('/compare/add/{product}', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/remove/{product}', [CompareController::class, 'remove'])->name('compare.remove');

Route::view('/about', 'about')->name('about.index');

// ─── PROFILE ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ============================================================
    // HỒ SƠ
    // URL: /profile
    // ============================================================
    Route::get('/profile', [ProfileController::class, 'myProfile'])
        ->name('profile');

    // ============================================================
    // THÔNG TIN CỦA TÔI
    // ============================================================
    Route::get('/profile/account', [ProfileController::class, 'account'])
        ->name('profile.account');

    // Alias cũ
    Route::get('/profile/edit', [ProfileController::class, 'myProfile'])
        ->name('profile.edit');

    // ============================================================
    // CẬP NHẬT THÔNG TIN
    // ============================================================
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // ============================================================
    // XÓA TÀI KHOẢN
    // ============================================================
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ============================================================
    // SỔ ĐỊA CHỈ
    // ============================================================
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])
        ->name('profile.address.store');

    Route::patch('/profile/address/{address}', [ProfileController::class, 'updateAddress'])
        ->name('profile.address.update');

    Route::delete('/profile/address/{address}', [ProfileController::class, 'destroyAddress'])
        ->name('profile.address.destroy');

    Route::patch('/profile/address/{address}/default', [ProfileController::class, 'setDefaultAddress'])
        ->name('profile.address.default');

    // ============================================================
    // ĐỔI MẬT KHẨU
    // ============================================================
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    // ============================================================
    // ĐƠN HÀNG
    // ============================================================
    Route::get('/profile/order', [OrderController::class, 'index'])
        ->name('profile.order');

    Route::get('/profile/order/{order}', [OrderController::class, 'show'])
        ->name('profile.order.show');

    Route::patch('/profile/order/{order}/cancel', [OrderController::class, 'cancel'])
        ->name('profile.order.cancel');

    Route::patch('/profile/order/{order}/received', [OrderController::class, 'received'])
        ->name('profile.order.received');

    Route::post('/profile/order/{order}/reorder', [OrderController::class, 'reorder'])
        ->name('profile.order.reorder');

    Route::post('/profile/order/{order}/review', [ReviewController::class, 'store'])
        ->name('profile.review.store');

    // ============================================================
    // VOUCHER
    // ============================================================
    Route::get('/profile/voucher', [VoucherController::class, 'index'])
        ->name('profile.voucher');

    // ============================================================
    // ĐÁNH GIÁ
    // ============================================================
    Route::get('/profile/review', [ReviewController::class, 'index'])
        ->name('profile.review');

    // ============================================================
    // YÊU THÍCH
    // ============================================================
    Route::get('/profile/yeu-thich', [WishlistController::class, 'index'])
        ->name('profile.wishlist');

    Route::post('/yeu-thich/{product}', [WishlistController::class, 'toggle'])
        ->name('wishlist.toggle');

    // ============================================================
    // THÔNG BÁO
    // ============================================================
    Route::prefix('thong-bao')->name('notifications.')->group(function () {

        Route::get('/', [NotificationController::class, 'list'])
            ->name('list');

        Route::post('/{id}/doc', [NotificationController::class, 'markRead'])
            ->name('mark-read');

        Route::post('/doc-het', [NotificationController::class, 'markAllRead'])
            ->name('mark-all-read');
    });
});

// ─── ĐƠN HÀNG (ngoài trang tài khoản) ──────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::patch('/order/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::patch('/order/{order}/received', [OrderController::class, 'received'])->name('order.received');
    Route::post('/order/{order}/reorder', [OrderController::class, 'reorder'])->name('order.reorder');
});

// ─── GIỎ HÀNG ─────────────────────────────────────────────────────
Route::prefix('gio-hang')->name('cart.')->middleware(\App\Http\Middleware\CustomerOnlyMiddleware::class)->group(function () {
    Route::get('/',               [CartController::class, 'index'])->name('index');
    Route::post('/them',          [CartController::class, 'add'])->name('add');
    Route::post('/mua-ngay',      [CartController::class, 'buyNow'])->name('buy-now');
    Route::patch('/{key}',  [CartController::class, 'update'])->name('update');
    Route::delete('/{key}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/',            [CartController::class, 'clear'])->name('clear');
});

Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
// ─── THANH TOÁN (CHECKOUT) ────────────────────────────────────────
Route::prefix('thanh-toan')->name('checkout.')->group(function () {

    // Các bước đặt hàng từ giỏ hàng vẫn bắt buộc đăng nhập
    Route::middleware(['auth', \App\Http\Middleware\CustomerOnlyMiddleware::class])->group(function () {
        Route::get('/',             [\App\Http\Controllers\CheckoutController::class, 'index'])->name('index');
        Route::post('/',            [\App\Http\Controllers\CheckoutController::class, 'store'])->name('store');
        Route::post('/kiem-tra-ma', [\App\Http\Controllers\CheckoutController::class, 'checkVoucher'])->name('check-voucher');
    });

    // ── Các route dưới đây KHÔNG bắt buộc đăng nhập ──────────────────
    // Lý do: khách bấm nút "Thanh toán ngay" trong email nhắc thanh toán
    // (OrderPaymentReminderMail) hoặc được MoMo redirect về sau khi thanh
    // toán, ở thời điểm đó trình duyệt có thể chưa có phiên đăng nhập.
    // Bảo mật được đảm bảo bằng chữ ký URL (signed) thay vì bằng session:
    //   - momo.retry: link chỉ được tạo hợp lệ từ hệ thống (trong mail),
    //     có hạn sử dụng, middleware 'signed' sẽ chặn nếu bị sửa/hết hạn.
    //   - success: chấp nhận nếu link có chữ ký hợp lệ (được sinh ra ngay
    //     sau khi MoMo xác nhận thanh toán) HOẶC nếu người dùng đăng nhập
    //     đúng là chủ đơn hàng (xem CheckoutController::success()).
    Route::get('/momo/return',        [\App\Http\Controllers\CheckoutController::class, 'momoReturn'])->name('momo.return');
    Route::get('/momo/lai/{order}',   [\App\Http\Controllers\CheckoutController::class, 'retryMomoPayment'])->name('momo.retry')->middleware('signed');
    Route::get('/thanh-cong/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
});

// ─── ADMIN ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {

    // ── Thống kê / Dashboard (admin + staff) ────────────────────────
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // ── Sản phẩm (admin + staff) ──────────────────────────────────
    Route::prefix('san-pham')->name('products.')->group(function () {
        Route::get('/',                          [AdminProductController::class, 'index'])->name('index');
        Route::get('/them',                      [AdminProductController::class, 'create'])->name('create');
        Route::post('/',                         [AdminProductController::class, 'store'])->name('store');
        Route::get('/thung-rac',                 [AdminProductController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',        [AdminProductController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',          [AdminProductController::class, 'emptyTrash'])->name('empty-trash');
        Route::get('/{product}/sua',             [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{product}',                 [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{product}',              [AdminProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{product}/them-so-luong', [AdminProductController::class, 'addStock'])->name('add-stock');
        Route::patch('/{id}/khoi-phuc',          [AdminProductController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',     [AdminProductController::class, 'forceDelete'])->name('force-delete');
        Route::get('/kiem-tra-ten', [AdminProductController::class, 'checkName'])
                ->name('check-name');
    });

    // ── Danh mục (admin + staff) ──────────────────────────────────
    Route::prefix('danh-muc')->name('categories.')->group(function () {
        Route::get('/',                          [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/them',                      [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/',                         [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/thung-rac',                 [AdminCategoryController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',        [AdminCategoryController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',          [AdminCategoryController::class, 'emptyTrash'])->name('empty-trash');
        Route::get('/{category}/sua',            [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}',                [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}',             [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{id}/khoi-phuc',          [AdminCategoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',     [AdminCategoryController::class, 'forceDelete'])->name('force-delete');
    });

    // ── Thuộc tính sản phẩm ───────────────────────────────────────
    Route::prefix('thuoc-tinh')->name('attributes.')->group(function () {
        Route::get('/list',            [AdminAttributeController::class, 'list'])->name('list');
        Route::post('/',               [AdminAttributeController::class, 'store'])->name('store');
        Route::patch('/{attribute}/toggle-variant', [AdminAttributeController::class, 'toggleVariant'])->name('toggle-variant');
        Route::delete('/{attribute}',  [AdminAttributeController::class, 'destroy'])->name('destroy');
    });

    // ── Banner trang chủ (admin + staff) ──────────────────────────
    Route::prefix('banner')->name('banners.')->group(function () {
        Route::get('/',                          [AdminBannerController::class, 'index'])->name('index');
        Route::get('/them',                      [AdminBannerController::class, 'create'])->name('create');
        Route::post('/',                         [AdminBannerController::class, 'store'])->name('store');
        Route::get('/thung-rac',                 [AdminBannerController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',        [AdminBannerController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',          [AdminBannerController::class, 'emptyTrash'])->name('empty-trash');
        Route::get('/{banner}/sua',              [AdminBannerController::class, 'edit'])->name('edit');
        Route::put('/{banner}',                  [AdminBannerController::class, 'update'])->name('update');
        Route::delete('/{banner}',               [AdminBannerController::class, 'destroy'])->name('destroy');
        Route::patch('/{banner}/toggle-active',  [AdminBannerController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{id}/khoi-phuc',          [AdminBannerController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',     [AdminBannerController::class, 'forceDelete'])->name('force-delete');
    });

    // ── Sự kiện / Khuyến mãi theo mùa (admin + staff) ──────────────
    Route::prefix('su-kien')->name('events.')->group(function () {
        Route::get('/',                          [AdminEventController::class, 'index'])->name('index');
        Route::get('/them',                      [AdminEventController::class, 'create'])->name('create');
        Route::post('/',                         [AdminEventController::class, 'store'])->name('store');
        Route::get('/thung-rac',                 [AdminEventController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',        [AdminEventController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',          [AdminEventController::class, 'emptyTrash'])->name('empty-trash');

        // 2. Các route động có tham số ({event}) phải đặt ở phía dưới
        Route::get('/{event}',                   [AdminEventController::class, 'show'])->name('show');
        Route::get('/{event}/sua',               [AdminEventController::class, 'edit'])->name('edit');
        Route::put('/{event}',                   [AdminEventController::class, 'update'])->name('update');
        Route::delete('/{event}',                [AdminEventController::class, 'destroy'])->name('destroy');
        Route::patch('/{event}/toggle-active',   [AdminEventController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/bulk-toggle',              [AdminEventController::class, 'bulkToggle'])->name('bulk-toggle');
        Route::patch('/{id}/khoi-phuc',          [AdminEventController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',     [AdminEventController::class, 'forceDelete'])->name('force-delete');
    });

    // ── Đánh giá (admin + staff) ──────────────────────────────────
    Route::prefix('danh-gia')->name('reviews.')->group(function () {
        Route::get('/',                          [AdminReviewController::class, 'index'])->name('index');
        Route::patch('/{review}/toggle-visible', [AdminReviewController::class, 'toggleVisible'])->name('toggle-visible');
        Route::post('/{review}/reply',           [AdminReviewController::class, 'reply'])->name('reply');
        Route::delete('/{review}/reply',         [AdminReviewController::class, 'deleteReply'])->name('delete-reply');
        Route::delete('/{review}',               [AdminReviewController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-toggle',              [AdminReviewController::class, 'bulkToggle'])->name('bulk-toggle');
    });

    // ── Tin tức (admin + staff) ───────────────────────────────────
    Route::prefix('tin-tuc')->name('news.')->group(function () {
        Route::get('/',                              [AdminNewsController::class, 'index'])->name('index');
        Route::get('/them',                          [AdminNewsController::class, 'create'])->name('create');
        Route::post('/',                             [AdminNewsController::class, 'store'])->name('store');
        Route::get('/thung-rac',                      [AdminNewsController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',             [AdminNewsController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',               [AdminNewsController::class, 'emptyTrash'])->name('empty-trash');

        // Danh mục tin tức
        Route::get('/danh-muc',                     [AdminNewsController::class, 'categories'])->name('categories');
        Route::post('/danh-muc',                    [AdminNewsController::class, 'storeCategory'])->name('categories.store');
        Route::get('/danh-muc/thung-rac',            [AdminNewsController::class, 'categoriesTrash'])->name('categories.trash');
        Route::patch('/danh-muc/khoi-phuc-tat-ca',   [AdminNewsController::class, 'restoreAllCategories'])->name('categories.restore-all');
        Route::delete('/danh-muc/don-thung-rac',     [AdminNewsController::class, 'emptyTrashCategories'])->name('categories.empty-trash');
        Route::put('/danh-muc/{newsCategory}',      [AdminNewsController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/danh-muc/{newsCategory}',   [AdminNewsController::class, 'destroyCategory'])->name('categories.destroy');
        Route::patch('/danh-muc/{id}/khoi-phuc',     [AdminNewsController::class, 'restoreCategory'])->name('categories.restore');
        Route::delete('/danh-muc/{id}/xoa-vinh-vien', [AdminNewsController::class, 'forceDeleteCategory'])->name('categories.force-delete');

        Route::get('/{news}/sua',                    [AdminNewsController::class, 'edit'])->name('edit');
        Route::put('/{news}',                        [AdminNewsController::class, 'update'])->name('update');
        Route::delete('/{news}',                     [AdminNewsController::class, 'destroy'])->name('destroy');
        Route::patch('/{news}/toggle-active',        [AdminNewsController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{id}/khoi-phuc',               [AdminNewsController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',          [AdminNewsController::class, 'forceDelete'])->name('force-delete');
    });

    // ── Quản lý đơn hàng (admin + staff) ──────────────────────────
    Route::prefix('don-hang')->name('orders.')->group(function () {
        // ── Route tĩnh ──
        Route::get('/',                            [AdminOrderController::class, 'index'])->name('index');
        Route::get('/thung-rac',              [AdminOrderController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',     [AdminOrderController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',       [AdminOrderController::class, 'emptyTrash'])->name('empty-trash');
        Route::get('/them',                        [AdminOrderController::class, 'create'])->name('create');
        Route::post('/',                           [AdminOrderController::class, 'store'])->name('store');

        // ── Route động ──
        Route::get('/{order}',                     [AdminOrderController::class, 'show'])->name('show');
        Route::get('/{order}/sua',                 [AdminOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}',                     [AdminOrderController::class, 'update'])->name('update');
        Route::patch('/{order}/status',            [AdminOrderController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{order}/cancel',            [AdminOrderController::class, 'cancel'])->name('cancel');
        Route::delete('/{order}',                  [AdminOrderController::class, 'destroy'])->name('destroy');

        // ── Route thùng rác có tham số động ──
        Route::patch('/{id}/khoi-phuc',       [AdminOrderController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',  [AdminOrderController::class, 'forceDelete'])->name('force-delete');
    });
    // ── Khách hàng tiềm năng / AI Leads (admin + staff) ────────────
    Route::prefix('khach-hang-tiem-nang')->name('leads.')->group(function () {
        Route::get('/',                              [AdminLeadController::class, 'index'])->name('index');
        Route::get('/{user}',                         [AdminLeadController::class, 'show'])->name('show');
        Route::post('/{user}/de-xuat-thu-cong',       [AdminLeadController::class, 'updateSuggestions'])->name('update-suggestions');
        Route::post('/{user}/tang-voucher',           [AdminLeadController::class, 'giftVoucher'])->name('gift-voucher');
        Route::post('/{user}/tinh-lai-diem',          [AdminLeadController::class, 'recalculate'])->name('recalculate');

        // ── Chấm điểm AI lại cho TOÀN BỘ khách hàng (chạy nền qua Queue) ──
        // Lưu ý: đặt 2 route này TRƯỚC nếu sau này thêm route động dạng
        // /{user}/... khác để tránh xung đột, nhưng vì đường dẫn ở đây
        // ("tinh-lai-diem-toan-bo") không trùng pattern {user} nên đặt ở
        // đâu trong group cũng không ảnh hưởng.
        Route::post('/tinh-lai-diem-toan-bo',         [AdminLeadController::class, 'recalculateAll'])->name('recalculate-all');
        Route::get('/tinh-lai-diem-toan-bo/trang-thai', [AdminLeadController::class, 'recalculateAllStatus'])->name('recalculate-all.status');
    });

    // ── Quản lý voucher (admin + staff) ──────────────────────────
    Route::prefix('voucher')->name('vouchers.')->group(function () {
        // ── Các route tĩnh (không có tham số) ──
        Route::get('/',                            [AdminVoucherController::class, 'index'])->name('index');
        Route::get('/them',                        [AdminVoucherController::class, 'create'])->name('create');
        Route::post('/',                           [AdminVoucherController::class, 'store'])->name('store');

        // ── Thùng rác (route tĩnh) ──
        Route::get('/thung-rac',              [AdminVoucherController::class, 'trash'])->name('trash');
        Route::patch('/khoi-phuc-tat-ca',     [AdminVoucherController::class, 'restoreAll'])->name('restore-all');
        Route::delete('/don-thung-rac',       [AdminVoucherController::class, 'emptyTrash'])->name('empty-trash');

        // ── Các route có tham số động (đặt xuống dưới) ──
        Route::get('/{voucher}/sua',          [AdminVoucherController::class, 'edit'])->name('edit');
        Route::put('/{voucher}',              [AdminVoucherController::class, 'update'])->name('update');
        Route::delete('/{voucher}',           [AdminVoucherController::class, 'destroy'])->name('destroy');
        Route::patch('/{voucher}/toggle',     [AdminVoucherController::class, 'toggleActive'])->name('toggle-active');

        // ── Route thùng rác có tham số động (đặt cuối) ──
        Route::patch('/{id}/khoi-phuc',       [AdminVoucherController::class, 'restore'])->name('restore');
        Route::delete('/{id}/xoa-vinh-vien',  [AdminVoucherController::class, 'forceDelete'])->name('force-delete');
    });

    // ── Liên hệ (admin + staff) ──────────────────────────────────
    Route::prefix('lien-he')->name('contacts.')->group(function () {
        Route::get('/',                               [AdminContactController::class, 'index'])->name('index');
        Route::get('/{contact}',                     [AdminContactController::class, 'show'])->name('show');
        Route::post('/{contact}/phan-hoi',           [AdminContactController::class, 'reply'])->name('reply');
        Route::patch('/{contact}/trang-thai',        [AdminContactController::class, 'updateStatus'])->name('status');
        Route::delete('/{contact}',                  [AdminContactController::class, 'destroy'])->name('destroy');
    });

    // ── Phân quyền (chỉ admin) ────────────────────────────────────
    Route::middleware('role:admin')->prefix('nguoi-dung')->name('users.')->group(function () {
        Route::get('/',                              [AdminUserController::class, 'index'])->name('index');
        Route::get('/tao-moi',                       [AdminUserController::class, 'create'])->name('create');
        Route::post('/',                             [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/sua',                    [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}',                        [AdminUserController::class, 'update'])->name('update');
        Route::patch('/{user}/role',                 [AdminUserController::class, 'updateRole'])->name('update-role');
        Route::patch('/{user}/toggle-active',        [AdminUserController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{user}',                     [AdminUserController::class, 'destroy'])->name('destroy');
        Route::patch('/trash/{id}/restore',          [AdminUserController::class, 'restore'])->name('restore');
        Route::delete('/trash/{id}/xoa-vinh-vien',  [AdminUserController::class, 'forceDelete'])->name('force-delete');
    });
});

// ─── TIN TỨC STOREFRONT ───────────────────────────────────────────
Route::prefix('tin-tuc')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/danh-muc/{slug}', [NewsController::class, 'category'])->name('category');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// ─── LIÊN HỆ ────────────────────────────────────────────────────────
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact.index');
Route::post('/lien-he', [ContactController::class, 'send'])->name('contact.send');
Route::post('/dang-ky-nhan-tin', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

require __DIR__ . '/auth.php';
// Admin Login (Sử dụng chung xử lý đăng nhập với web, nhưng giao diện riêng)
Route::view('/admin/login', 'admin.auth.login')->name('admin.login')->middleware('guest');
