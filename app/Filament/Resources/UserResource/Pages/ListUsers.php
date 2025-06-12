<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Helpers\PermissionHelper;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->hidden(PermissionHelper::isNotAdmin())
            ->action(fn () => User::where('id', $this->record->id)->update(['active' => !$this->record->active])),
        ];
    }
    protected function getTableActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
