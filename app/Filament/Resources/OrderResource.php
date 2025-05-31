<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute = 'order_number';

    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')
                        ->schema([
                            Forms\Components\TextInput::make('order_number')
                                ->default('ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)))
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->label('Customer')
                                ->searchable()
                                ->preload()
                                ->placeholder('Guest Order'),
                            Forms\Components\TextInput::make('customer_name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('customer_email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('customer_phone')
                                ->tel()
                                ->required()
                                ->maxLength(15),
                            Forms\Components\Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'payment_pending' => 'Payment Pending',
                                    'confirmed' => 'Confirmed',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'delivered' => 'Delivered',
                                    'cancelled' => 'Cancelled',
                                    'refunded' => 'Refunded',
                                    'failed' => 'Failed',
                                ])
                                ->required()
                                ->default('pending'),
                             Forms\Components\DateTimePicker::make('paid_at'),
                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Address Information')
                        ->schema([
                            // Tạm thời để trống, bạn sẽ quản lý Address qua Select hoặc RelationManager cho User
                            // Hoặc bạn có thể tạo fields để nhập địa chỉ trực tiếp vào Order nếu không link tới Address model
                            Forms\Components\Select::make('shipping_address_id')
                                ->relationship('shippingAddress', 'full_address') // Giả sử có accessor full_address
                                ->label('Shipping Address')
                                ->searchable()
                                ->placeholder('Select shipping address'),
                             Forms\Components\Select::make('billing_address_id')
                                ->relationship('billingAddress', 'full_address') // Giả sử có accessor full_address
                                ->label('Billing Address')
                                ->searchable()
                                ->placeholder('Select billing address'),
                        ]),
                    Forms\Components\Wizard\Step::make('Payment & Shipping')
                        ->schema([
                            Forms\Components\Select::make('shipping_method_id')
                                ->relationship('shippingMethod', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('payment_method_id')
                                ->relationship('paymentMethod', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\TextInput::make('subtotal')->numeric()->prefix('VNĐ')->required(),
                            Forms\Components\TextInput::make('shipping_fee')->numeric()->prefix('VNĐ')->default(0),
                            Forms\Components\TextInput::make('discount_amount')->numeric()->prefix('VNĐ')->default(0),
                            Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('VNĐ')->default(0),
                            Forms\Components\TextInput::make('total_amount')->numeric()->prefix('VNĐ')->required(),
                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Notes')
                        ->schema([
                            Forms\Components\Textarea::make('notes')->label('Customer Notes'),
                            Forms\Components\Textarea::make('admin_notes')->label('Admin Notes'),
                        ]),
                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => fn ($state) => in_array($state, ['payment_pending', 'confirmed', 'processing']),
                        'success' => fn ($state) => in_array($state, ['shipped', 'delivered']),
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'failed', 'refunded']),
                    ])->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->money('vnd')->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label('Payment')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'payment_pending' => 'Payment Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class, // OrderItemRelationManager
            RelationManagers\PromotionsRelationManager::class, // Nếu cần quản lý khuyến mãi đã áp dụng
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'), // Có thể không cần tạo đơn hàng thủ công nhiều
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            // 'view' => Pages\ViewOrder::route('/{record}'), // View page rất quan trọng cho đơn hàng
        ];
    }

     public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->orderBy('created_at', 'desc'); // Sắp xếp đơn hàng mới nhất lên đầu
    }
}