<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Exports\InvoiceExporter;
use App\Filament\Resources\InvoiceResource;
use app\Helpers\PermissionHelper;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
            ->visible(PermissionHelper::isAdmin())
                ->exporter(InvoiceExporter::class)
                ->label('Exportar')
                ->modifyQueryUsing(
                    fn($query) =>
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                )
        ];
    }
}
