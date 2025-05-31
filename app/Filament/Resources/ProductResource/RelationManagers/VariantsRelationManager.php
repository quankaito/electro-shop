<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Attribute;
use App\Models\AttributeValue;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $recordTitleAttribute = 'sku';

    public function form(Form $form): Form
    {
        // Lấy danh sách attributes và values để tạo Selects động
        // Đây là một ví dụ đơn giản hóa. Trong thực tế, bạn có thể cần UI phức tạp hơn.
        $attributeSchema = [];
        $attributes = Attribute::with('values')->get();
        foreach ($attributes as $attribute) {
            $attributeSchema[] = Forms\Components\Select::make('options.' . $attribute->slug) // Lưu vào mảng options
                ->label($attribute->name)
                ->options($attribute->values->pluck('value', 'id'))
                ->searchable()
                ->placeholder('Select ' . $attribute->name);
        }


        return $form
            ->schema(array_merge([
                Forms\Components\TextInput::make('sku')
                    ->label('Variant SKU')
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                Forms\Components\TextInput::make('specific_price')
                    ->label('Variant Price')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->helperText('Leave blank to use base product price or price modifier logic (if any).'),
                Forms\Components\TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('image_id')
                    ->label('Variant Image (Optional)')
                    ->relationship(name: 'image', titleAttribute: 'alt_text') // Liên kết tới ProductImage
                    ->options(function (RelationManager $livewire) {
                        return $livewire->ownerRecord->images()->pluck('alt_text', 'id'); // Lấy ảnh của product cha
                    })
                    ->placeholder('Select an image for this variant'),
                ],
                $attributeSchema // Thêm các select thuộc tính động
            ))->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('options_display') // Cần accessor trong model ProductVariant
                    ->label('Options')
                    ->getStateUsing(function ($record) {
                        return $record->options->map(function ($value) {
                            return $value->attribute->name . ': ' . $value->value;
                        })->implode(', ');
                    }),
                Tables\Columns\TextColumn::make('specific_price')->money('vnd'),
                Tables\Columns\TextColumn::make('stock_quantity'),
                Tables\Columns\ImageColumn::make('image.image_path')->label('Image'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, array $data) {
                        // Xử lý lưu options vào bảng product_variant_options
                        if (isset($data['options'])) {
                            $optionIds = array_filter(array_values($data['options'])); // Lấy ID của attribute_values
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