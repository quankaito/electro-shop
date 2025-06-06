<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets; // Thêm use statement này cho gọn

class Dashboard extends BaseDashboard
{
    /**
     * Sắp xếp lại thứ tự các widget tại đây.
     * Filament sẽ tự động xếp chúng vào các hàng dựa trên $columnSpan đã khai báo.
     */
    public function getHeaderWidgets(): array
    {
        return [
            // HÀNG 1: Đặt 2 widget 6-cột cạnh nhau
            Widgets\RevenueThisMonth::class,
            Widgets\TotalOrders::class,

            // HÀNG 2: Biểu đồ toàn chiều rộng
            Widgets\OrdersByDay::class,

            // HÀNG 3: Bảng đơn hàng toàn chiều rộng
            // Giả sử tên file của bạn là RecentOrders.php
            Widgets\RecentOrders::class,
        ];
    }
}