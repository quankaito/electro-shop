<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Attribute;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Form $form): Form
    {
        // ===============================================
        // Phần tạo Selects cho các thuộc tính (Đã sửa lỗi)
        // ===============================================
        $attributeSchema = [];
        // Lấy attributes và chỉ eager load các values không bị null
        $attributes = Attribute::with(['values' => function ($query) {
            $query->whereNotNull('value');
        }])->get();

        foreach ($attributes as $attribute) {
            // Chỉ tạo Select nếu attribute có ít nhất một value hợp lệ
            if ($attribute->values->isNotEmpty()) {
                $attributeSchema[] = Forms\Components\Select::make('options.' . $attribute->slug)
                    ->label($attribute->name)
                    ->options($attribute->values->pluck('value', 'id'))
                    ->searchable()
                    ->placeholder('Select ' . $attribute->name);
            }
        }

        return $form
            ->schema(array_merge(
                [
                    Forms\Components\TextInput::make('sku')
                        ->label('Variant SKU')
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),
                    Forms\Components\TextInput::make('specific_price')
                        ->label('Variant Price')
                        ->numeric()
                        ->prefix('VNĐ')
                        ->helperText('Leave blank to use base product price.'),
                    Forms\Components\TextInput::make('stock_quantity')
                        ->numeric()
                        ->default(0),

                    // ===============================================
                    // Phần Select ảnh của biến thể (Đã sửa lỗi)
                    // ===============================================
                    Forms\Components\Select::make('image_id')
                        ->label('Variant Image (Optional)')
                        ->relationship(name: 'image', titleAttribute: 'alt_text')
                        ->options(function (RelationManager $livewire) {
                            return $livewire->ownerRecord->images()
                                // Lấy ra các ảnh có alt_text hoặc có image_path để làm fallback
                                ->where(function ($query) {
                                    $query->whereNotNull('alt_text')
                                          ->orWhereNotNull('image_path');
                                })
                                ->get()
                                // Tạo ra một mảng [id => label] an toàn
                                ->mapWithKeys(function ($image) {
                                    // Ưu tiên dùng alt_text, nếu không có thì dùng tên file ảnh
                                    $label = $image->alt_text ?? basename($image->image_path);
                                    return [$image->id => $label];
                                });
                        })
                        ->placeholder('Select an image for this variant'),
                ],
                $attributeSchema // Thêm các select thuộc tính động đã được lọc
            ))->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image.image_path')->label('Image')->disk('cloudinary'),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('options_display')
                    ->label('Options')
                    ->getStateUsing(function ($record) {
                        return $record->options->map(function ($value) {
                            // Thêm kiểm tra để tránh lỗi nếu relation bị thiếu
                            if ($value && $value->attribute) {
                                return $value->attribute->name . ': ' . $value->value;
                            }
                            return '';
                        })->filter()->implode(', ');
                    }),
                Tables\Columns\TextColumn::make('specific_price')->money('vnd'),
                Tables\Columns\TextColumn::make('stock_quantity'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, array $data) {
                        // Xử lý lưu options vào bảng product_variant_options
                        if (isset($data['options'])) {
                            $optionIds = array_filter(array_values($data['options']));
                            $record->options()->sync($optionIds);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, array $data) {
                        if (isset($data['options'])) {
                            $optionIds = array_filter(array_values($data['options']));
                            $record->options()->sync($optionIds);
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}