<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                // Upload ảnh desktop lên Cloudinary
                Forms\Components\FileUpload::make('image_url_desktop')
                    ->label('Desktop Image')
                    ->image()
                    ->directory('banners/desktop')
                    ->disk('cloudinary')            // ← Lưu lên disk "cloudinary"
                    ->preserveFilenames(false)      // Đặt false để Filament tự sinh file name ngẫu nhiên (đảm bảo ko trùng)
                    ->required()
                    ->maxSize(2048), // 2 MB max (2048 KB)

                // Upload ảnh mobile (nếu có) lên Cloudinary
                Forms\Components\FileUpload::make('image_url_mobile')
                    ->label('Mobile Image (Optional)')
                    ->image()
                    ->directory('banners/mobile')
                    ->disk('cloudinary')            // ← Lưu lên disk "cloudinary"
                    ->preserveFilenames(false)
                    ->maxSize(1024),

                Forms\Components\TextInput::make('link_url')
                    ->label('Link URL (Optional)')
                    ->url()
                    ->maxLength(255),

                Forms\Components\TextInput::make('position')
                    ->helperText('E.g., homepage_slider, category_top_banner')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),

                Forms\Components\DateTimePicker::make('start_date')->nullable(),
                Forms\Components\DateTimePicker::make('end_date')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Hiển thị ảnh desktop từ Cloudinary
                Tables\Columns\ImageColumn::make('image_url_desktop')
                    ->label('Desktop Image')
                    ->disk('cloudinary')       // ← Lấy URL từ disk "cloudinary"
                    ->height(80)
                    ->width(120),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->reorderable('sort_order')    // Cho phép kéo thả sắp xếp theo sort_order
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
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
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
