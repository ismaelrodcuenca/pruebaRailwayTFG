<?php

namespace App\Filament\Resources\StatusResource\Pages;

use App\Filament\Resources\StatusResource;
use App\Helpers\PermissionHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatus extends EditRecord
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
                return[];

    }
}
