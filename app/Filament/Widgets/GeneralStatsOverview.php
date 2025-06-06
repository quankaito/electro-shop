<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class GeneralStatsOverview extends BaseWidget
{
    // Để widget này chiếm toàn bộ chiều rộng
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // === 1. TỔNG SỐ USER ===
        $totalUsers = User::count();

        // === 2. TỔNG SỐ SẢN PHẨM TRONG KHO ===
        // Giả định: bạn quản lý tồn kho và có cột 'stock_quantity'
        $totalStock = Product::where('manage_stock', true)->sum('stock_quantity');

        // === 3. USER MUA NHIỀU NHẤT (KHÁCH HÀNG VIP) ===
        $topSpender = User::withSum(['orders' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'total_amount')
            ->orderBy('orders_sum_total_amount', 'desc')
            ->first();

        // === 4. SẢN PHẨM ĐƯỢC MUA NHIỀU NHẤT ===
        // Giả định: bạn có bảng 'order_items' với cột 'product_id' và 'quantity'
        $bestSellerData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled') // Chỉ tính đơn hàng không bị hủy
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->first();

        $bestSellerProduct = null;
        if ($bestSellerData) {
            $bestSellerProduct = Product::find($bestSellerData->product_id);
        }

        // === 5. SẢN PHẨM ĐÁNH GIÁ CAO NHẤT ===
        $topRatedProduct = Product::withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->first();


        return [
            // Thẻ 1: Tổng số user
            Stat::make('Tổng số khách hàng', $totalUsers)
                ->description('Tổng số tài khoản đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            // Thẻ 2: Tổng sản phẩm trong kho
            Stat::make('Tổng sản phẩm trong kho', number_format($totalStock))
                ->description('Tổng số lượng các mặt hàng')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),

            // Thẻ 3: Khách hàng VIP
            Stat::make('Khách hàng VIP', $topSpender ? $topSpender->name : 'Chưa có')
                ->description('Chi tiêu: ' . number_format($topSpender->orders_sum_total_amount ?? 0) . '₫')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            // Thẻ 4: Sản phẩm bán chạy nhất
            Stat::make('Sản phẩm bán chạy nhất', $bestSellerProduct ? $bestSellerProduct->name : 'Chưa có')
                ->description(($bestSellerData->total_sold ?? 0) . ' đã bán')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            // Thẻ 5: Sản phẩm đánh giá cao nhất
            Stat::make('Đánh giá cao nhất', $topRatedProduct ? $topRatedProduct->name : 'Chưa có')
                ->description(number_format($topRatedProduct->reviews_avg_rating ?? 0, 1) . ' sao')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),
        ];
    }
}