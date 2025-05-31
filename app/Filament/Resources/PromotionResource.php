<?php

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options([
                        'percentage' => 'Percentage Discount',
                        'fixed_amount' => 'Fixed Amount Discount',
                    ])
                    ->required()
                    ->default('percentage')
                    ->reactive(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->prefix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : 'VNĐ'),
                Forms\Components\TextInput::make('max_discount_amount')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->nullable()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'percentage'),
                Forms\Components\TextInput::make('min_order_value')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->nullable(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date')
                    ->nullable(),
                Forms\Components\TextInput::make('usage_limit_per_code')
                    ->numeric()
                    ->nullable()->label('Usage Limit (Total)'),
                Forms\Components\TextInput::make('usage_limit_per_user')
                    ->numeric()
                    ->nullable()->label('Usage Limit (Per User)'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record, string $state): string => $record->type === 'percentage' ? "{$state}%" : number_format($state) . ' VNĐ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('times_used')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
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