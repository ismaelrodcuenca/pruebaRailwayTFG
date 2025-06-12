<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public static ?string $title = 'Facturas';
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de emisión')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('invoice_number')->label('Número de factura'),
                Tables\Columns\TextColumn::make('total')->label('Importe total')
                    ->suffix(' €'),
                Tables\Columns\IconColumn::make('is_down_payment')
                    ->label('Anticipo')
                    ->boolean(),
                TextColumn::make('work_order_id')
                    ->label('Número de pedido')
                    ,
            ]);
    }
}
