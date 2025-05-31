<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ProductResource; // Để link đến trang Edit/Create của Product

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        // Dùng lại form của ProductResource
        return ProductResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Hiển thị thumbnail: thêm ->disk('cloudinary')
                Tables\Columns\ImageColumn::make('thumbnail.image_path')
                    ->label('Thumb')
                    ->disk('cloudinary')   // Lấy ảnh từ Cloudinary
                    ->height(40)
                    ->width(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 2. Tên sản phẩm, kèm link đến trang Edit của Filament ProductResource
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record): string => ProductResource::getUrl('edit', ['record' => $record])),

                // 3. SKU
                Tables\Columns\TextColumn::make('sku')->label('SKU'),

                // 4. Category
                Tables\Columns\TextColumn::make('category.name')->label('Category'),

                // 5. Giá gốc
                Tables\Columns\TextColumn::make('regular_price')->money('vnd'),

                // 6. Trạng thái active
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Create mới sẽ tự gán brand_id = ownerRecord->id
                Tables\Actions\CreateAction::make()
                    ->url(fn (): string => ProductResource::getUrl(
                        'create',
                        // Đẩy sẵn brand_id vào form Create của Product
                        ['data' => ['brand_id' => $this->ownerRecord->id]]
                    )),

                // Nếu muốn attach existing product (trường hợp many-to-many), giữ AttachAction
                // Trong ví dụ của bạn, products chỉ belongsTo Brand nên có thể bỏ hoặc tùy chỉnh
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                // Edit: chuyển hướng sang trang edit của Filament ProductResource
                Tables\Actions\EditAction::make()
                    ->url(fn ($record): string => ProductResource::getUrl('edit', ['record' => $record])),

                // Remove Brand: chỉ gán brand_id = null để tách liên kết
                Tables\Actions\Action::make('remove_brand')
                    ->label('Remove Brand')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['brand_id' => null])),

                // Nếu muốn xóa hẳn sản phẩm, có thể thêm DeleteAction, nhưng thường không nên
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('remove_brand_bulk')
                        ->label('Remove Brand from Selected')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['brand_id' => null]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
