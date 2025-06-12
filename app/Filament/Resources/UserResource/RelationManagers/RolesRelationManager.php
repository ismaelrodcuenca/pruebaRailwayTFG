<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'rolUser';

    protected static ?string $title = 'Roles';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->preload()
                    ->hidden()
                    ->default(fn() => $this->ownerRecord->id)
                    ->label('Usuario'),
                Select::make('rol_id')
                    ->relationship('rol', 'name')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->label('Rol')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('rol.name'),
            ])
            ->headerActions([
                
                Tables\Actions\CreateAction::make()
                ->label('Asignar Rol'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
