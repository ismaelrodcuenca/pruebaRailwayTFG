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
                    ->label("Titulo"),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->label("Costo"),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label("Precio"),
                Forms\Components\TextInput::make('distributor')
                    ->required()
                    ->label("Distribuidor"),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'name')
                    ->required()
                    ->label("Tipo"),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label("Categoria"),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label("Titulo")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label("Costo")
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label("Precio")
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor')
                    ->label("Distribuidor")
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label("Tipo")
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label("Categoria")
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
