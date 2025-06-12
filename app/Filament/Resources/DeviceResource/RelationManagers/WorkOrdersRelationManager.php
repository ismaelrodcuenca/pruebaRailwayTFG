<?php

namespace App\Filament\Resources\DeviceResource\RelationManagers;

use App\Models\Device;
use App\Models\Store;
use App\Models\WorkOrder;
use constants;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'workOrders';

    protected static ?string $title = 'Hoja de Pedidos';
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->schema([
                    Forms\Components\Placeholder::make("Dispositivo")
                        ->content(function () {
                            $modelo = $this->getOwnerRecord()->model->name ?? 'ni modelo';
                            $marca = $this->getOwnerRecord()->model->brand->name ?? 'Sin marca';
                            return $marca . " " . $modelo;
                        }),

                    Placeholder::make("Tienda")
                        ->content(function () {
                            $store = Store::findOrFail(session('store_id'), ['name']);
                            return $store['name'];
                        }),
                    Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->dehydrated(true),
                    Hidden::make('store_id')
                        ->default(session('store_id'))
                        ->dehydrated(true),
                    Hidden::make('device_id')
                        ->default(function () {
                            return $this->getOwnerRecord()->id;
                        })
                        ->dehydrated(true),
                    Hidden::make('work_order_number_warranty')
                        ->dehydrated(true)
                        ->default(null),
                    Toggle::make('is_warranty')
                        ->default(false)
                        ->dehydrated(true),
                ]),

                Forms\Components\Textarea::make('failure')
                    ->label('Failure')
                    ->default(fn($record) => $record?->failure)
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('private_comment')
                    ->label('Private Comment')
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->nullable()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('physical_condition')
                    ->label('Physical Condition')
                    ->required()
                    ->columnSpan('full')
                    ->dehydrated(true),
                Forms\Components\Textarea::make('humidity')
                    ->label('Humidity')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Textarea::make('test')
                    ->label('Test')
                    ->required()
                    ->columnSpan('full'),
                Forms\Components\Select::make('repair_time_id')
                    ->label('Repair Time')
                    ->relationship('repairTime', 'name')
                    ->required(),

            ])
            ->Actions([
                DeleteAction::make('ELIMINAR')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('work_order_number')
                    ->label('Numero de Pedido')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default')
                    ->searchable(),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Tienda')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('work_order_number_warranty')
                    ->label('Pedido de Garantía')
                    ->icon(fn($record) => $record->work_order_number_warranty ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Último Status')
                    ->getStateUsing(function ($record) {
                        return $record->statusWorkOrders->last()->status->name ;
                    })
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default'),
                Tables\Columns\TextColumn::make('repairTime.name')
                    ->color(fn($record) => $record->is_warranty ? 'success' : 'default')
                    ->label('Tiempo de Reparación'),
            ])
            ->recordUrl(fn($record) => url("/dashboard/work-orders/{$record->id}/edit"))
            ->recordTitleAttribute('work_order_number')
           
            ->headerActions([
                CreateAction::make('create')
                    ->label('Crear Pedido')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Grid::make(3)
                            ->schema([
                                Section::make("Dispositivo")
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(function (callable $get, $livewire) {
                                                $device = $livewire->getOwnerRecord();
                                                if (!$device) {
                                                    return 'Sin dispositivo';
                                                }
                                                $modelo = $device->model->name ?? 'Modelo desconocido';
                                                $marca = $device->model->brand->name ?? 'Marca desconocida';
                                                return "{$marca} {$modelo}";
                                            }),
                                    ])
                                    ->icon("heroicon-o-device-phone-mobile")
                                    ->columnSpan(1),

                                Section::make("Tienda")
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(function () {
                                                $store = Store::findOrFail(session('store_id'), ['name']);
                                                return $store['name'];
                                            }),
                                    ])
                                    ->icon('heroicon-o-building-storefront')
                                    ->columnSpan(1),

                                Section::make("Creado Por")
                                    ->schema([
                                        Placeholder::make("")
                                            ->content(fn() => auth()->user()->name ?? ''),
                                    ])
                                    ->icon('heroicon-o-user-circle')
                                    ->columnSpan(1)
                            ]),

                        Section::make('Cliente')
                            ->icon('heroicon-s-user')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        Section::make("Documento")
                                            ->schema([
                                                Placeholder::make("")
                                                    ->content(function ($get, $livewire) {
                                                        $device = $livewire->getOwnerRecord();
                                                        return $device->client->document ?? 'Sin Cliente';
                                                    }),
                                            ])
                                            ->columnSpan(1),

                                        Section::make("Nombre")
                                            ->schema([
                                                Placeholder::make("")
                                                    ->content(function ($livewire) {
                                                        $device = $livewire->getOwnerRecord();
                                                        $name = $device->client->name ?? 'John';
                                                        $surname = $device->client->surname ?? 'Doe';
                                                        $surname2 = $device->client->surname2 ?? null;
                                                        if ($surname2) {
                                                            return $name . " " . $surname . " " . $surname2;
                                                        }
                                                        return $name . " " . $surname;
                                                    }),
                                            ])
                                            ->columnSpan(2),

                                        Section::make("Telefono")
                                            ->schema([
                                                Placeholder::make("")
                                                    ->content(function ($get, $livewire) {
                                                        $device = $livewire->getOwnerRecord();
                                                        return $device->client->phone_number ?? '696 696 696';
                                                    }),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        Group::make()->schema([
                            Hidden::make('user_id')
                                ->default(auth()->user()->id)
                                ->dehydrated(true),
                            Hidden::make('store_id')
                                ->default(session('store_id'))
                                ->dehydrated(true),
                            Hidden::make('device_id')
                                ->default(function ($livewire) {
                                    return $livewire->getOwnerRecord()->id;
                                })
                                ->dehydrated(true),
                            Hidden::make('work_order_number_warranty')
                                ->dehydrated(true)
                                ->default(null),
                            Hidden::make('is_warranty')
                                ->default(false)
                                ->dehydrated(true),
                        ]),

                        Textarea::make('failure')
                            ->label('Avería')
                            ->default(fn($record) => $record?->failure)
                            ->required()
                            ->columnSpan('full'),
                        Textarea::make('private_comment')
                            ->label('Comentario Privado')
                            ->nullable()
                            ->columnSpan('full'),
                        Textarea::make('comment')
                            ->label('Comentario')
                            ->nullable()
                            ->columnSpan('full'),
                        Textarea::make('physical_condition')
                            ->label('Condición Física')
                            ->required()
                            ->columnSpan('full')
                            ->dehydrated(true),
                        Textarea::make('humidity')
                            ->label('Humedad')
                            ->required()
                            ->columnSpan('full'),
                        Textarea::make('test')
                            ->label('Test')
                            ->required()
                            ->columnSpan('full'),
                        Select::make('repair_time_id')
                            ->label('Tiempo de Reparación')
                            ->relationship('repairTime', 'name')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        WorkOrder::create($data);
                        Notification::make()
                            ->title('Pedido creado correctamente.')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Crear Pedido')
            ]);
    }
}
