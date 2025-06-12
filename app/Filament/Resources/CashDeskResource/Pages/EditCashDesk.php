<?php

namespace App\Filament\Resources\CashDeskResource\Pages;

use App\Filament\Resources\CashDeskResource;
use App\Helpers\PermissionHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashDesk extends EditRecord
{
    protected static string $resource = CashDeskResource::class;
protected function getHeaderActions(): array
    {
        return[];
    }
}
