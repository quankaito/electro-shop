<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ReviewResource; // Để link

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    protected static ?string $recordTitleAttribute = 'comment'; // Hoặc rating

    public function form(Form $form): Form
    {
        // Thường chỉ để sửa is_approved hoặc xem chi tiết
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->dehydrated(false), // Không lưu lại khi form chỉ để view
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Textarea::make('comment')
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Approved'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Customer'),
                Tables\Columns\TextColumn::make('rating')->badge()
                    ->colors([
                        'danger' => fn ($state): bool => $state < 3,
                        'warning' => fn ($state): bool => $state == 3,
                        'success' => fn ($state): bool => $state > 3,
                    ]),
                Tables\Columns\TextColumn::make('comment')->limit(50),
                Tables\Columns\IconColumn::make('is_approved')->boolean()->label('Approved'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approval Status'),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // User tạo review từ frontend
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(), // Chỉ để sửa is_approved
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\Action::make('approve_review')
                    ->label(fn ($record) => $record->is_approved ? 'Unapprove' : 'Approve')
                    ->icon(fn ($record) => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_approved ? 'warning' : 'success')
                    ->action(fn ($record) => $record->update(['is_approved' => !$record->is_approved]))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_approved' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('unapprove_selected')
                        ->label('Unapprove Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_approved' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public function canCreate(): bool
    {
        return false;
    }
}