<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Widgets\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends TableWidget
{
    protected int | string | array $columnSpan = 'full';
    // protected static string $view = 'filament.widgets.recent-orders';

    /**
     * @return Builder
     */
    public function getTableQuery(): Builder
    {
        return Order::query()
            ->orderBy('created_at', 'desc')
            ->take(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('order_number')->label('Mã đơn'),
            Tables\Columns\TextColumn::make('customer_name')->label('Khách hàng'),
            Tables\Columns\TextColumn::make('total_amount')
                ->label('Tổng tiền')
                ->money('VND', true),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Ngày đặt')
                ->dateTime('d/m/Y H:i'),
        ];
    }
}
