<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\RelationManagers\DeviceRelationManager;
use App\Filament\Resources\DeviceResource\Pages;
use App\Filament\Resources\DeviceResource\RelationManagers;
use App\Filament\Resources\DeviceResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\DeviceResource\RelationManagers\WorkOrdersRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Device;
use App\Models\DeviceModel;
use DB;
use DragonCode\Support\Facades\Helpers\Boolean;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use constants;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Dispositivo';

    public static ?string $navigationGroup = 'Catálogo';
    private ?int $brand_id;

    
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isManager();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Datos del cliente")
                    ->icon('heroicon-o-user')
                    ->schema([
                        Split::make([
                            Placeholder::make('Documento')
                                ->content(function (callable $get) {
                                    $clientId = $get('client_id');
                                    if (!$clientId) {
                                        return 'Sin cliente seleccionado';
                                    }
                                    $client = Client::find($clientId);
                                    if (!$client) {
                                        return 'Cliente no encontrado';
                                    }
                                    return "{$client->documentType->name}: {$client->document}";
                                }),

                            Placeholder::make("Nombre")
                                ->content(function (callable $get) {
                                    $clientId = $get('client_id');
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
                                    $clientId = $get('client_id');
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
                                ->default(false),
                            Forms\Components\TextInput::make('serial_number')
                                ->label(constants::SERIAL_NUMBER)
                                ->nullable(),
                            Forms\Components\TextInput::make('IMEI')
                                ->label(constants::IMEI)
                                ->nullable()
                        ]),

                        Split::make([
                            Section::make('')
                                ->schema([
                                    Select::make('brand_filter')
                                        ->label('Marca')
                                        ->options(fn() => Brand::orderBy('name')->pluck('name', 'id'))
                                        ->reactive()
                                        ->afterStateHydrated(function (callable $set, $record) {
                                            $model = $record->model;
                                            if ($model && $model->brand_id) {
                                                $set('brand_filter', $model->brand_id);
                                            }
                                        })
                                        ->afterStateUpdated(fn(callable $set) => $set('device_model_id', null))
                                        ->required(),

                                    Select::make('device_model_id')
                                        ->label('Modelo')
                                        ->options(function (callable $get) {
                                            $brandId = $get('brand_filter');
                                            return $brandId
                                                ? DeviceModel::where('brand_id', $brandId)
                                                    ->orderBy('name')
                                                    ->pluck('name', 'id')
                                                : [];
                                        })
                                        ->searchable()
                                        ->required()
                                        ->placeholder('Seleccione una marca primero'),
                                ])
                            ,

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('model.brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('model.name')
                    ->label(constants::MODELO)
                    ->sortable()
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('has_no_serial_or_imei')
                    ->color(fn($state): string => $state ? 'success' : 'danger')
                    ->icon(fn($state): ?string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->label('No SN o IMEI')
                    ->toggleable()
                    ->searchable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('client.document')
                    ->label(constants::CLIENT)
                    ->sortable()
                    ->alignCenter()
                    ->state(function ($record) {
                        if (!$record->client) {
                            return 'Sin cliente';
                        }
                        $client = $record->client;
                        $surname2 = $client->surname2 ?? '';
                        return "{$client->document} - {$client->name} {$client->surname} {$surname2}";
                    })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->label(constants::SERIAL_NUMBER)
                    ->sortable()
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('IMEI')
                    ->label(constants::IMEI)
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('colour')
                    ->label(constants::COLOUR)
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('unlock_code')
                    ->label(constants::UNLOCK_CODE)
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(true, true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
               
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WorkOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}