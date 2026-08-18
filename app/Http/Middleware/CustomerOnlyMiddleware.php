<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->isCustomer()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tài khoản quản trị và nhân viên không được phép mua hàng.'], 403);
            }
            return redirect()->route('home')->with('error', 'Tài khoản quản trị và nhân viên không được phép mua bán trên hệ thống. Vui lòng đăng xuất để mua hàng.');
        }

        return $next($request);
    }
}
