<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Shop Management';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Details')->tabs([

                    // =========================
                    // Tab: General
                    // =========================
                    Forms\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(Product::class, 'slug', ignoreRecord: true),

                            Forms\Components\TextInput::make('sku')
                                ->label('SKU')
                                ->maxLength(100)
                                ->unique(Product::class, 'sku', ignoreRecord: true),

                            Forms\Components\RichEditor::make('short_description')
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull(),
                        ])->columns(2),


                    // =========================
                    // Tab: Pricing & Stock
                    // =========================
                    Forms\Components\Tabs\Tab::make('Pricing & Stock')
                        ->schema([
                            Forms\Components\TextInput::make('regular_price')
                                ->required()
                                ->numeric()
                                ->prefix('VNĐ'),

                            Forms\Components\TextInput::make('sale_price')
                                ->numeric()
                                ->prefix('VNĐ')
                                ->nullable(),

                            Forms\Components\Toggle::make('manage_stock')
                                ->label('Manage Stock?')
                                ->default(true)
                                ->reactive(),

                            Forms\Components\TextInput::make('stock_quantity')
                                ->numeric()
                                ->default(0)
                                ->visible(fn (Forms\Get $get) => $get('manage_stock')),

                            Forms\Components\Select::make('stock_status')
                                ->options([
                                    'in_stock'     => 'In Stock',
                                    'out_of_stock' => 'Out of Stock',
                                    'on_backorder' => 'On Backorder',
                                ])
                                ->required(),
                        ])->columns(2),


                    // =========================
                    // Tab: Organization
                    // =========================
                    Forms\Components\Tabs\Tab::make('Organization')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('brand_id')
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->preload(),
                        ])->columns(2),


                    // =========================
                    // Tab: Attributes & Variants
                    // =========================
                    Forms\Components\Tabs\Tab::make('Attributes & Variants')
                        ->schema([
                            Forms\Components\Placeholder::make('variants_info')
                                ->label('Product Variants')
                                ->content('Quản lý biến thể sản phẩm (ví dụ: màu sắc, kích thước) sau khi lưu sản phẩm, bằng Relation Manager “Variants” phía dưới.'),
                        ]),


                    // =========================
                    // Tab: Media (Ảnh sản phẩm)
                    // =========================
                    Forms\Components\Tabs\Tab::make('Media')
                        ->schema([
                            /**
                             * Nếu bạn đã có Relation Manager (ProductImageRelationManager)
                             * thì thực tế không cần upload riêng ở đây, bạn sẽ quản lý
                             * ảnh thông qua Relation Manager. Tuy nhiên, nếu muốn upload
                             * tất cả ảnh ngay trong form chính, ta có thể thay “images_upload”
                             * thành một FileUpload nhiều ảnh.
                             *
                             * Lưu ý: Filament sẽ tự xóa ảnh cũ trên Cloudinary khi bản ghi
                             * bị xóa hoặc khi thay thế file mới.
                             */
                            Forms\Components\FileUpload::make('images_upload')
                                ->label('Product Images')
                                ->multiple()
                                ->image()
                                ->directory('products')      // Thư mục trên Cloudinary sẽ là “products/”
                                ->disk('cloudinary')         // ← Dùng disk “cloudinary”
                                ->preserveFilenames(false)   // Tạo tên file ngẫu nhiên, tránh trùng
                                ->maxSize(1024) // 2MB
                                ->reorderable()              // Cho phép kéo thả sắp xếp
                                ->appendFiles()              // Giữ file cũ khi edit
                                ->columnSpanFull(),

                            Forms\Components\Placeholder::make('images_info')
                                ->label('Product Images')
                                ->content('Hoặc bạn có thể quản lý ảnh sản phẩm sau khi lưu, bằng Relation Manager “Images” phía dưới.'),
                        ]),


                    // =========================
                    // Tab: SEO
                    // =========================
                    Forms\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('meta_description'),

                            Forms\Components\TagsInput::make('meta_keywords'),
                        ]),


                    // =========================
                    // Tab: Status
                    // =========================
                    Forms\Components\Tabs\Tab::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('is_featured')
                                ->label('Featured Product')
                                ->default(false),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),

                            Forms\Components\DateTimePicker::make('published_at')
                                ->nullable(),
                        ])->columns(2),

                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Hiển thị Thumbnail (Ảnh đại diện) lấy từ relation “thumbnail”
                // Nếu thumbnail lưu đường dẫn lên Cloudinary, cần disk('cloudinary').
                Tables\Columns\ImageColumn::make('thumbnail.image_path')
                    ->label('Thumbnail')
                    ->disk('cloudinary')
                    ->height(50)
                    ->width(50),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('regular_price')
                    ->money('vnd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->money('vnd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // SỬA LẠI 2 BỘ LỌC NÀY
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(
                        \App\Models\Category::query()
                            ->whereNotNull('name') // Chỉ lấy category có tên
                            ->pluck('name', 'id')
                    ),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->options(
                        \App\Models\Brand::query()
                            ->whereNotNull('name') // Chỉ lấy brand có tên
                            ->pluck('name', 'id')
                    ),

                // Các bộ lọc khác giữ nguyên
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ImagesRelationManager::class,        // Quản lý bảng product_images
            RelationManagers\VariantsRelationManager::class,      // Quản lý biến thể
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
