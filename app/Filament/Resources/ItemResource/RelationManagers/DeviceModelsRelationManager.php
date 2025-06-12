<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use App\Models\Brand;
use App\Models\DeviceModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceModelsRelationManager extends RelationManager
{
    protected static string $relationship = 'deviceModels';


    public static ?string $title = 'Modelos';
        public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('brand.name'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form([
                        Forms\Components\Select::make('brand_id')
                            ->label('Marca')
                            ->options(Brand::all()->pluck('name', 'id'))
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('device_model_id')
                            ->label('Modelo')
                            // Filtra modelos según la marca seleccionada
                            ->options(function (callable $get) {
                                $brandId = $get('brand_id');
                                if (!$brandId) {
                                    return DeviceModel::query()
                                        ->pluck('name', 'id');
                                }
                                return DeviceModel::where('brand_id', $brandId)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),
                    ])
                    ->action(fn($data)=>$this->getOwnerRecord()->deviceModels()->attach($data['device_model_id']))
                    ->label('Añadir modelo')
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/device-models/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
                    DetachAction::make()
            ])
            ->recordUrl(fn($record) => url("/dashboard/device-models/{$record->id}/edit"));
    }
}
