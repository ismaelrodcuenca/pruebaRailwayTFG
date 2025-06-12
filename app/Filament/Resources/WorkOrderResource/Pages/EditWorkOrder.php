<?php

namespace App\Filament\Resources\WorkOrderResource\Pages;

use App\Filament\Resources\WorkOrderResource;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WorkOrderController;
use App\Models\Client;
use App\Models\Closure;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\ItemWorkOrder;
use App\Models\PaymentMethod;
use App\Models\RepairTime;
use App\Models\Status;
use App\Models\StatusWorkOrder;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use SeekableIterator;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->disabled(fn() => PermissionHelper::canBeEdited($this->record));
    }
    protected function getHeaderActions(): array
    {
        return [
           
            Action::make('PDF')
            ->label(function ($record) {
                $facturadoStatusId = Status::where('name', 'ENTREGADO')->first()?->id;
                $facturadoWorkOrders = $record->statusWorkOrders->where('status_id', $facturadoStatusId);

                return $facturadoWorkOrders->isNotEmpty() ? 'Factura' : 'Hoja Pedido';
            })
            ->icon('heroicon-o-printer')
            ->color('primary')
            ->url(fn($record) => route('generateWorkOrder', ['id' => $record->id]))
            ->openUrlInNewTab(condition: true),
            Action::make('Garantía')
                ->icon('heroicon-o-plus-circle')
                ->color(Color::hex('#083b5d'))
                ->visible(fn() => PermissionHelper::canAddWarranty($this->record))
                ->openUrlInNewTab()
                ->form([
                    TextInput::make('failure')
                        ->label('Avería')
                        ->placeholder('Describe la avería')
                        ->default(fn($record) => $record->failure)
                        ->required(),

                    Textarea::make('private_comment')
                        ->label('Comentario privado')
                        ->placeholder('Solo visible internamente'),

                    Textarea::make('comment')
                        ->label('Comentario')
                        ->placeholder('Comentario para el cliente'),

                    TextInput::make('physical_condition')
                        ->label('Condición física')
                        ->placeholder('Describe la condición física del equipo'),

                    TextInput::make('humidity')
                        ->label('Humedad')
                        ->placeholder('¿Se ha encontrado humedad?'),

                    TextInput::make('test')
                        ->label('Test')
                        ->placeholder('¿Test realizado?'),

                    Select::make('repair_time_id')
                        ->label('Tiempo de reparación')
                        ->options(RepairTime::all()->pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('Selecciona un tiempo de reparación'),
                ])
                ->action(
                    function (array $data, $record) {
                        // Crear un nuevo WorkOrder con los datos proporcionados
                        WorkOrder::create([
                            'work_order_number' => null,
                            'is_warranty' => true,
                            'work_order_number_warranty' => $record->work_order_number,
                            'failure' => $data['failure'] ?? $record->failure,
                            'private_comment' => $data['private_comment'] ?? null,
                            'comment' => $data['comment'] ?? null,
                            'physical_condition' => $data['physical_condition'] ?? null,
                            'humidity' => $data['humidity'] ?? null,
                            'test' => $data['test'] ?? null,
                            'user_id' => auth()->user()->id,
                            'repair_time_id' => $data['repair_time_id'] ?? null,
                            'device_id' => $record->device_id,
                            'store_id' => session('store_id'),
                        ]);
                    }
                ),
            Action::make('cobro')
                ->label('Cobrar')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn() => PermissionHelper::canBeBilled($this->record))
                ->modalHeading('Cobro')
                ->form(function ($record) {
                    return [
                        Grid::make(2)
                            ->label("Introducir Importe")
                            ->schema([
                                Select::make('payment_method_id')
                                    ->label('Método de pago')
                                    ->options(PaymentMethod::all()->pluck('name', 'id'))
                                    ->default(1)
                                    ->searchable()
                                    ->required(),
                                TextInput::make('full_amount')
                                    ->label('Importe a cobrar')
                                    ->numeric()
                                    ->reactive()
                                    ->minValue(1)
                                    ->maxValue(InvoiceController::calcularPendiente($record->id))
                                    ->default(fn($record) => InvoiceController::calcularPendiente($record->id))
                                    ->suffix('€'),
                            ]),
                        Card::make('Importes')
                            ->columns(4)
                            ->schema([
                                Placeholder::make('total')
                                    ->label('Total')
                                    ->reactive()
                                    ->content(fn($record) => InvoiceController::calcularTotal($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('Total Pendiente')
                                    ->content(fn($record) => InvoiceController::calcularPendiente($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('base')
                                    ->label('Base Imponible')
                                    ->content(fn($record) => InvoiceController::calcularBase($record->id) . ' €')
                                    ->columnSpan(1),
                                Placeholder::make('taxes')
                                    ->label('Impuestos')
                                    ->content(fn($record) => InvoiceController::calcularImpuestos($record->id) . ' €')
                                    ->columnSpan(1),
                            ]),
                        Section::make("Opciones")->schema([
                            Grid::make(2)->schema([
                                Select::make('company_id')
                                    ->label('Buscar Empresa')
                                    ->searchable(['cif', 'name', 'corporate_name'])
                                    ->createOptionUsing(function (array $data) {
                                        return Company::create($data);
                                    })
                                    ->options(
                                        Company::all()->mapWithKeys(fn($company) => [
                                            $company->id => $company->cif . ' - ' . $company->name
                                        ])
                                    )
                                    ->createOptionForm([
                                        TextInput::make('cif')
                                            ->label('CIF')
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required(),
                                        TextInput::make('corporate_name')
                                            ->label('Corporate Name')
                                            ->required(),
                                        TextInput::make('address')
                                            ->label('Address')
                                            ->required(),
                                        TextInput::make('postal_code')
                                            ->label('Postal Code')
                                            ->required(),
                                        TextInput::make('locality')
                                            ->label('Locality')
                                            ->required(),
                                        TextInput::make('province')
                                            ->label('Province')
                                            ->required(),
                                        TextInput::make('discount')
                                            ->label('Discount')
                                            ->numeric()->minValue(0)->maxValue(100)
                                            ->default(0)->suffix('%'),

                                    ])
                                    ->columnSpan(1)
                                    ->placeholder("Buscar"),
                                Toggle::make('is_down_payment')
                                    ->label('Anticipo')
                                    ->default(false)
                                    ->columnSpan(1)
                                    ->helperText('Marcar si es un anticipo'),
                            ])
                        ]),
                        TextInput::make('comment')
                            ->label('Comentario')
                            ->placeholder('Comentario sobre el cobro si aplica'),
                    ];
                })
                ->action(function (array $data, $record) {
                    Invoice::create([
                        'invoice_number' => null,
                        'base' => InvoiceController::calcularBase($record->id),
                        'taxes' => InvoiceController::calcularImpuestos($record->id),
                        'total' => $data['full_amount'],
                        'is_refund' => false,
                        'is_down_payment' => $data['is_down_payment'] ?? false,
                        'work_order_id' => $record->id,
                        'client_id' => $record->device->client->id,
                        'store_id' => $record->store_id,
                        'company_id' => $data['company_id'],
                        'payment_method_id' => $data['payment_method_id'],
                        'user_id' => auth()->user()->id,
                        'comment' => $data['comment'] ?? null,
                    ]);

                    if (InvoiceController::isFullyPayed($record->id) && $record->closure != null) {
                        StatusWorkOrder::create([
                            'status_id' => Status::where('name', 'FACTURADO')->first()->id,
                            'work_order_id' => $record->id,
                            'user_id' => auth()->user()->id,
                        ]);
                      
                    }

                    Notification::make()
                        ->title('Cobro realizado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                })
                ->after(function ($record) {
                    if (InvoiceController::isFullyPayed($this->record->id) && $record->closure != null) {

                        StatusWorkOrder::create([
                            'status_id' => Status::where('name', 'ENTREGADO')->first()->id,
                            'work_order_id' => $this->record->id,
                            'user_id' => auth()->user()->id,
                        ]);

                    }
                }),

            Action::make('entregar')
                ->label('Entregar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(PermissionHelper::canBeDelivered($this->record))
                ->requiresConfirmation()
                ->action(function ($record) {
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'ENTREGADO')->first()->id,
                        'work_order_id' => $record->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Pedido entregado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),
            Action::make('Devolución')
                ->icon('heroicon-o-credit-card')
                ->color(Color::Orange)
                ->visible(fn() => PermissionHelper::canBeRefunded($this->record))
                ->openUrlInNewTab()
                ->form([
                    Placeholder::make('total')
                        ->content(fn() => (InvoiceController::calcularTotal($this->record->id) * -1) . " €"),
                    Select::make('payment_method_id')
                        ->label('Método de devolución')
                        ->default(1)
                        ->options(PaymentMethod::all()->pluck('name', 'id')),
                    Textarea::make('comment')
                        ->label('Comentario')
                        ->placeholder('Comentario sobre la devolución si aplica'),
                ])
                ->action(fn() => InvoiceController::generateRefundsForWorkOrder($this->record->id, [
                    'payment_method_id' => request()->input('payment_method_id'),
                    'comment' => request()->input('comment'),
                ])),

            Action::make('Asignar')
                ->icon('heroicon-o-hand-raised')
                ->color(Color::Teal)
                ->requiresConfirmation()
                ->visible(PermissionHelper::canBeAssigned($this->record))
                ->openUrlInNewTab()
                ->action(function () {
                    $workOrder = $this->record;
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'EN REPARACIÓN')->first()->id,
                        'work_order_id' => $workOrder->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Asignado a ' . auth()->user()->name . '.')
                        ->success()
                        ->send();
                }),
            Action::make('Reparado')
                ->icon('heroicon-o-check')
                ->color(Color::Sky)
                ->visible(fn() => PermissionHelper::canBeRepaired($this->record))
                ->openUrlInNewTab()
                ->form([
                    TextInput::make('test')
                        ->label('Test')
                        ->placeholder("¿Test realizado?")
                        ->required(),
                    Textarea::make('comment')
                        ->label('Comment')
                        ->placeholder("¿Qué se ha hecho en la reparación?")
                        ->required(),
                    TextInput::make('humidity')
                        ->label('Humidity')
                        ->placeholder("¿Se ha encontrado humedad en taller?")
                        ->required(),
                    Select::make('user_id')
                        ->hidden()
                        ->dehydrated(true),
                ])
                ->action(function ($data) {
                    $this->record->closure()->create([
                        'test' => $data['test'],
                        'comment' => $data['comment'],
                        'humidity' => $data['humidity'],
                        'user_id' => auth()->user()->id,
                    ]);
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'COMPLETADO')->first()->id,
                        'work_order_id' => $this->record->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Pedido reparado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),

            Action::make('Pdte Pieza')
                ->icon('heroicon-o-puzzle-piece')
                ->color(Color::Amber)
                ->requiresConfirmation()
                ->visible(PermissionHelper::canBeBackorder($this->record))
                ->openUrlInNewTab()
                ->action(function () {
                    $workOrder = $this->record;
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'PENDIENTE DE PIEZA')->first()->id,
                        'work_order_id' => $workOrder->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('.')
                        ->success()
                        ->send();
                }),
            Action::make('Cancelar')
                ->icon('heroicon-o-x-circle')
                ->color(Color::Red)
                ->requiresConfirmation()
                ->visible(PermissionHelper::canBeCanceled($this->record))
                ->openUrlInNewTab()
                ->action(function () {
                    $workOrder = $this->record;
                    StatusWorkOrder::create([
                        'status_id' => Status::where('name', 'CANCELADO')->first()->id,
                        'work_order_id' => $workOrder->id,
                        'user_id' => auth()->user()->id,
                    ]);
                    Notification::make()
                        ->title('Pedido cancelado.')
                        ->success()
                        ->send();
                }),
            Action::make("Info")
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->action(fn() => PermissionHelper::infoNotification($this->record))
        ];
    }
}
