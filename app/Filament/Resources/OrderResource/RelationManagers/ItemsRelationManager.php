<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\Summarizers\Sum; // Import the Sum summarizer

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'product_name';

    public function form(Form $form): Form
    {
        // ... (form definition remains the same)
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('product_name', $product->name);
                            $set('price', $product->sale_price ?: $product->regular_price);
                        }
                    })
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get, $state) => $set('subtotal', (float)$state * (float)$get('price'))),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, Get $get, $state) => $set('subtotal', (float)$state * (float)$get('quantity'))),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('VNĐ')
                    ->disabled()
                    ->dehydrated(),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product')
                    ->default(fn ($record) => $record->product_name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price')->money('vnd'),
                Tables\Columns\TextColumn::make('subtotal')
                    ->money('vnd')
                    ->summarize( // <-- Attach summarizer to the column
                        Sum::make() // No need to specify 'subtotal' here, it's inferred from the column
                            ->label('Total') // This label appears in the summary cell for *this* column
                            ->money('vnd')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = (float)$data['quantity'] * (float)$data['price'];
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subtotal'] = (float)$data['quantity'] * (float)$data['price'];
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            // No separate ->summaries() or ->footer() needed in this case
    }
}