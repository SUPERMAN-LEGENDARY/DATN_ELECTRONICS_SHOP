<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\ProfileController;
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

// ─── DASHBOARD ────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── PROFILE ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


use App\Http\Controllers\CartController;

// ─── GIỎ HÀNG ─────────────────────────────────────────────────────
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/',                    [CartController::class, 'index'])->name('index');
    Route::post('/them',               [CartController::class, 'add'])->name('add');
    Route::post('/mua-ngay',           [CartController::class, 'buyNow'])->name('buy-now');
    Route::patch('/{productId}',       [CartController::class, 'update'])->name('update');
    Route::delete('/{productId}',      [CartController::class, 'remove'])->name('remove');
    Route::delete('/',                 [CartController::class, 'clear'])->name('clear');
});

// ─── ADMIN: QUẢN LÝ SẢN PHẨM ─────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('san-pham')->name('products.')->group(function () {
        Route::get('/',                          [AdminProductController::class, 'index'])->name('index');
        Route::get('/them',                      [AdminProductController::class, 'create'])->name('create');
        Route::post('/',                         [AdminProductController::class, 'store'])->name('store');
        Route::get('/{product}/sua',             [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{product}',                 [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{product}',              [AdminProductController::class, 'destroy'])->name('destroy');
        Route::patch('/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('toggle-active');
    });
});

    // Thêm vào web.php trước require auth.php
Route::get('/tin-tuc', fn() => redirect('/'))->name('news.index');
Route::get('/tin-tuc/{slug}', fn() => redirect('/'))->name('news.show');

require __DIR__.'/auth.php';