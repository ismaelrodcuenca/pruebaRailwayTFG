<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use App\Filament\Resources\ItemResource;
use app\Helpers\PermissionHelper;
use App\Http\Controllers\InvoiceController;
use App\Models\Category;
use App\Models\Item;
use App\Models\Type;
use constants;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\HtmlString;

class ItemWorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'itemWorkOrders';

    protected static ?string $title = 'Items';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Group::make([
                Toggle::make('only_model_items')
                    ->label('Mostrar solo items del modelo')
                    ->default(true)
                    ->reactive()
                    ->columnSpan('full')
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if ($state) {
                            $set('item_id', null);
                        }
                    }),
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->searchable(fn($get) => $get('only_model_items') ? false : true)
                    ->options(function ($get) {
                        $parent = $this->getOwnerRecord();
                        if ($get('only_model_items') && $parent->device && $parent->device->model->id) {
                            return Item::whereHas('deviceModels', function ($query) use ($parent) {
                                $query->where('device_model_id', $parent->device->model->id);
                            })->pluck('name', 'id');
                        }
                    })
                    ->columnSpan('full')
                    ->required(),
                TextInput::make('modified_amount')
                    ->label('Precio Modificado')
                    ->numeric()
                    ->columnSpan('full'),
            ])
                ->label('Añadir Item')
                ->columns(1),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')->label('Titulo'),
                TextColumn::make('modified_amount')->label('Precio')
                    ->state(fn($record) => $record->modified_amount ?? $record->item->price)
                    ->color(fn($record) => $record->modified_amount ? 'warning' : 'black')
                    ->money("EUR")
                    ->summarize([
                        Summarizer::make()
                            ->label(function(){
                                $total = InvoiceController::calcularTotal($this->getOwnerRecord()->id);
                                return $total."€";
                            })
                    ]),
            ])
            ->contentFooter(view('footer_items'))
            ->headerActions([
                Action::make('crearNuevoItem')
                    ->label('Crear Item')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
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
                            ->options(Type::orderBy('name')->pluck('name', 'id')->toArray())
                            ->label(constants::TYPE),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->options(Category::orderBy('name')->pluck('name','id')->toArray())
                            ->required()
                            ->label(constants::CATEGORY),
                        Toggle::make('link_to_stores')
                            ->label('Asociar a todas las tiendas')
                            ->required()
                            ->default(true)
                            ->helperText("En caso de no querer asociarlo a todas las tiendas, desmarca esta opción y asocia manualmente en la pestaña de 'Tiendas'"),
                        Toggle::make('link_item_device_model')
                            ->label('Asociar a un modelo de dispositivo')
                            ->default(false)
                            ->helperText("En caso de no querer asociarlo a un modelo de dispositivo, desmarca esta opción y asocia manualmente en la pestaña de 'Modelos '")
                            ->reactive(),
                        TextInput::make('device_model_id')
                            ->visible(false)
                            ->default(fn($get)=> $get('link_item_device_model') ? null : $this->getOwnerRecord()->device?->model?->id),
                        
                    ])
                    ->action(function (array $data, $livewire) {
                        $item = Item::create([
                            'name' => $data['name'],
                            'price' => $data['price'],
                            'cost' => $data['cost'] ?? null,
                            'distributor' => $data['distributor'] ?? null,
                            'type_id' => $data['type_id'],
                            'category_id' => $data['category_id'],
                        ]);
                        if (!empty($data['link_item_device_model'])) {
                            $deviceModelId = $data['device_model_id'] ?? null;
                            if ($deviceModelId) {
                                $item->deviceModels()->attach($deviceModelId);
                            } else {
                                $parent = $livewire->getOwnerRecord();
                                $device = $parent->device ?? null;
                                if ($device && $device->model->id) {
                                    $item->deviceModels()->attach($device->model->id);
                                }
                            }
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Item creado')
                            ->body('El nuevo item ha sido creado correctamente.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\CreateAction::make()
                    ->label("Añadir Item")
                    ->icon('heroicon-o-plus')
                    ->visible(function () {
                        return PermissionHelper::canAddItems($this->getOwnerRecord());
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()

                    ->label("Modificar")
                    ->visible(PermissionHelper::canAddItems($this->getOwnerRecord()))
                    ->icon('heroicon-o-currency-euro')
                    ->color('warning')
                    ->form([
                        TextInput::make('modified_amount')
                            ->numeric()
                            ->required()
                            ->default(fn($record) => $record->pivot->modified_amount)
                            ->afterStateUpdated(function ($state, $record) {
                                $record->update(['modified_amount' => $state]);
                            }),
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->label("Quitar")
                    ->icon('heroicon-o-trash')
                    ->visible(fn($record) => PermissionHelper::canAddItems($this->getOwnerRecord())),
                ]);
    }
}
