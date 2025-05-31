<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Promotion;
use App\Filament\Resources\PromotionResource;

class PromotionsRelationManager extends RelationManager
{
    protected static string $relationship = 'promotions';
    protected static ?string $recordTitleAttribute = 'name'; // Từ model Promotion

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('promotion_id') // Đây là ID của promotion
                    ->label('Promotion Code')
                    ->options(Promotion::where('is_active', true)->pluck('code', 'id')) // Chỉ hiển thị KM active
                    ->searchable()
                    ->required()
                    ->columnSpanFull()
                    ->disabledOn('edit'), // Không cho sửa KM đã áp dụng
                Forms\Components\TextInput::make('discount_applied')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->required()
                    ->helperText('The actual discount amount applied to this order from this promotion.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable()
                    ->url(fn ($record): string => PromotionResource::getUrl('edit', ['record' => $record->id])), // Dùng $record->id vì $record ở đây là Promotion model
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.discount_applied')->label('Discount Applied')->money('vnd'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make() // Attach KM vào đơn hàng
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(), // Select để chọn Promotion
                        Forms\Components\TextInput::make('discount_applied') // Cột pivot
                            ->numeric()
                            ->prefix('VNĐ')
                            ->required(),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make() // Sửa cột pivot
                    ->form(fn (Tables\Actions\EditAction $action, $record): array => [ // $record ở đây là Promotion model
                        Forms\Components\TextInput::make('discount_applied')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->required()
                            ->default($record->pivot->discount_applied), // Lấy giá trị từ pivot
                    ]),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}