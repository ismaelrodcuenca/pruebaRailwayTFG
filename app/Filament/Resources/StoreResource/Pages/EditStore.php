<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Helpers\PermissionHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
