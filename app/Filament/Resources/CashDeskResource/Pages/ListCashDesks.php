<?php

namespace App\Filament\Resources\CashDeskResource\Pages;

use App\Filament\Resources\CashDeskResource;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCashDesks extends ListRecords
{
    protected static string $resource = CashDeskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make("Cerrar Caja")
                ->form([
                    \Filament\Forms\Components\Group::make()
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('cash_float')
                                ->label('Fondo')
                                ->numeric()
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('cash_amount')
                                ->label('Efectivo Contabilizado')
                                ->numeric()
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('card_amount')
                                ->label('Totales datáfono')
                                ->numeric()
                                ->required(),
                        ])
                        ->columns(1),
                ])
                ->action(function (array $data) {
                    \App\Models\CashDesk::create([
                        'cash_float' => $data['cash_float'],
                        'cash_amount' => $data['cash_amount'],
                        'card_amount' => $data['card_amount'],
                    ]);
                })
                ->after(function (array $data, Action $action) {
                    $storeId = session('store_id');
                    $lastCashDesk = \App\Models\CashDesk::where('store_id', $storeId)
                        ->latest()
                        ->first();

                    $diffCash = abs(($lastCashDesk->cash_amount ?? 0) - $data['cash_amount']);
                    $diffCard = abs(($lastCashDesk->card_amount ?? 0) - $data['card_amount']);

                    if ($diffCash === 0 && $diffCard === 0) {
                        $action->notify('success', 'Todo correcto');
                    } else {
                        $action->notify(
                            'warning',
                            "Diferencia detectada: Efectivo {$diffCash}, Tarjeta {$diffCard}"
                        );
                    }
                })

        ];
    }
}
