<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(15)
                    ->unique(ignoreRecord: true),

                /**
                 * Avatar:
                 *  - Thêm ->disk('cloudinary') để upload lên Cloudinary.
                 *  - Khi bản ghi bị xóa hoặc avatar thay đổi, Filament tự xóa ảnh cũ trên Cloudinary.
                 */
                Forms\Components\FileUpload::make('avatar')
                    ->image()
                    ->directory('avatars')        // Thư mục trên Cloudinary sẽ là "avatars/"
                    ->disk('cloudinary')         // Upload lên Cloudinary
                    ->avatar()                   // Hiển thị khung avatar tròn
                    ->preserveFilenames(false)   // Đặt tên file tự động
                    ->maxSize(1024), // Giới hạn 2MB

                Forms\Components\DateTimePicker::make('email_verified_at'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state)) // Chỉ hash khi có giá trị
                    ->required(fn (string $context): bool => $context === 'create'), // Bắt buộc khi tạo mới

                Forms\Components\Toggle::make('is_admin')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /**
                 * Đổi thành lấy ảnh từ Cloudinary:
                 *  - Thêm ->disk('cloudinary') để Filament biết URL.
                 *  - Circular() hiển thị avatar tròn.
                 */
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->disk('cloudinary')
                    ->height(40)
                    ->width(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\AddressesRelationManager::class,
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
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
