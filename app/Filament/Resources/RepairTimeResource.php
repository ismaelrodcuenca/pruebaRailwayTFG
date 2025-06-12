<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairTimeResource\Pages;
use App\Filament\Resources\RepairTimeResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\RepairTime;
use constants;
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
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class RepairTimeResource extends Resource
{
    protected static ?string $model = RepairTime::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $label = 'Tiempo De Reparación ';

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
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
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
            'index' => Pages\ListRepairTimes::route('/'),
            'create' => Pages\CreateRepairTime::route('/create'),
            'edit' => Pages\EditRepairTime::route('/{record}/edit'),
        ];
    }
}