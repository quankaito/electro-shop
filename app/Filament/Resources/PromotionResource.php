<?php
// app/Filament/Resources/PromotionResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin chính')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên khuyến mãi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Giá trị & Điều kiện')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Loại khuyến mãi')
                            ->options([
                                'percentage' => 'Giảm theo phần trăm (%)',
                                'fixed_amount' => 'Giảm số tiền cố định (VNĐ)',
                            ])
                            ->required()
                            ->default('percentage')
                            ->live(), // Sử dụng live() thay cho reactive() trong v3
                        Forms\Components\TextInput::make('value')
                            ->label('Giá trị')
                            ->required()
                            ->numeric()
                            ->prefix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : 'VNĐ'),
                        Forms\Components\TextInput::make('max_discount_amount')
                            ->label('Giảm tối đa (VNĐ)')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'percentage'),
                        Forms\Components\TextInput::make('min_order_value')
                            ->label('Giá trị đơn hàng tối thiểu (VNĐ)')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Thời gian & Giới hạn sử dụng')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Ngày kết thúc')
                            ->nullable()
                            ->helperText('Bắt buộc phải có ngày kết thúc để hiển thị trên trang chủ.'),
                        Forms\Components\TextInput::make('usage_limit_per_code')
                            ->numeric()
                            ->nullable()->label('Giới hạn lượt dùng (toàn hệ thống)'),
                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->numeric()
                            ->nullable()->label('Giới hạn lượt dùng (trên mỗi khách)'),
                    ])->columns(2),

                Forms\Components\Section::make('Trạng thái')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt khuyến mãi')
                            ->required()
                            ->default(true),
                        // === THAY ĐỔI BẮT ĐẦU TỪ ĐÂY ===
                        Forms\Components\Toggle::make('is_featured_on_home')
                            ->label('Nổi bật trên trang chủ')
                            ->helperText('Nếu bật, khuyến mãi này sẽ hiển thị ở khu vực nổi bật trên trang chủ. Chỉ nên có 1 khuyến mãi được bật tại một thời điểm.')
                            ->default(false),
                        // === THAY ĐỔI KẾT THÚC TẠI ĐÂY ===
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên khuyến mãi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->copyable()
                    ->copyableState(fn (string $state): string => "Mã: {$state}"),
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Giá trị')
                    ->formatStateUsing(fn ($record, string $state): string => $record->type === 'percentage' ? "{$state}%" : number_format($state) . ' VNĐ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Ngày kết thúc')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('times_used')
                    ->label('Đã dùng')
                    ->sortable(),

                // === THAY ĐỔI BẮT ĐẦU TỪ ĐÂY ===
                Tables\Columns\IconColumn::make('is_featured_on_home')
                    ->label('Nổi bật')
                    ->boolean()
                    ->sortable(),
                // === THAY ĐỔI KẾT THÚC TẠI ĐÂY ===

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed_amount' => 'Fixed Amount',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái kích hoạt'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}