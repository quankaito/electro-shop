<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;

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
                    ->helperText('Unique code, e.g., cod, bank_transfer, vnpay'),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('instructions')
                    ->label('Payment Instructions')
                    ->columnSpanFull()
                    ->helperText('E.g., Bank account details for bank transfer.'),

                Forms\Components\FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('payment_methods')   // Ảnh sẽ lưu vào thư mục “payment_methods/” trên Cloudinary
                    ->disk('cloudinary')            // ← Sử dụng disk “cloudinary”
                    ->preserveFilenames(false)      // Sinh tên file ngẫu nhiên để tránh trùng lặp
                    ->maxSize(1024)  // Giới hạn kích thước 2MB (tuỳ chọn)
                    ->imagePreviewHeight('100'),

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
                // Hiển thị logo từ disk “cloudinary”
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('cloudinary')   // ← Lấy URL từ Cloudinary
                    ->height(40)
                    ->width(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->searchable()
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
            'index'  => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit'   => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
