<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Filament\Resources\WorkOrderResource\RelationManagers\ClosureRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\ItemWorkOrdersRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\StatusRelationManager;
use App\Filament\Resources\WorkOrderResource\RelationManagers\StatusWorkOrdersRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Device;
use App\Models\Store;
use App\Models\WorkOrder;
use Date;
use DB;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    

    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::hasRole();
    }
    public static function getGloballySearchableAttributes(): array
    {
        return PermissionHelper::hasRole() ? [
            'work_order_number',
        ] : [];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'work_order_number' => ($record->work_order_number . " " . $record->device->model->brand->name . " - " . $record->device->model->name),
        ];
    }

    protected static ?string $label = 'Hojas de pedido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Section::make("Dispositivo")
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(function (callable $get) {
                                                $deviceId = $get('device_id');
                                                if (!$deviceId) {
                                                    return 'Sin dispositivo';
                                                }
                                                $device = Device::with('model.brand')->find($deviceId);
                                                if (!$device) {
                                                    return 'Dispositivo no encontrado';
                                                }
                                                $modelo = $device->model->name ?? 'Modelo desconocido';
                                                $marca = $device->model->brand->name ?? 'Marca desconocida';
                                                return "{$marca} - {$modelo}";
                                            }),
                                    ])
                                    ->icon("heroicon-o-device-phone-mobile")
                                    ->columnSpan(1),

                                Section::make("Tienda")
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(function ($record) {
                                                $store = Store::find($record->store_id);
                                                return $store['name'] ?? 'Sin Tienda';
                                            }),
                                    ])
                                    ->icon('heroicon-o-building-storefront')
                                    ->columnSpan(1),
                                Section::make('Estado')
                                    ->icon('heroicon-o-clock')
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(function ($record) {
                                                $lastStatusWorkOrder = $record->statusWorkOrders->last();
                                                $lastStatus = $record->statusWorkOrders->last()->status->name ?? 'Sin Estado';
                                                $hasInvoices = $record->invoices()->exists();
                                                if ($lastStatus == "PENDIENTE" && $hasInvoices) {
                                                    $lastStatus .= " - CON ANTICIPO";
                                                }
                                                if(!(auth()->user()->id === $lastStatusWorkOrder->user_id )) {
                                                    $lastStatus .= " - " . $lastStatusWorkOrder->user->name;
                                                }elseif (auth()->user()->id === $lastStatusWorkOrder->user_id) {
                                                     $lastStatus .= " - TÚ";
                                                }
                                                return $lastStatus ?? 'Sin Estado';
                                            }),
                                    ])->columnSpan(1),
                            ]),
                    ]),
                //Sección para el cliente
                Section::make('Cliente')
                    ->icon('heroicon-s-user')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Sección para el documento del cliente
                                Section::make("Documento")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->document ?? 'Sin Cliente'),

                                    ])
                                    ->columnSpan(1),
                                // Sección para el nombre del cliente
                                Section::make("Nombre")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(function (callable $get) {
                                                $name = Device::find($get('device_id'))->client->name ?? 'John';
                                                $surname = Device::find($get('device_id'))->client->surname ?? 'Doe';
                                                $surname2 = Device::find($get('device_id'))->client->surname2;
                                                if ($surname2) {
                                                    return $name . " " . $surname . " " . $surname2;
                                                }
                                                return $name . " " . $surname;
                                            }),

                                    ])
                                    ->columnSpan(1),

                                // Sección para el teléfono del cliente
                                Section::make("Teléfono")
                                    ->schema([

                                        Placeholder::make("")
                                            ->content(fn(callable $get) => Device::find($get('device_id'))->client->phone_number ?? '696 696 696'),

                                    ])
                                    ->columnSpan(1),

                            ]),
                    ]),
                //Seccion oculta con los IDs necesarios
                Group::make()->schema([
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->dehydrated(true),
                    Hidden::make('store_id')
                        ->default(session('store_id'))
                        ->dehydrated(true),
                    Hidden::make('device_id')
                        ->default(function (callable $get) {
                            return $get('device_id') ?? '';
                        })
                        ->dehydrated(true),
                    Hidden::make('work_order_number_warranty')
                        ->dehydrated(true)
                        ->default(null),
                ]),

                //Sección para los datos del pedido
                Forms\Components\Textarea::make('failure')
                    ->label('Avería')
                    ->default(fn($record) => $record?->failure)
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Textarea::make('private_comment')
                    ->label('Comentario privado')
                    ->nullable()
                    ->columnSpan('full')
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentario')
                    ->nullable()
                    ->columnSpan('full')
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Textarea::make('physical_condition')
                    ->label('Estado físico')
                    ->required()
                    ->columnSpan('full')
                    ->dehydrated(true)
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Textarea::make('humidity')
                    ->label('Humedad')
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Textarea::make('test')
                    ->label('Prueba')
                    ->required()
                    ->columnSpan('full')
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),
                Forms\Components\Select::make('repair_time_id')
                    ->label('Tiempo de reparación')
                    ->relationship('repairTime', 'name')
                    ->required()
                    ->disabled(fn($record) => PermissionHelper::canBeEdited($record)),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('work_order_number')
                    ->label('Nº Hoja de pedido')
                    ->alignCenter()
                    ->state(fn($record) => $record->work_order_number_warranty ? $record->work_order_number . " (" . $record->work_order_number_warranty . ")" : $record->work_order_number)
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client')
                    ->label('Cliente')
                    ->alignCenter()
                    ->state(fn($record) => $record->device->client->name . " " . $record->device->client->surname)
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->searchable(),
                    Tables\Columns\TextColumn::make("ownerDevice")
                    ->label('Dispositivo')
                    ->alignCenter()
                    ->state(fn($record) => $record->device->model->brand->name .' '. $record->device->model->name)
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Tienda')
                    ->alignCenter()
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->visible(PermissionHelper::isAdmin())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('Status')
                    ->state(fn($record) => $record->statusWorkOrders->last()->status->name ?? 'Sin Estado')
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->alignCenter()
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('repairTime.name')
                    ->color(fn($record) => $record->work_order_number_warranty ? 'success' : 'default')
                    ->alignCenter()
                    ->state(function ($record) {
                        $status = $record->repairTime->name ?? 'Sin Tiempo de Reparación';
                        if (stripos($status, 'AVISAR') !== false) {
                            return 'SE AVISARÁ...';
                        }
                        return $status;
                    })
                    ->label('Tiempo de reparación'),
            ])

            ->recordTitleAttribute('work_order_number')
            ->filters([
                Filter::make("waiting")
                    ->label('Mostrar pendientes')
                    ->default(true)
                    ->query(function (Builder $query): Builder {
                        $query->whereDoesntHave('closure')->whereDoesntHave('statusWorkOrders', function (Builder $query) {
                            $query->whereIn('status_id', [3, 4, 5, 7, 8, 9]);
                        });
                        return $query;
                    }),
                Filter::make('warranty')
                    ->label('Mostrar solo garantías')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('work_order_number_warranty')),

                Filter::make('cancelled')
                    ->label('Mostrar cancelados')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('statusWorkOrders', function (Builder $query) {
                            $query->where('status_id', 5);
                        });
                    }),
                Filter::make('closed')
                    ->label('Mostrar cerrados')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('closure');
                    }),
                Filter::make('created_at')
                    ->label('Fecha de creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde')
                            ->placeholder('Fecha inicio')
                            ->default(now()->subDays(7)),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta')
                            ->placeholder('Fecha fin')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['created_from'])) {
                            $query->where('created_at', '>=', Date::make($data['created_from'])->startOfDay());
                        }
                        if (isset($data['created_until'])) {
                            $query->where('created_at', '<=', Date::make($data['created_until'])->endOfDay());
                        }
                        return $query;
                    }),

                SelectFilter::make('stores')
                    ->label('Tienda')
                    ->relationship('store', 'name')
                    ->visible(PermissionHelper::isAdmin())
                    ->options(auth()->user()->stores()->pluck('name', 'stores.id')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->hidden(fn($record) => $record->created_at
                        ? \Illuminate\Support\Carbon::parse($record->created_at)->diffInMinutes(now()) > 7
                        : true),
            ])
            ->query(function () {
                if (PermissionHelper::isNotAdmin()) {
                    return WorkOrder::query()
                        ->where('store_id', session('store_id'))->orderBy('work_order_number', 'desc');
                }

                return WorkOrder::query()->orderBy('work_order_number', 'desc');

            });
    }
    public static function getRelations(): array
    {
        return [
            ItemWorkOrdersRelationManager::class,
            InvoicesRelationManager::class,
            StatusWorkOrdersRelationManager::class,
            ClosureRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),

        ];
    }
}
