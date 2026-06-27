<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    // ─── Danh sách tài khoản ──────────────────────────────────────────
    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'staff'); // 'staff' | 'customer' | 'trash'

        if ($tab === 'trash') {
            $query = User::onlyTrashed();
        } else {
            $query = User::query()->whereNull('deleted_at');
            if ($tab === 'staff') {
                $query->whereIn('role', ['admin', 'staff']);
            } else {
                $query->where('role', 'customer');
            }
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

        // Lọc trạng thái (chỉ áp dụng cho tab staff và customer)
        if ($tab !== 'trash' && $request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'staff'    => User::whereIn('role', ['admin', 'staff'])->count(),
            'customer' => User::where('role', 'customer')->count(),
            'trash'    => User::onlyTrashed()->count(),
            'locked'   => User::where('is_active', 0)->count(),
        ];

        return view('admin.users.index', compact('users', 'tab', 'stats'));
    }

    // ─── Form tạo nhân viên ────────────────────────────────────────────
    public function create(): View
    {
        return view('admin.users.create');
    }

    // ─── Lưu nhân viên mới ────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:staff,admin'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index', ['tab' => 'staff'])
            ->with('success', "Đã tạo tài khoản nhân viên \"{$validated['name']}\" thành công.");
    }

    // ─── Form chỉnh sửa thông tin ─────────────────────────────────────
    public function edit(User $user): View
    {
        if ($user->isFirstAdmin() && $user->id !== auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa quản trị viên cấp cao nhất.');
        }

        $address = $user->addresses()->orderByDesc('is_default')->first();

        return view('admin.users.edit', compact('user', 'address'));
    }

    // ─── Cập nhật thông tin ───────────────────────────────────────────
    public function update(Request $request, User $user): RedirectResponse
    {
        $isSelf = $user->id === auth()->id();

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

        if (! $isSelf) {
            $rules['role'] = ['required', 'in:customer,staff,admin'];
        }

        // Đổi mật khẩu (tuỳ chọn)
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! $isSelf) {
            $data['role']      = $validated['role'];
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        // Cập nhật địa chỉ liên hệ
        $addressInput = collect($validated)->only([
            'address_province',
            'address_district',
            'address_ward',
            'address_street',
        ])->filter(fn($v) => filled($v));

        if ($addressInput->isNotEmpty()) {
            $address = $user->addresses()->orderByDesc('is_default')->first();

            $payload = [
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

        return redirect()->route('admin.users.index', ['tab' => $user->role === 'customer' ? 'customer' : 'staff'])
            ->with('success', "Đã cập nhật thông tin của \"{$user->name}\".");
    }

    // ─── Đổi role ────────────────────────────────────────────────────
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi role của chính bạn.');
        }

        if ($user->isFirstAdmin()) {
            return back()->with('error', 'Bạn không có quyền thay đổi role của quản trị viên cấp cao nhất.');
        }

        $request->validate([
            'role' => ['required', 'in:customer,staff,admin'],
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Đã cập nhật role của {$user->name} thành " . ucfirst($request->role) . '.');
    }

    // ─── Khoá / Mở khoá tài khoản ────────────────────────────────────
    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể khoá tài khoản của chính bạn.');
        }

        if ($user->isFirstAdmin()) {
            return back()->with('error', 'Bạn không có quyền khoá tài khoản của quản trị viên cấp cao nhất.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $msg = $user->is_active
            ? "Đã mở khoá tài khoản {$user->name}."
            : "Đã khoá tài khoản {$user->name}.";

        return back()->with('success', $msg);
    }

    // ─── Xoá mềm (chung) ─────────────────────────────────────────────
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xoá tài khoản của chính bạn.');
        }

        if ($user->isFirstAdmin()) {
            return back()->with('error', 'Không thể xoá tài khoản quản trị viên cấp cao nhất.');
        }

        $tab = $user->role === 'customer' ? 'customer' : 'staff';
        $name = $user->name;
        $user->delete(); // soft delete

        return redirect()->route('admin.users.index', ['tab' => $tab])
            ->with('success', "Đã xoá mềm tài khoản \"{$name}\".");
    }

    // ─── Khôi phục tài khoản đã xoá mềm ─────────────────────────────
    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index', ['tab' => 'trash'])
            ->with('success', "Đã khôi phục tài khoản \"{$user->name}\".");
    }

    // ─── Xoá vĩnh viễn ───────────────────────────────────────────────
    public function forceDelete(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $name = $user->name;

        // Gỡ ràng buộc FK trước khi xoá vĩnh viễn
        // 1. Đơn hàng: null hoá user_id để giữ lại lịch sử đơn hàng
        \DB::table('orders')->where('user_id', $user->id)->update(['user_id' => null]);

        // 2. Tin tức do user đăng (restrictOnDelete) → null hoá
        \DB::table('news')->where('user_id', $user->id)->update(['user_id' => null]);

        // 3. Địa chỉ (cascadeOnDelete sẽ tự xoá, nhưng orders.address_id restrict)
        //    → null hoá address_id trong orders trước, rồi xoá addresses
        \DB::table('orders')->whereIn(
            'address_id',
            \DB::table('addresses')->where('user_id', $user->id)->pluck('id')
        )->update(['address_id' => null]);
        \DB::table('addresses')->where('user_id', $user->id)->delete();

        $user->forceDelete();

        return redirect()->route('admin.users.index', ['tab' => 'trash'])
            ->with('success', "Đã xoá vĩnh viễn tài khoản \"{$name}\".");
    }
}
