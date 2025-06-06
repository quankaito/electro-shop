<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Order;
use Carbon\Carbon;

class OrdersByDay extends LineChartWidget
{
    // SỬA LỖI Ở ĐÂY: Thay đổi kiểu dữ liệu thành int | string | array
    protected int | string | array $columnSpan = 'full';

    // Thuộc tính $heading vẫn đúng
    protected static ?string $heading = 'Đơn hàng (7 ngày gần nhất)';

    protected function getData(): array
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $days->push($date);
        }

        $counts = $days->map(fn($date) => Order::whereDate('created_at', $date)->count());

        return [
            'labels' => $days->map(fn($date) => Carbon::parse($date)->format('d/m'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Số đơn',
                    'data' => $counts->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
            ],
        ];
    }
}