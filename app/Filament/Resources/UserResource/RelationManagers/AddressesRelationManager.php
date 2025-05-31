<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Province; // Import
use App\Models\District; // Import
use Filament\Forms\Get; // Import
use Illuminate\Support\Collection; //Import

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    protected static ?string $recordTitleAttribute = 'address_line1'; // Hoặc full_name

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('phone_number')->tel()->required()->maxLength(15),
                Forms\Components\TextInput::make('address_line1')->required()->maxLength(255),
                Forms\Components\TextInput::make('address_line2')->maxLength(255),
                Forms\Components\Select::make('province_id')
                    ->label('Province/City')
                    ->options(Province::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('district_id', null))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('district_id')
                    ->label('District/County')
                    ->options(function (Get $get) {
                        $province = Province::find($get('province_id'));
                        if (!$province) {
                            return District::all()->pluck('name', 'id');
                        }
                        return $province->districts()->pluck('name', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('ward_id', null))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('ward_id')
                    ->label('Ward/Commune')
                     ->options(function (Get $get) {
                        $district = District::find($get('district_id'));
                        if (!$district) { return Collection::empty(); }
                        return $district->wards()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('is_default_shipping'),
                Forms\Components\Toggle::make('is_default_billing'),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('full_address')->label('Full Address'),
                Tables\Columns\IconColumn::make('is_default_shipping')->boolean(),
                Tables\Columns\IconColumn::make('is_default_billing')->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}