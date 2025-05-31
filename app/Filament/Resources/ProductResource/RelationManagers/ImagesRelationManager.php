<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->directory('products')            // nằm trong thư mục "products"
                    ->disk('cloudinary')               // ← sử dụng disk “cloudinary”
                    ->required()
                    ->deleteUploadedFileUsing(function (string $state): void {
                        // Khi người dùng bấm Delete trên Filament, Filament sẽ gọi callback này.
                        // $state chính là đường dẫn (public ID) trên Cloudinary, tự động do disk 'cloudinary' tạo.
                        \Illuminate\Support\Facades\Storage::disk('cloudinary')->delete($state);
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
                    ->disk('cloudinary')            // ← hiển thị từ Cloudinary
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
                // Khi bấm Create, Filament sẽ upload lên Cloudinary và lưu image_path là public ID
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record, $livewire) {
                        // Trước khi xóa bản ghi, xoá tệp trên Cloudinary
                        if ($record->image_path) {
                            \Illuminate\Support\Facades\Storage::disk('cloudinary')->delete($record->image_path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Xóa tệp của từng bản ghi trong bulk delete
                            foreach ($records as $record) {
                                if ($record->image_path) {
                                    \Illuminate\Support\Facades\Storage::disk('cloudinary')->delete($record->image_path);
                                }
                            }
                        }),
                ]),
            ]);
    }
}
