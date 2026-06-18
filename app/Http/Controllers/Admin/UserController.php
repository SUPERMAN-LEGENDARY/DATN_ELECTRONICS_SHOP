<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Không cho phép tự thay đổi role của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi role của chính bạn.');
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

        $user->update(['is_active' => ! $user->is_active]);

        $msg = $user->is_active
            ? "Đã mở khoá tài khoản {$user->name}."
            : "Đã khoá tài khoản {$user->name}.";

        return back()->with('success', $msg);
    }
}
