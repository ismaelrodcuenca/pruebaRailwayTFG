<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Filament\Resources\TypeResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Type;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;
    public static ?string $navigationGroup = 'Miscelanea';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Tipos de Items';

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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
