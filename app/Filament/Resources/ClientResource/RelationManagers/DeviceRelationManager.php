<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Filament\Resources\DeviceResource\Pages\CreateDevice;
use App\Models\Client;
use Closure;
use constants;
use DB;
use Filament\Actions\EditAction;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceRelationManager extends RelationManager
{
    protected static string $relationship = 'devices';

    public static ?string $title = 'Dispositivos';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Datos del cliente")
                    ->icon('heroicon-o-user')
                    ->schema([
                        Split::make([
                            Placeholder::make('Documento')
                                ->content(function (callable $get) {
                                    $clientId = $get('client_id') ?? $this->getOwnerRecord()?->id;
                                    if (!$clientId) {
                                        return 'Sin cliente seleccionado';
                                    }
                                    $client = Client::find($clientId);
                                    if (!$client) {
                                        return 'Cliente no encontrado';
                                    }
                                    $documentTypeName = $client->documentType ? $client->documentType->name : 'Tipo de documento desconocido';
                                    return "{$documentTypeName}: {$client->document}";
                                }),

                            Placeholder::make("Nombre")
                                ->content(function (callable $get) {
                                    
                                    $clientId = $get('client_id') ?? $this->getOwnerRecord()?->id;
                                    if (!$clientId) {
                                        return 'Sin cliente seleccionado';
                                    }
                                    $client = Client::find($clientId);
                                    if (!$client) {
                                        return 'Cliente no encontrado';
                                    }
                                    return "{$client->name}";
                                }),
                            Placeholder::make("Apellidos")
                                ->content(function (callable $get) {
                                    $clientId = $get('client_id') ?? $this->getOwnerRecord()?->id;
                                    if (!$clientId) {
                                        return 'Sin cliente seleccionado';
                                    }
                                    $client = Client::find($clientId);
                                    if (!$client) {
                                        return 'Cliente no encontrado';
                                    }
                                    $surname2 = $client->surname2 ?? '';
                                    return "{$client->surname} {$surname2}";
                                }),
                            Forms\Components\Select::make('client_id')
                                ->required()
                                ->hidden(),
                        ]),
                    ])
                    ->columnSpan('full'),

                Section::make("Datos del dispositivo")
                    ->icon('heroicon-o-device-phone-mobile')
                    ->schema([
                        Section::make([
                            Forms\Components\Toggle::make('has_no_serial_or_imei')
                                ->label('No Serial or IMEI')
                                ->default(false)
                                ->reactive(),
                            Forms\Components\TextInput::make('serial_number')
                                ->label(constants::SERIAL_NUMBER)
                                ->nullable(fn(callable $get) => $get('has_no_serial_or_imei') === true)
                                ->reactive()
                                ->required(
                                    function (callable $get) {
                                        $hasNoSerialOrImei = $get('has_no_serial_or_imei');
                                        $imeiEmpty = empty($get('IMEI'));
                                        if ($hasNoSerialOrImei) {
                                            return false;
                                        }
                                        return $imeiEmpty;
                                    }
                                )
                                ->dehydrated(
                                    fn(callable $get) => !$get('has_no_serial_or_imei') === true
                                )
                                ->disabled(fn(callable $get) => $get('has_no_serial_or_imei') === true),
                            Forms\Components\TextInput::make('IMEI')
                                ->label(constants::IMEI)
                                ->nullable(fn(callable $get) => $get('has_no_serial_or_imei') === true)
                                ->required(
                                    function (callable $get) {
                                        $hasNoSerialOrImei = $get('has_no_serial_or_imei');
                                        $serialEmpty = empty($get('serial_number'));
                                        if ($hasNoSerialOrImei) {
                                            return false;
                                        }
                                        return $serialEmpty;
                                    }
                                )
                                ->reactive()
                                ->dehydrated(
                                    fn(callable $get) => !$get('has_no_serial_or_imei') === true
                                )
                                ->disabled(fn(callable $get) => $get('has_no_serial_or_imei') === true)
                                ->minLength(function (callable $get) {
                                    if ($get('has_no_serial_or_imei')) {
                                        return 0;
                                    }
                                    return 15;
                                })
                                ->maxLength(function (callable $get) {
                                    if ($get('has_no_serial_or_imei')) {
                                        return 0;
                                    }
                                    return 15;
                                })
                        ]),

                        Split::make([
                            Section::make()
                                ->schema([
                                    Select::make('brand_id')
                                        ->label('Marca')
                                        ->options(fn() => DB::table('brands')->orderBy('name', 'ASC')->pluck('name', 'id')->toArray())
                                        ->reactive()
                                        ->afterStateUpdated(fn(callable $set) => $set('device_model_id', null))
                                        ->required(),

                                    Select::make('device_model_id')
                                        ->label('Modelo')
                                        ->options(function (callable $get) {
                                            $brandId = $get('brand_id');
                                            return $brandId
                                                ? DB::table('device_models')->where('brand_id', $brandId)->orderBy('name', 'ASC')->pluck('name', 'id')->toArray()
                                                : [];
                                        })
                                        ->placeholder('Seleccione una Marca')
                                        ->required(),
                                ])
                                ->columnSpan(1), 

                            Section::make()
                                ->schema([
                                    TextInput::make('colour')
                                        ->label(constants::COLOUR)
                                        ->required(),

                                    TextInput::make('unlock_code')
                                        ->label(constants::UNLOCK_CODE)
                                        ->nullable(),
                                ])
                                ->columnSpan(1),
                        ])



                    ]),





            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                ->label('Crear Dispositivo'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('model.brand.name')
                    ->label('Marca'),
                Tables\Columns\TextColumn::make('model.name')
                    ->label(constants::MODELO),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label(constants::SERIAL_NUMBER)
                    ->default('Sin Serial'),
                Tables\Columns\TextColumn::make('IMEI')
                    ->label(constants::IMEI)
                    ->default('Sin IMEI'),
                Tables\Columns\TextColumn::make('colour')
                    ->label(constants::COLOUR),
                Tables\Columns\TextColumn::make('unlock_code')
                    ->label(constants::UNLOCK_CODE)
                    ->toggleable(),
            ])
            ->recordUrl(fn($record) => url("/dashboard/devices/{$record->id}/edit"))
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => url("/dashboard/devices/{$record->id}/edit"))
                    ->openUrlInNewTab(false),
            ]);
    }
}
