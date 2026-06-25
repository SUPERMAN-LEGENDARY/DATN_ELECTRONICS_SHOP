<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        // Lọc theo role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // ─── Hiển thị form chỉnh sửa thông tin ───────────────────────────
    public function edit(User $user): View
    {
        if ($user->isFirstAdmin() && $user->id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa quản trị viên cấp cao nhất.');
        }

        // Lấy địa chỉ mặc định (hoặc địa chỉ đầu tiên) của user, nếu có
        $address = $user->addresses()->orderByDesc('is_default')->first();

        return view('admin.users.edit', compact('user', 'address'));
    }

    // ─── Cập nhật thông tin khách hàng / nhân viên ───────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        $isSelf = $user->id === auth()->id();

        // ── Admin gốc (admin thứ 1) chỉ có thể tự sửa thông tin của chính mình.
        //    Các admin khác (admin thứ 2, thứ 3, ...) không được phép sửa admin gốc.
        if ($user->isFirstAdmin() && ! $isSelf) {
            return back()->with('error', 'Bạn không có quyền chỉnh sửa quản trị viên cấp cao nhất.');
        }

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],

            // Địa chỉ liên hệ (không bắt buộc)
            'address_province'  => ['nullable', 'string', 'max:255'],
            'address_district'  => ['nullable', 'string', 'max:255'],
            'address_ward'      => ['nullable', 'string', 'max:255'],
            'address_street'    => ['nullable', 'string', 'max:255'],
        ];

        // Không cho tự đổi role / khoá tài khoản của chính mình
        if (! $isSelf) {
            $rules['role'] = ['required', 'in:customer,staff,admin'];
        }

        $validated = $request->validate($rules);

        // ── Cập nhật thông tin tài khoản (KHÔNG đổi mật khẩu ở đây) ──
        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! $isSelf) {
            $data['role']      = $validated['role'];
            $data['is_active'] = $request->boolean('is_active');
        }

        $user->update($data);

        // ── Cập nhật / tạo địa chỉ liên hệ (nếu admin có nhập) ──
        $addressInput = collect($validated)->only([
            'address_province',
            'address_district',
            'address_ward',
            'address_street',
        ])->filter(fn($v) => filled($v));

        if ($addressInput->isNotEmpty()) {
            $address = $user->addresses()->orderByDesc('is_default')->first();

            $payload = [
                // Người nhận & SĐT lấy theo thông tin tài khoản
                'full_name' => $address->full_name ?? $data['name'],
                'phone'     => $data['phone'] ?? $address->phone ?? '',
                'province'  => $validated['address_province']  ?? $address->province  ?? '',
                'district'  => $validated['address_district']  ?? $address->district  ?? '',
                'ward'      => $validated['address_ward']      ?? $address->ward      ?? '',
                'street'    => $validated['address_street']    ?? $address->street    ?? '',
            ];

            if ($address) {
                $address->update($payload);
            } else {
                $payload['user_id']    = $user->id;
                $payload['is_default'] = true;
                Address::create($payload);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Đã cập nhật thông tin của \"{$user->name}\".");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Không cho phép tự thay đổi role của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi role của chính bạn.');
        }

        // Admin gốc (admin thứ 1) không thể bị admin khác đổi role
        if ($user->isFirstAdmin()) {
            return back()->with('error', 'Bạn không có quyền thay đổi role của quản trị viên cấp cao nhất.');
        }

        $request->validate([
            'role' => ['required', 'in:customer,staff,admin'],
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Đã cập nhật role của {$user->name} thành " . ucfirst($request->role) . '.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể khoá tài khoản của chính bạn.');
        }

        // Admin gốc (admin thứ 1) không thể bị admin khác khoá tài khoản
        if ($user->isFirstAdmin()) {
            return back()->with('error', 'Bạn không có quyền khoá tài khoản của quản trị viên cấp cao nhất.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $msg = $user->is_active
            ? "Đã mở khoá tài khoản {$user->name}."
            : "Đã khoá tài khoản {$user->name}.";

        return back()->with('success', $msg);
    }
}
