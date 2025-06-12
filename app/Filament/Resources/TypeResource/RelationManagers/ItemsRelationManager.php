<?php

namespace App\Filament\Resources\TypeResource\RelationManagers;

use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(constants::NAME_TYPO),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->label(constants::COST),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label(constants::PRICE),
                Forms\Components\TextInput::make('distributor')
                    ->required()
                    ->label(constants::DISTRIBUTOR),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'name')
                    ->required()
                    ->label(constants::TYPE),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label(constants::CATEGORY),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::NAME_TYPO)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label(constants::COST)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(constants::PRICE)
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor')
                    ->label(constants::DISTRIBUTOR)
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label(constants::TYPE)
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(constants::CATEGORY)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
}
