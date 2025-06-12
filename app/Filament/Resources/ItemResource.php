<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers\DeviceModelsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\StoresRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Item;
use App\Models\Store;
use App\Models\Type;
use constants;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-battery-0';
    public static ?string $navigationGroup = 'Catálogo';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(constants::NAME_TYPO),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->label(constants::COST),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->label(constants::PRICE),
                Forms\Components\TextInput::make('distributor')
                    ->required()
                    ->label(constants::DISTRIBUTOR),
                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'name')
                    ->label(constants::TYPE),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label(constants::CATEGORY),
                Toggle::make('link_to_stores')
                    ->label('Asociar a todas las tiendas')
                    ->required()
                    ->hiddenOn('edit')
                    ->default(true)
                    ->helperText("En caso de no querer asociarlo a todas las tiendas, desmarca esta opción y asocia manualmente en la pestaña de 'Tiendas'"),
                Toggle::make('link_item_device_model')
                    ->label('Asociar a un modelo de dispositivo')
                    ->hiddenOn('edit')
                    ->default(false)
                    ->helperText("En caso de no querer asociarlo a un modelo de dispositivo, desmarca esta opción y asocia manualmente en la pestaña de 'Modelos '")
                    ->reactive(),
                Select::make('brand_id')
                    ->label('Marca')
                    ->options(Brand::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->visible(fn($get) => $get('link_item_device_model') === true)
                    ->reactive()
                    ->required(fn($get) => $get('link_item_device_model') === true),
                Select::make('device_model_id')
                    ->label('Modelo')
                    ->visible(fn($get) => $get('link_item_device_model') === true)
                    ->options(function ($get) {
                        $brandId = $get('brand_id');
                        if ($brandId) {
                            return DeviceModel::where('brand_id', $brandId)->orderBy('name')->pluck('name', 'id')->toArray();
                        }
                        return [];
                    })
                    ->required(fn($get) => $get('link_item_device_model') === true)
                    ->placeholder("Selecciona una marca"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(constants::NAME_TYPO)
                    ->searchable()
                    ->toggleable()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(constants::PRICE)
                    ->sortable()
                    ->numeric()
                    ->alignCenter()
                    ->suffix('€')
                    ->toggleable(true, false),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Coste')
                    ->sortable()
                    ->suffix('€')
                    ->alignCenter()
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('distributor')
                    ->label(constants::DISTRIBUTOR)
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label(constants::TYPE)
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(constants::CATEGORY)
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('device_model_id')
                    ->form([
                        Select::make('brand_id')
                            ->label('Marca')
                            ->options(Brand::orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('device_model_id', null);
                            }),
                        Select::make('device_model_id')
                            ->label('Modelo')
                            ->options(function ($get) {
                                $brandId = $get('brand_id');
                                if ($brandId) {
                                    return DeviceModel::where('brand_id', $brandId)->orderBy('name')->pluck('name', 'id')->toArray();
                                }
                                return [];
                            })
                            ->placeholder("Selecciona una marca")
                            ->searchable(),
                    ])
                    ->query(function ($query, $data) {
                        if (!empty($data['device_model_id'])) {
                            $query->whereHas('deviceModels', function ($q) use ($data) {
                                $q->where('device_models.id', $data['device_model_id']);
                            });
                        }
                    }),
                SelectFilter::make('category_id')
                    ->label('Categorias')
                    ->options(Category::all()->pluck('name', 'id')->toArray()),
                SelectFilter::make('type_id')
                    ->label('Tipo')
                    ->options(Type::all()->pluck('name', 'id')->toArray()),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            DeviceModelsRelationManager::class,
            StoresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
