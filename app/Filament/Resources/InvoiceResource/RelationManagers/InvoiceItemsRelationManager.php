<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Carbon\Carbon;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoiceItems';
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
            Tables\Columns\TextColumn::make('item.name')
                ->label("Titulo")
                ->toggleable()
                ->alignCenter()
                ->sortable(),
            Tables\Columns\TextColumn::make('modified_amount')
                ->label("Precio")
                ->sortable()
                ->numeric()
                ->state(fn($record)=>$record->modified_amount ?? $record->item->price)
                ->alignCenter()
                ->suffix('€')
                ->toggleable(true, false),
            Tables\Columns\TextColumn::make('item.cost')
                ->label('Coste')
                ->sortable()
                ->suffix('€')
                ->alignCenter()
                ->toggleable(true),
            Tables\Columns\TextColumn::make('item.distributor')
                ->label("Distribuidor")
                ->alignCenter()
                ->toggleable(),
            Tables\Columns\TextColumn::make('item.type.name')
                ->label("Tipo")
                ->sortable()
                ->alignCenter()
                ->toggleable(),
            Tables\Columns\TextColumn::make('item.category.name')
                ->label("Categoria")
                ->sortable()
                ->alignCenter()
                ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hidden(fn($record) => Carbon::parse($record->created_at)->notEqualTo(now())),
                Tables\Actions\DeleteAction::make()
                ->hidden(fn($record) => Carbon::parse($record->created_at)->notEqualTo(now())),
            ]);
    }
}
