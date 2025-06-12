<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $label = 'Empresas';
    
    public static ?string $navigationGroup = 'Recursos';

    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('cif')
                    ->label('CIF')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('corporate_name')
                    ->label('Razón Social')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->required(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código Postal')
                    ->required(),
                Forms\Components\TextInput::make('locality')
                    ->label('Localidad')
                    ->required(),
                Forms\Components\TextInput::make('province')
                    ->label('Provincia')
                    ->required(),
                Forms\Components\TextInput::make('discount')
                    ->label('Descuento')
                    ->numeric()->minValue(0)->maxValue(100)
                    ->default(0)
                    ->suffix('%'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cif')
                    ->label('CIF')
                    ->searchable()
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('corporate_name')
                    ->label('Razón Social')
                    ->searchable()
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Código Postal')
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('locality')
                    ->label('Localidad')
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('province')
                    ->label('Provincia')
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('discount')
                    ->label('Descuento')
                    ->toggleable(true)
                    ->suffix('%')
                    ->alignCenter()
                    ->numeric()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
