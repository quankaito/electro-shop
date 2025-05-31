<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 3; // Sau Post

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question')
                    ->required()
                    ->rows(3),
                Forms\Components\RichEditor::make('answer') // Hoặc MarkdownEditor
                    ->required()
                    ->columnSpanFull(),
                // Forms\Components\Select::make('faq_category_id') // Nếu có bảng faq_categories
                //     ->relationship('faqCategory', 'name')
                //     ->searchable(),
                Forms\Components\Toggle::make('is_active')->required()->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')->limit(50)->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
    public static function getPages(): array { return ['index' => Pages\ListFaqs::route('/'), 'create' => Pages\CreateFaq::route('/create'), 'edit' => Pages\EditFaq::route('/{record}/edit')]; }
}