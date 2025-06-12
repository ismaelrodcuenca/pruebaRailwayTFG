<?php

namespace App\Filament\Resources\TypeResource\Pages;

use App\Filament\Resources\TypeResource;
use App\Helpers\PermissionHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditType extends EditRecord
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
