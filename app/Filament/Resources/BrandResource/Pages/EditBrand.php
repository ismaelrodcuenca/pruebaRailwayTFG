<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use App\Helpers\PermissionHelper;
use constants;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;



    protected function getHeaderActions(): array
    {
        return  [
        ];
    }
}
