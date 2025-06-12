<?php

namespace App\Filament\Resources\DeviceModelResource\RelationManagers;

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

     public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Costo')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->toggleable()
                    ->suffix('€'),
                Tables\Columns\TextColumn::make('distributor')
                    ->label('Distribuidor')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
            ])
            ->recordUrl(fn($record) => url("/dashboard/items/{$record->id}/edit"))
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/items/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
            ]);
    }
}