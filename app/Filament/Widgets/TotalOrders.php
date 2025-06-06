<?php

namespace App\Filament\Widgets;

// SỬA 1: Thay đổi use statements
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat; // Bắt buộc phải có để tạo thẻ
use App\Models\Order;

// SỬA 2: Kế thừa từ StatsOverviewWidget
class TotalOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 6;
    /**
     * SỬA 3: Toàn bộ logic tạo thẻ nằm trong phương thức getStats()
     * Phương thức này sẽ trả về một MẢNG các đối tượng Stat.
     */
    protected function getStats(): array
    {
        // Tạo và trả về một Stat
        return [
            Stat::make('Tổng số đơn hàng', Order::count())
                ->description('Tất cả đơn hàng trong hệ thống') // Dòng mô tả nhỏ bên dưới (tùy chọn)
                ->descriptionIcon('heroicon-m-shopping-bag') // Icon cho dòng mô tả (tùy chọn)
                ->color('primary'), // Màu sắc (tùy chọn: success, warning, danger, primary)
        ];
    }
}