<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use constants;
use App\Helpers\PermissionHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static ?string $navigationGroup = 'Miscelanea';

    protected static ?string $label = 'Categoria';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isAdmin();
    }
    public static function authorization()
    {
        return [false];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label("Nombre")->required(),
                Select::make('tax_id')->label("Porcentaje")->relationship('tax', 'percentage')->required()->suffix('%')
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Titulo")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tax.name')
                    ->label("Impuestos")
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tax.percentage')
                    ->label("Porcentaje")
                    ->sortable()
                    ->searchable()->suffix('%'),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make()
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
