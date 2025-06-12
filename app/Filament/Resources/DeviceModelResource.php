<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceModelResource\Pages;
use App\Filament\Resources\DeviceModelResource\RelationManagers\ItemsRelationManager;
use App\Models\DeviceModel;
use DB;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class DeviceModelResource extends Resource
{
    protected static ?string $model = DeviceModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $label = 'Modelos';

    public static ?string $navigationGroup = 'Catálogo';
    
    public static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label("Nombre")
                    ->required(),
                Forms\Components\Select::make('brand_id')
                    ->label("Marca")
                    ->relationship('brand', 'name')
                    ->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->label("Marca")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('brand.name', 'asc')
            ->filters([
                SelectFilter::make('brand_id')
                    ->options(DB::table('brands')->orderBy('name', 'asc')->pluck('name', 'id')->toArray())
                    ->label('Marca')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/device-models/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
            ]);
        ;
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeviceModels::route('/'),
            'create' => Pages\CreateDeviceModel::route('/create'),
            'edit' => Pages\EditDeviceModel::route('/{record}/edit'),
        ];
    }
}
