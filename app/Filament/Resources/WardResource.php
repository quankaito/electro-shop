<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WardResource\Pages;
use App\Models\Ward;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WardResource extends Resource
{
    protected static ?string $model = Ward::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification'; // Khác icon
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $label = 'Ward/Commune';
    protected static ?string $pluralLabel = 'Wards/Communes';
    protected static ?int $navigationSort = 2; // Để dưới District

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('district_id')
                    ->relationship('district', 'name') // Cần đảm bảo district có 'name' hoặc kết hợp với province.name
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->district->name}"), // Hiển thị cả tên quận/huyện
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
                Tables\Columns\TextColumn::make('district.name')->label('District/County')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('district.province.name')->label('Province/City')->searchable()->sortable(),
            ])
            ->filters([
                 Tables\Filters\SelectFilter::make('district_id')
                    ->relationship('district', 'name') // Cần custom label
                    ->label('Filter by District/County')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->province->name}"),
            ])
            ->actions([Tables\Actions\EditAction::make(), /* Tables\Actions\DeleteAction::make() */])
            ->bulkActions([/* Tables\Actions\DeleteBulkAction::make() */]);
    }
    public static function getPages(): array { return ['index' => Pages\ListWards::route('/')]; }
}