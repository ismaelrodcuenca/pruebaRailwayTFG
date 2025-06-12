<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
class DeviceModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'device_models';

    protected static ?string $title = 'Modelos';
    
    protected static ?string $label = "Modelos";

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->recordUrl(fn($record) => url("/dashboard/device-models/{$record->id}/edit"))
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/device-models/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('name', 'asc');
    }
}
