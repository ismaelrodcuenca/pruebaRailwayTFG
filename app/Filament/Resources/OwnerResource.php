<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\Pages;
use App\Filament\Resources\OwnerResource\RelationManagers;
use app\Helpers\PermissionHelper;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;
    protected static ?string $label = "Datos Fiscales";
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Recursos';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isAdmin();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('corportate_name')
                    ->label('Razón Social')
                    ->required(),
                Forms\Components\TextInput::make('CIF')
                    ->label('CIF')
                    ->maxLength(20)
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código Postal')
                    ->maxLength(10)
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->label('Ciudad')
                    ->required(),
                Forms\Components\TextInput::make('province')
                    ->label('Provincia')
                    ->required(),
                Forms\Components\TextInput::make('country')
                    ->label('País')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->maxLength(30)
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('corporate_email')
                    ->label('Email Corporativo')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('website')
                    ->label('Sitio Web')
                    ->url()
                    ->nullable(),
                Forms\Components\TextInput::make('foundation_year')
                    ->label('Año de Fundación')
                    ->numeric()
                    ->nullable(),
                Forms\Components\TextInput::make('sector')
                    ->label('Sector')
                    ->nullable(),
                Forms\Components\Textarea::make('short_description')
                    ->label('Descripción Corta')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('corportate_name')
                    ->label('Nombre Corporativo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('CIF')
                    ->label('CIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Código Postal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province')
                    ->label('Provincia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('corporate_email')
                    ->label('Email Corporativo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Sitio Web'),
                Tables\Columns\TextColumn::make('foundation_year')
                    ->label('Año de Fundación')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Sector')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('short_description')
                    ->label('Descripción Corta')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->hidden(PermissionHelper::isNotAdmin()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOwners::route('/'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }
}
