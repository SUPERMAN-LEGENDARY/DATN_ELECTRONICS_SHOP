<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// ─── TRANG CHỦ ────────────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ── Dashboard admin ────────────────────────────────────────────
Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── STOREFRONT: SẢN PHẨM ────────────────────────────────────────
Route::prefix('san-pham')->name('products.')->group(function () {
    Route::get('/',               [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}',         [ProductController::class, 'show'])->name('show');
    Route::post('/{id}/danh-gia', [ProductController::class, 'storeReview'])
        ->name('review')
        ->middleware('auth');
});

// ─── PROFILE ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── GIỎ HÀNG ─────────────────────────────────────────────────────
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/',               [CartController::class, 'index'])->name('index');
    Route::post('/them',          [CartController::class, 'add'])->name('add');
    Route::post('/mua-ngay',      [CartController::class, 'buyNow'])->name('buy-now');
    Route::patch('/{productId}',  [CartController::class, 'update'])->name('update');
    Route::delete('/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/',            [CartController::class, 'clear'])->name('clear');
});

// ─── ADMIN ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {

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

    // ── Phân quyền (chỉ admin) ────────────────────────────────────
    Route::middleware('role:admin')->prefix('nguoi-dung')->name('users.')->group(function () {
        Route::get('/',                            [AdminUserController::class, 'index'])->name('index');
        Route::patch('/{user}/role',               [AdminUserController::class, 'updateRole'])->name('update-role');
        Route::patch('/{user}/toggle-active',      [AdminUserController::class, 'toggleActive'])->name('toggle-active');
    });
});

// ─── TIN TỨC STOREFRONT (placeholder) ────────────────────────────
Route::get('/tin-tuc', fn() => redirect('/'))->name('news.index');
Route::get('/tin-tuc/{slug}', fn() => redirect('/'))->name('news.show');

require __DIR__ . '/auth.php';