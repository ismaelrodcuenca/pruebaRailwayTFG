<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxResource\Pages;
use App\Filament\Resources\TaxResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use constants;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';
    protected static ?string $label = 'Impuesto';
    public static ?string $navigationGroup = 'Miscelanea';
    
   public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(constants::NAME)->required(),
                TextInput::make('percentage')->label(constants::PERCENTAJE)->required()->numeric()->prefix('%'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(constants::NAME_TYPO)
                ->sortable()
                ->searchable(),
                TextColumn::make('percentage')
                ->label(constants::PERCENTAJE)
                ->sortable()
                ->searchable(),
            ])
            ->filters([
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
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
