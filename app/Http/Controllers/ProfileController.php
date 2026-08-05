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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Trang tài khoản (thông tin cá nhân + sổ địa chỉ + đổi mật khẩu)
     */
    public function account(Request $request): View
    {
        $user = $request->user()->load('addresses');

        $addresses = $user->addresses;

        $userId = $user->id;

        $totalOrders      = Order::where('user_id', $userId)->count();
        $pendingOrders    = Order::where('user_id', $userId)->where('status', 'pending')->count();
        $processingOrders = Order::where('user_id', $userId)->whereIn('status', ['confirmed', 'processing'])->count();
        $shippingOrders   = Order::where('user_id', $userId)->where('status', 'shipped')->count();
        $completedOrders  = Order::where('user_id', $userId)->where('status', 'delivered')->count();
        $cancelledOrders  = Order::where('user_id', $userId)->whereIn('status', ['cancelled', 'returned'])->count();

        return view('profile.account', compact(
            'user',
            'addresses',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippingOrders',
            'completedOrders',
            'cancelledOrders'
        ));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('profile.account')
            ->with('success', 'Cập nhật thông tin thành công.');
    }

    /**
     * Thêm địa chỉ mới
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

        $data['user_id'] = auth()->id();

        // Địa chỉ đầu tiên của khách sẽ tự động là mặc định
        $data['is_default'] = !Address::where('user_id', auth()->id())->exists();

        Address::create($data);

        return back()->with('success', 'Thêm địa chỉ thành công.');
    }

    /**
     * Cập nhật địa chỉ
     */
    public function updateAddress(Request $request, Address $address): RedirectResponse
    {
        abort_if($address->user_id != auth()->id(), 403);

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'province'  => 'required|string|max:100',
            'district'  => 'required|string|max:100',
            'ward'      => 'required|string|max:100',
            'street'    => 'required|string|max:255',
        ]);

        $address->update($data);

        return back()->with('success', 'Cập nhật địa chỉ thành công.');
    }

    /**
     * Xóa địa chỉ
     */
    public function destroyAddress(Address $address): RedirectResponse
    {
        abort_if($address->user_id != auth()->id(), 403);

        $default = $address->is_default;

        $address->delete();

        if ($default) {
            $newDefault = Address::where('user_id', auth()->id())->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Đã xóa địa chỉ.');
    }

    /**
     * Đặt địa chỉ mặc định
     */
    public function setDefaultAddress(Address $address): RedirectResponse
    {
        abort_if($address->user_id != auth()->id(), 403);

        Address::where('user_id', auth()->id())->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return back()->with('success', 'Đã đặt địa chỉ mặc định.');
    }

    /**
     * Đổi mật khẩu
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        // Lưu ý: User model có cast 'password' => 'hashed'
        // nên Laravel tự động hash khi gán giá trị.
        // KHÔNG gọi Hash::make() ở đây để tránh hash 2 lần.
        $request->user()->update([
            'password' => $request->password,
        ]);

        return redirect()
            ->route('profile.account')
            ->with('success', 'Đổi mật khẩu thành công.');
    }

    /**
     * Xóa tài khoản
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        Address::where('user_id', $user->id)->delete();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
