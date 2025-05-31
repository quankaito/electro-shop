<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingMethodResource\Pages;
use App\Models\ShippingMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique code, e.g., standard_shipping, express_shipping'),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->prefix('VNĐ')
                    ->default(0)
                    ->helperText('Set 0 if cost is calculated dynamically or free.'),

                /**
                 * Logo của ShippingMethod:
                 *  - Thêm ->disk('cloudinary') để upload lên Cloudinary.
                 *  - Filament tự xóa ảnh cũ khi bản ghi bị xóa hoặc khi thay logo mới.
                 */
                Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->directory('shipping_methods')   // Thư mục trên Cloudinary sẽ là "shipping_methods/"
                    ->disk('cloudinary')              // Upload lên Cloudinary
                    ->preserveFilenames(false)        // Tạo tên file tự động
                    ->maxSize(1024),  // Giới hạn 2MB

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /**
                 * Hiển thị logo từ Cloudinary:
                 *  - Thêm ->disk('cloudinary') để Filament tạo URL đúng.
                 */
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('cloudinary')
                    ->height(40)
                    ->width(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->money('vnd')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShippingMethods::route('/'),
            'create' => Pages\CreateShippingMethod::route('/create'),
            'edit'   => Pages\EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
