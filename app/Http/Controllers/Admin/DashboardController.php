<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ─── Thống kê tổng quan ──────────────────────────────
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total');
        $totalProducts = Product::count();
        $totalUsers = User::count();

        // ─── Doanh thu theo tháng (7 tháng gần nhất) ──────────
        $monthlyRevenue = Order::where('status', 'delivered')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Đảm bảo đủ 12 tháng
        $months = [];
        $revenues = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = 'Tháng ' . $i;
            $revenues[] = $monthlyRevenue[$i] ?? 0;
        }

        // ─── Top 5 sản phẩm bán chạy ──────────────────────────
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // ─── Đơn hàng gần đây ──────────────────────────────────
        $recentOrders = Order::with(['user', 'address'])
            ->latest()
            ->limit(10)
            ->get();

        // ─── Thống kê đơn hàng theo trạng thái ──────────────────
        $statusCounts = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hàng'
        ];

        $statusData = [];
        foreach ($statusLabels as $key => $label) {
            $statusData['labels'][] = $label;
            $statusData['data'][] = $statusCounts[$key] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalProducts',
            'totalUsers',
            'months',
            'revenues',
            'topProducts',
            'recentOrders',
            'statusData'
        ));
    }
}