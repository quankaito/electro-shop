<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 2; // Sau PostCategory

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\RichEditor::make('content') // Hoặc MarkdownEditor
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('excerpt')
                            ->columnSpanFull(),
                    ])->columnSpan(2), // Main content lấy 2/3 chiều rộng

                Forms\Components\Section::make('Meta')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('posts')           // Thư mục trên Cloudinary là “posts/”
                            ->disk('cloudinary')           // ← Sử dụng disk “cloudinary”
                            ->preserveFilenames(false)     // Tạo tên file ngẫu nhiên tránh trùng
                            ->maxSize(1024) // Giới hạn 2MB (tuỳ chọn)
                            ->imagePreviewHeight('150'),    // Preview cao 150px trong form

                        Forms\Components\Select::make('user_id')
                            ->relationship('author', 'name')
                            ->label('Author')
                            ->default(auth()->id())     // Tự điền user đang đăng nhập làm author
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'published' => 'Published',
                                'archived'  => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->helperText('Để trống để publish ngay khi status là "Published".'),

                        Forms\Components\Select::make('categories')
                            ->multiple()
                            ->relationship(titleAttribute: 'name') // Filament 3.x
                            ->preload()
                            ->label('Categories'),
                    ])->columnSpan(1), // Sidebar lấy 1/3 chiều rộng
            ])
            ->columns(3); // Tổng cộng 3 cột
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Hiển thị featured_image từ Cloudinary, kích thước 60×60
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Featured')
                    ->disk('cloudinary') // ← Lấy URL từ Cloudinary
                    ->height(60)
                    ->width(60),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray'    => 'draft',
                        'success' => 'published',
                        'warning' => 'archived',
                    ])->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categories')
                    ->listWithLineBreaks()
                    ->bulleted(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name'),
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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
