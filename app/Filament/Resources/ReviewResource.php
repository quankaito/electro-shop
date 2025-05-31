<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 6; // Sau Order

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'), // Không cho sửa sản phẩm của review
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'), // Không cho sửa người review
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Approved')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('rating')->badge()
                    ->colors([
                        'danger' => fn ($state): bool => $state < 3,
                        'warning' => fn ($state): bool => $state == 3,
                        'success' => fn ($state): bool => $state > 3,
                    ])->sortable(),
                Tables\Columns\TextColumn::make('comment')->limit(50)->searchable(),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('Approved'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')->relationship('product', 'name')->label('Product'),
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approval Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Action để Approve nhiều review
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
    public static function getPages(): array { return ['index' => Pages\ListReviews::route('/'), 'edit' => Pages\EditReview::route('/{record}/edit')]; } // Không cần create
}