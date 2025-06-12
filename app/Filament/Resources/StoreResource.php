<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $label = 'Tiendas';

    public static ?string $navigationGroup = 'Recursos';

    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isManager();
    }

    public static function form(Form $form): Form
    {
        return $form
           ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label("Nombre")
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->label('Dirección')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('prefix')
                        ->required()
                        ->label('Prefijo')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('number')
                        ->required()
                        ->label('Número de Teléfono')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->label("Email")
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('schedule')
                        ->label('Horario')
                        ->required()
                        ->maxLength(255),
                ]);

    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prefix')
                    ->label('Prefijo')
                    ->searchable()
                    ->toggleable(true, true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('Número de Teléfono')
                    ->searchable()
                    ->toggleable(true, true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(true, true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->label('Horario')
                    ->toggleable(true, true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->disabled(PermissionHelper::isNotAdmin())
                ->hidden(PermissionHelper::isNotAdmin()),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
