<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
/**
 * ============================================================
 * HỒ SƠ
 * URL: /profile
 * Route: profile
 * View: profile/my-profile.blade.php
 * ============================================================
 */
public function myProfile(Request $request): View
{
    $user = $request->user();

    $userId = $user->id;

    // Tổng đơn hàng
    $totalOrders = Order::where('user_id', $userId)->count();

    // Chờ xác nhận
    $pendingOrders = Order::where('user_id', $userId)
        ->where('status', 'pending')
        ->count();

    // Đang xử lý
    $processingOrders = Order::where('user_id', $userId)
        ->whereIn('status', [
            'confirmed',
            'processing',
        ])
        ->count();

    // Đang giao
    $shippingOrders = Order::where('user_id', $userId)
        ->where('status', 'shipped')
        ->count();

    // Đã hoàn thành
    $completedOrders = Order::where('user_id', $userId)
        ->where('status', 'delivered')
        ->count();

    // Đã hủy / hoàn trả
    $cancelledOrders = Order::where('user_id', $userId)
        ->whereIn('status', [
            'cancelled',
            'returned',
        ])
        ->count();

    return view('profile.my-profile', compact(
        'user',
        'totalOrders',
        'pendingOrders',
        'processingOrders',
        'shippingOrders',
        'completedOrders',
        'cancelledOrders'
    ));
}

    /**
     * ============================================================
     * THÔNG TIN CỦA TÔI
     * URL: /profile/account
     * Route: profile.account
     * View: profile/account.blade.php
     * ============================================================
     */
    public function account(Request $request): View
    {
        $user = $request->user()->load('addresses');

        $addresses = $user->addresses;

        return view('profile.account', [
            'user' => $user,
            'addresses' => $addresses,
        ]);
    }

    /**
     * ============================================================
     * CẬP NHẬT THÔNG TIN CÁ NHÂN
     * Route: profile.update
     * ============================================================
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        // Nếu email thay đổi thì yêu cầu xác minh lại
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Cập nhật thông tin thành công.'
            );
    }

    /**
     * ============================================================
     * THÊM ĐỊA CHỈ
     * Route: profile.address.store
     * ============================================================
     */
    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'province'  => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'ward'      => 'required|string|max:100',
            'street'    => 'required|string|max:255',
        ]);

        $userId = $request->user()->id;

        $data['user_id'] = $userId;

        // Địa chỉ đầu tiên tự động là mặc định
        $data['is_default'] = !Address::where(
            'user_id',
            $userId
        )->exists();

        Address::create($data);

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Thêm địa chỉ thành công.'
            );
    }

    /**
     * ============================================================
     * CẬP NHẬT ĐỊA CHỈ
     * Route: profile.address.update
     * ============================================================
     */
    public function updateAddress(
        Request $request,
        Address $address
    ): RedirectResponse {
        // Chỉ được sửa địa chỉ của chính mình
        abort_if(
            $address->user_id !== $request->user()->id,
            403
        );

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'province'  => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'ward'      => 'required|string|max:100',
            'street'    => 'required|string|max:255',
        ]);

        $address->update($data);

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Cập nhật địa chỉ thành công.'
            );
    }

    /**
     * ============================================================
     * XÓA ĐỊA CHỈ
     * Route: profile.address.destroy
     * ============================================================
     */
    public function destroyAddress(
        Address $address
    ): RedirectResponse {
        // Chỉ được xóa địa chỉ của chính mình
        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        $wasDefault = $address->is_default;

        $address->delete();

        // Nếu xóa địa chỉ mặc định
        // thì chọn địa chỉ đầu tiên còn lại làm mặc định
        if ($wasDefault) {
            $newDefault = Address::where(
                'user_id',
                auth()->id()
            )->first();

            if ($newDefault) {
                $newDefault->update([
                    'is_default' => true,
                ]);
            }
        }

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Đã xóa địa chỉ.'
            );
    }

    /**
     * ============================================================
     * ĐẶT ĐỊA CHỈ MẶC ĐỊNH
     * Route: profile.address.default
     * ============================================================
     */
    public function setDefaultAddress(
        Address $address
    ): RedirectResponse {
        // Chỉ được thao tác địa chỉ của chính mình
        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        // Bỏ mặc định tất cả địa chỉ
        Address::where(
            'user_id',
            auth()->id()
        )->update([
            'is_default' => false,
        ]);

        // Đặt địa chỉ hiện tại làm mặc định
        $address->update([
            'is_default' => true,
        ]);

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Đã đặt địa chỉ mặc định.'
            );
    }

    /**
     * ============================================================
     * ĐỔI MẬT KHẨU
     * Route: profile.password.update
     * ============================================================
     */
    public function updatePassword(
        UpdatePasswordRequest $request
    ): RedirectResponse {
        /*
         * User Model đang dùng:
         *
         * protected $casts = [
         *     'password' => 'hashed',
         * ];
         *
         * Vì vậy không cần Hash::make().
         */

        $request->user()->update([
            'password' => $request->password,
        ]);

        return redirect()
            ->route('profile.account')
            ->with(
                'success',
                'Đổi mật khẩu thành công.'
            );
    }

    /**
     * ============================================================
     * XÓA TÀI KHOẢN
     * Route: profile.destroy
     * ============================================================
     */
    public function destroy(
        Request $request
    ): RedirectResponse {
        // Kiểm tra mật khẩu hiện tại
        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );

        $user = $request->user();

        // Đăng xuất
        Auth::logout();

        // Xóa địa chỉ
        Address::where(
            'user_id',
            $user->id
        )->delete();

        // Xóa tài khoản
        $user->delete();

        // Hủy session
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}