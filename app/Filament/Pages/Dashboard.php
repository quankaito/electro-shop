<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets; // Thêm use statement này cho gọn

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            // Thêm widget mới của chúng ta vào đây
            Widgets\GeneralStatsOverview::class,

            // Các widget cũ của bạn
            Widgets\RevenueThisMonth::class,
            Widgets\TotalOrders::class,
            Widgets\OrdersByDay::class,
            Widgets\RecentOrders::class,
        ];
    }
}