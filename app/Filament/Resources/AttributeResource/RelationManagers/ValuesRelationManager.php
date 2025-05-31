<?php

namespace App\Filament\Resources\AttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values'; // Tên relationship trong model Attribute
    protected static ?string $recordTitleAttribute = 'value';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    // Nên unique theo attribute_id + slug
                    ->unique(
                        table: 'attribute_values',
                        column: 'slug',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule, Get $get) {
                            // $this->ownerRecord is the Attribute model instance
                            return $rule->where('attribute_id', $this->ownerRecord->id);
                        }
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('value')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug'),
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