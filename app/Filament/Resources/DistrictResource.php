<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin'; // Khác icon với province
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $label = 'District/County';
    protected static ?string $pluralLabel = 'Districts/Counties';
    protected static ?int $navigationSort = 1; // Để dưới Province

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('code')->required()->maxLength(10)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('province.name')->label('Province/City')->searchable()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('province_id')
                    ->relationship('province', 'name')
                    ->label('Filter by Province/City')
            ])
            ->actions([Tables\Actions\EditAction::make(), /* Tables\Actions\DeleteAction::make() */])
            ->bulkActions([/* Tables\Actions\DeleteBulkAction::make() */]);
    }
    public static function getPages(): array { return ['index' => Pages\ListDistricts::route('/')]; }
}