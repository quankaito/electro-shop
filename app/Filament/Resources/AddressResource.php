<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Province; // Import
use App\Models\District; // Import
use Filament\Forms\Get; // Import
use Illuminate\Support\Collection; //Import

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1; // Sau User

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('full_name')->required()->maxLength(255),
                Forms\Components\TextInput::make('phone_number')->tel()->required()->maxLength(15),
                Forms\Components\TextInput::make('address_line1')->label('Address Line 1 (Street, Number)')->required()->maxLength(255),
                Forms\Components\TextInput::make('address_line2')->label('Address Line 2 (Apartment, Suite, etc.)')->maxLength(255),

                Forms\Components\Select::make('province_id')
                    ->label('Province/City')
                    ->options(Province::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('district_id', null)) // Reset district khi province thay đổi
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
                    ->afterStateUpdated(fn (callable $set) => $set('ward_id', null)) // Reset ward khi district thay đổi
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('ward_id')
                    ->label('Ward/Commune')
                     ->options(function (Get $get) {
                        $district = District::find($get('district_id'));
                        if (!$district) {
                            return Collection::empty(); // Hoặc Ward::all() nếu muốn hiển thị tất cả ban đầu
                        }
                        return $district->wards()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('postal_code')->maxLength(10),
                Forms\Components\Toggle::make('is_default_shipping')->label('Default Shipping Address'),
                Forms\Components\Toggle::make('is_default_billing')->label('Default Billing Address'),
                Forms\Components\Select::make('type')
                    ->options([
                        'shipping' => 'Shipping',
                        'billing' => 'Billing',
                    ])->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('full_address')->label('Full Address')
                    ->getStateUsing(fn (Address $record): string => $record->full_address) // Sử dụng accessor
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('address_line1', 'like', "%{$search}%")
                            ->orWhere('address_line2', 'like', "%{$search}%")
                            ->orWhereHas('province', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('district', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('ward', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                Tables\Columns\IconColumn::make('is_default_shipping')->boolean()->label('Default Ship.'),
                Tables\Columns\IconColumn::make('is_default_billing')->boolean()->label('Default Bill.'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}