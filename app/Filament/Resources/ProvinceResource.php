<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $label = 'Province/City';
    protected static ?string $pluralLabel = 'Provinces/Cities';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')->required()->maxLength(10)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('country_code')->default('VN')->required()->maxLength(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make(), /* Tables\Actions\DeleteAction::make() */]) // Cẩn thận khi xóa
            ->bulkActions([/* Tables\Actions\DeleteBulkAction::make() */]);
    }
    public static function getPages(): array { return ['index' => Pages\ListProvinces::route('/')]; } // Chỉ cần list, edit
}