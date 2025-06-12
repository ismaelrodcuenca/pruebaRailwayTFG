<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use app\Helpers\PermissionHelper;
use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';
    protected static ?string $title = 'Facturas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->disabledOn('edit')
                            ->reactive()
                            ->minValue(0)
                            ->visibleOn(! 'edit')
                            ->default(fn ($record) => InvoiceController::calcularTotal($record::getOwnerRecord()->id))
                            ->suffix('€'),
                    ]),
                Card::make('Importes')
                    ->columns(4)
                    ->schema([
                        Placeholder::make('Total Pagado:')
                            ->content(fn ($record) => InvoiceController::calcularTotal($record->id) . ' €')
                            ->columnSpan(1),
                        Placeholder::make('Total Pendiente')
                            ->content(fn ($record) => InvoiceController::calcularPendiente($record->id) . ' €')
                            ->columnSpan(1),
                        Placeholder::make('Base imponible')
                            ->content(fn ($record) => InvoiceController::calcularBase($record->id) . ' €')
                            ->columnSpan(1),
                        Placeholder::make('Impuestos')
                            ->content(fn ($record) => InvoiceController::calcularImpuestos($record->id) . ' €')
                            ->columnSpan(1),
                    ]),
                Grid::make(2)
                    ->schema([
                        Select::make('company_id')
                            ->label('Buscar Empresa')
                            ->searchable(['cif', 'name', 'corporate_name'])
                            ->createOptionForm([
                                TextInput::make('cif')->label('CIF')->required(),
                                TextInput::make('name')->label('Name')->required(),
                                TextInput::make('corporate_name')->label('Corporate Name')->required(),
                                TextInput::make('address')->label('Address')->required(),
                                TextInput::make('postal_code')->label('Postal Code')->required(),
                                TextInput::make('locality')->label('Locality')->required(),
                                TextInput::make('province')->label('Province')->required(),
                                TextInput::make('discount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0)
                                    ->suffix('%'),
                            ])
                            ->createOptionUsing(fn (array $data) => \App\Models\Company::create($data))
                            ->relationship('company', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->cif . ' - ' . $record->name)
                            ->columnSpan(1)
                            ->placeholder("Buscar"),
                        Toggle::make('is_down_payment')
                            ->disabledOn('edit')
                            ->label('Anticipo'),
                    ]),
                Textarea::make('comment')
                    ->label('Comentario')
                    ->placeholder('Comentario sobre el cobro si aplica')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'default')
                    ->label('Factura')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Importe')
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'default')
                    ->alignCenter()
                    ->money('eur', true),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'default')
                    ->label('Método de pago')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->alignCenter()
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'default')
                    ->getStateUsing(fn ($record) => $record->company
                        ? $record->company->corporate_name
                        : 'NO'),
                Tables\Columns\IconColumn::make('is_down_payment')
                    ->label('Anticipo')
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'default')
                    ->alignCenter()
                    ->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('Devolución Total')
                    ->label('Devolución Total')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('refund_payment_method_id')
                            ->label('Método de devolución')
                            ->options(PaymentMethod::all()->pluck('name', 'id'))
                            ->default(1)
                            ->searchable()
                            ->required(),
                        Textarea::make('comment')
                            ->label('Comentario de devolución')
                            ->placeholder('Comentario sobre la devolución si aplica'),
                    ])
                    ->action(function ($record, array $data) {
                        $dataOverride = [
                            'payment_method_id' => $data['refund_payment_method_id'],
                            'comment'           => $data['comment'],
                        ];
                        InvoiceController::generateRefundsForWorkOrder(
                            $this->getOwnerRecord()->id,
                            $dataOverride
                        );
                    })
                    ->visible(fn () => PermissionHelper::canBeRefunded($this->getOwnerRecord())),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('Cambiar método')
                    ->label('Cambiar método')
                    ->icon('heroicon-o-credit-card')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('payment_method_id')
                            ->label('Método de pago')
                            ->options(PaymentMethod::all()->pluck('name', 'id'))
                            ->default(fn ($record) => $record->payment_method_id)
                            ->searchable()
                            ->required(),
                        Textarea::make('comment')
                            ->label('Comentario')
                            ->placeholder('Comentario sobre el cambio de método si aplica'),
                    ])
                    ->action(function ($record) {
                        $record->update([
                            'payment_method_id' => request()->input('payment_method_id'),
                        ]);
                    })
                    ->visible(fn () => PermissionHelper::canBeRefunded($this->getOwnerRecord())),
                \Filament\Tables\Actions\Action::make('Devolución')
                    ->label('Devolución')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->disabled(fn ($record) => $record->is_refund)
                    ->requiresConfirmation()
                    ->form([
                        Select::make('payment_method_id')
                            ->label('Método de devolución')
                            ->options(PaymentMethod::all()->pluck('name', 'id'))
                            ->default(1)
                            ->searchable()
                            ->required(),
                        Textarea::make('comment')
                            ->label('Comentario de devolución')
                            ->placeholder('Comentario sobre la devolución si aplica'),
                    ])
                    ->action(function ($record, array $data) {
                        $dataOverride = [
                            'payment_method_id' => $data['payment_method_id'],
                            'comment'           => $data['comment'],
                        ];
                        InvoiceController::createRefundInvoice($record->id, $dataOverride);
                    })
                    ->visible(fn () => PermissionHelper::canBeRefunded($this->getOwnerRecord())),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
