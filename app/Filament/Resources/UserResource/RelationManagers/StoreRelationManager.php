<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Helpers\PermissionHelper;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreRelationManager extends RelationManager
{
    protected static string $relationship = 'stores';

    public function form(Form $form): Form
    {
        return $form

            ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label("Nombre"),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->label("Dirección")
                        ->maxLength(255),
                    Forms\Components\TextInput::make('prefix')
                        ->required()
                        ->label("Prefijo")
                        ->maxLength(255),
                    Forms\Components\TextInput::make('number')
                        ->required()
                        ->tel()
                        ->label('Número')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->label('Correo Electrónico')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('schedule')
                        ->required()
                        ->label('Horario')
                        ->maxLength(255),
                ]);
            }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->toggleable(true, true),
                Tables\Columns\TextColumn::make('number'),
                Tables\Columns\TextColumn::make('schedule')
                    ->toggleable(true, true),
            ])
            ->filters([

            ])
            ->headerActions([
                CreateAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
                AttachAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->actions([
                EditAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
                DetachAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DetachBulkAction::make()->visible(PermissionHelper::isAdmin()),
                ]),
            ]);
    }
}
