<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ProductResource; // Để link sang ProductResource

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        // Tái sử dụng form của ProductResource (tạo / sửa sản phẩm)
        return ProductResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Thumbnail hiển thị từ Cloudinary
                Tables\Columns\ImageColumn::make('thumbnail.image_path')
                    ->label('Thumb')
                    ->disk('cloudinary')   // ← Lấy ảnh từ Cloudinary
                    ->height(40)
                    ->width(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 2. Tên sản phẩm, kèm link đến trang Edit của ProductResource
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record): string => ProductResource::getUrl('edit', ['record' => $record])),

                // 3. SKU
                Tables\Columns\TextColumn::make('sku')->label('SKU'),

                // 4. Giá gốc (regular_price)
                Tables\Columns\TextColumn::make('regular_price')
                    ->money('vnd')
                    ->sortable(),

                // 5. Trạng thái is_active
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Create Action: mở trang tạo mới Product, truyền sẵn category_id
                Tables\Actions\CreateAction::make()
                    ->url(fn (): string => ProductResource::getUrl(
                        'create',
                        ['data' => ['category_id' => $this->ownerRecord->id]]
                    )),

                // Attach Action: chỉ cần khi mối quan hệ là many-to-many
                // Nếu product chỉ belongsTo category, bạn có thể bỏ dòng dưới
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                // Edit Action: mở trang Edit của ProductResource
                Tables\Actions\EditAction::make()
                    ->url(fn ($record): string => ProductResource::getUrl('edit', ['record' => $record])),

                // Detach Action: tách mối quan hệ (nếu many-to-many)
                Tables\Actions\DetachAction::make(),

                // Delete Action: xóa luôn sản phẩm
                // (Cẩn thận nếu sản phẩm chỉ thuộc 1 category, sẽ bị xóa vĩnh viễn)
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
