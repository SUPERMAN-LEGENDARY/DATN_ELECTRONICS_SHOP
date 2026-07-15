<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Kho voucher của khách hàng: voucher dùng chung + voucher được tặng riêng cho khách này
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $keyword = trim((string) $request->get('code', ''));

        $vouchers = Voucher::where('is_active', true)
            ->when($keyword !== '', fn($q) => $q->where('code', 'like', "%{$keyword}%"))
            ->where(function ($q) use ($userId) {
                $q->whereNull('assigned_user_id')
                    ->orWhere('assigned_user_id', $userId);
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->whereColumn('used_count', '<', 'usage_limit')
            ->orderBy('expires_at')
            ->paginate(10);

        return view('profile.voucher', compact('vouchers', 'keyword'));
    }
}
