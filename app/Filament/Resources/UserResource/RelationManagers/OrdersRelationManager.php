<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\OrderResource; // Để có thể link đến trang view order

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $recordTitleAttribute = 'order_number';

    public function form(Form $form): Form
    {
        // Thường không tạo đơn hàng trực tiếp từ đây, chỉ hiển thị
        return $form
            ->schema([
                // Forms for creating/editing orders related to a user are complex
                // and better handled by the main OrderResource.
                // This relation manager is primarily for viewing.
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable()->sortable()
                    ->url(fn ($record): string => OrderResource::getUrl('view', ['record' => $record])), // Link to order view
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => fn ($state) => in_array($state, ['payment_pending', 'confirmed', 'processing']),
                        'success' => fn ($state) => in_array($state, ['shipped', 'delivered']),
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'failed', 'refunded']),
                    ])->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->money('vnd')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Không nên tạo order từ đây
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record): string => OrderResource::getUrl('view', ['record' => $record])),
                // Tables\Actions\EditAction::make()->url(fn ($record): string => OrderResource::getUrl('edit', ['record' => $record])),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function canCreate(): bool
    {
        return false; // Không cho tạo order từ relation manager của User
    }
}