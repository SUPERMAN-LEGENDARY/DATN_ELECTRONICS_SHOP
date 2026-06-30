@extends('layouts.admin')

@push('styles')
<style>
    /* ===== STATS CARDS ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        transition: transform 0.15s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-card .icon {
        font-size: 28px;
        margin-bottom: 8px;
        color: #1E88E5;
    }

    .stat-card .number {
        font-size: 28px;
        font-weight: 700;
        color: #0D1B2A;
    }

    .stat-card .label {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .stat-card .icon.orange {
        color: #F57C00;
    }

    .stat-card .icon.green {
        color: #388E3C;
    }

    .stat-card .icon.purple {
        color: #7B1FA2;
    }

    /* ===== CHART CONTAINERS ===== */
    .chart-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-box {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    .chart-box h3 {
        font-size: 16px;
        margin-bottom: 16px;
        color: #0D1B2A;
    }

    .chart-box .chart-container {
        position: relative;
        height: 250px;
    }

    /* ===== TABLE ===== */
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .table th {
        background: #f8f9fc;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 10px 12px;
        border-bottom: 2px solid #e9ecef;
        text-align: left;
        color: #495057;
    }

    .table td {
        padding: 10px 12px;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background: #f8f9fc;
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        line-height: 1.5;
    }

    .badge-success {
        background: #d4edda;
        color: #155724;
    }

    .badge-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-info {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-secondary {
        background: #e2e3e5;
        color: #383d41;
    }

    .text-muted {
        color: #6c757d;
    }

    .text-center {
        text-align: center;
    }

    .mt-3 {
        margin-top: 16px;
    }

    @media (max-width: 768px) {
        .chart-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
@endpush

@section('title', 'Thống kê')

@section('content')
<div class="container">
    <h1>📊 Tổng quan</h1>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="number">{{ number_format($totalOrders) }}</div>
            <div class="label">Tổng đơn hàng</div>
        </div>
        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-money-bill-wave"></i></div>
            <div class="number">{{ number_format($totalRevenue) }}đ</div>
            <div class="label">Doanh thu (đã giao)</div>
        </div>
        <div class="stat-card">
            <div class="icon green"><i class="fas fa-boxes"></i></div>
            <div class="number">{{ number_format($totalProducts) }}</div>
            <div class="label">Sản phẩm</div>
        </div>
        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-users"></i></div>
            <div class="number">{{ number_format($totalUsers) }}</div>
            <div class="label">Người dùng</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="chart-grid">
        <div class="chart-box">
            <h3><i class="fas fa-chart-bar"></i> Doanh thu theo tháng ({{ date('Y') }})</h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="chart-box">
            <h3><i class="fas fa-chart-pie"></i> Trạng thái đơn hàng</h3>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="chart-box" style="margin-bottom:24px;">
        <h3><i class="fas fa-crown"></i> Top 5 sản phẩm bán chạy</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng bán</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ number_format($product->total_sold) }}</td>
                    <td>{{ number_format($product->total_revenue) }}đ</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu bán hàng.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent Orders --}}
    <div class="chart-box">
        <h3><i class="fas fa-clock"></i> Đơn hàng gần đây</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td>{{ number_format($order->total) }}đ</td>
                    <td>
                        @php
                        $class = match($order->status) {
                        'delivered' => 'badge-success',
                        'cancelled' => 'badge-danger',
                        'pending' => 'badge-warning',
                        'processing'=> 'badge-info',
                        default => 'badge-secondary'
                        };
                        @endphp
                        <span class="badge {{ $class }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Chưa có đơn hàng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── Biểu đồ doanh thu ──
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: {
                    !!json_encode($months) !!
                },
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: {
                        !!json_encode($revenues) !!
                    },
                    backgroundColor: 'rgba(30, 136, 229, 0.6)',
                    borderColor: 'rgba(30, 136, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value >= 1000000 ? (value / 1000000) + 'M' : value;
                            }
                        }
                    }
                }
            }
        });

        // ── Biểu đồ trạng thái ──
        const ctx2 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {
                    !!json_encode($statusData['labels']) !!
                },
                datasets: [{
                    data: {
                        !!json_encode($statusData['data']) !!
                    },
                    backgroundColor: [
                        '#FFC107', // pending - vàng
                        '#FF9800', // confirmed - cam
                        '#2196F3', // processing - xanh dương
                        '#00BCD4', // shipped - xanh cyan
                        '#4CAF50', // delivered - xanh lá
                        '#F44336', // cancelled - đỏ
                        '#9E9E9E' // returned - xám
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush