<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class RevenueThisMonth extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        // SỬA Ở ĐÂY: Thay 'total_price' bằng 'total_amount'
        $revenue = Order::where('created_at', '>=', $startOfMonth)
                        ->sum('total_amount'); // <-- Đã sửa đúng theo file migration

        $formattedRevenue = number_format($revenue, 0, ',', '.') . '₫';

        return [
            Stat::make('Doanh thu tháng này', $formattedRevenue)
                ->description('Tổng doanh thu trong tháng hiện tại')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}