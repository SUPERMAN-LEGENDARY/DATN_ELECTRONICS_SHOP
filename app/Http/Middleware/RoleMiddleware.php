<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Hỗ trợ nhiều role: 'role:admin,staff'
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Kiểm tra tài khoản có bị khoá không
        if (! auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khoá.']);
        }

        if (! in_array($userRole, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
