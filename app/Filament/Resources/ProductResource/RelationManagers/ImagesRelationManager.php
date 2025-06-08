<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $recordTitleAttribute = 'alt_text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->directory('products')
                    ->disk('cloudinary')
                    ->required()
                    ->deleteUploadedFileUsing(function ($state): void {
                        $disk = Storage::disk('cloudinary');
                        foreach ((array) $state as $path) {
                            if (is_array($path) && isset($path['path'])) {
                                $disk->delete($path['path']);
                            } elseif (is_string($path)) {
                                $disk->delete($path);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('alt_text')
                    ->label('Alt Text')
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_thumbnail')
                    ->label('Is Thumbnail?'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Preview')
                    ->disk('cloudinary')
                    ->height(80)
                    ->width(80),

                Tables\Columns\TextColumn::make('alt_text')
                    ->label('Alt Text'),

                Tables\Columns\IconColumn::make('is_thumbnail')
                    ->label('Thumbnail?')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record): void {
                        if ($record->image_path) {
                            Storage::disk('cloudinary')->delete($record->image_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            foreach ($records as $record) {
                                if ($record->image_path) {
                                    Storage::disk('cloudinary')->delete($record->image_path);
                                }
                            }
                        }),
                ]),
            ]);
    }
}
