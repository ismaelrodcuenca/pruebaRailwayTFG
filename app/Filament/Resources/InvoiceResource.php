<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\InvoiceItemsRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Client;
use App\Models\Invoice;
use Carbon\Carbon;
use DB;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $label = 'Facturas';

    public static ?string $navigationGroup = 'Recursos';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make("Tienda")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(function ($record) {
                                        $store = $record->store ?? null;
                                        return $store['name'] ?? 'Sin Tienda';
                                    }),
                            ])
                            ->icon('heroicon-o-building-storefront')
                            ->columnSpan(1),
                        Section::make("Empresa")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(function ($record) {
                                        $company = $record->company ?? null;
                                        return $company['name'] ?? 'Sin Empresa';
                                    }),
                            ])
                            ->icon('heroicon-o-briefcase')
                            ->columnSpan(1),
                        Section::make("Creado por")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(function ($record) {
                                        return $record->user->name ?? "Sin Usuario";

                                    }),
                            ])
                            ->icon('heroicon-o-user-circle')
                            ->columnSpan(1),
                    ]),
                Section::make("Cliente")
                ->columns(3)
                ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Section::make("Documento")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(fn($record) => $record->client->document ?? '00000000A'),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Section::make("Nombre")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(function ($record) {
                                        $client = $record->client ?? null;
                                        $name = $client->name ?? 'John';
                                        $surname = $client->surname ?? 'Doe';
                                        $surname2 = $client->surname2 ?? null;
                                        if ($surname2) {
                                            return $name . " " . $surname . " " . $surname2;
                                        }
                                        return $name . " " . $surname;
                                    }),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Section::make("Teléfono")
                            ->schema([
                                Forms\Components\Placeholder::make("")
                                    ->content(fn($record) => $record->client->phone_number ?? 'Sin Número '),
                            ])
                            ->columnSpan(1),

                    ]),

                // Sección oculta con los IDs necesarios
                Forms\Components\Group::make()->schema([
                    Forms\Components\Hidden::make('user_id')
                        ->default(auth()->user()->id)
                        ->dehydrated(true),
                    Forms\Components\Hidden::make('store_id')
                        ->default(session('store_id'))
                        ->dehydrated(true),
                ]),
                // Sección para el cliente

                Card::make("Datos de la factura")
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Número de factura')
                            ->disabledOn(['edit', 'create']),
                        Forms\Components\TextInput::make('base')
                            ->label('Base')
                            ->numeric()
                            ->disabled()
                            ->default(0)
                            ->suffix('€')
                            ->minValue(0)
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('taxes')
                            ->label('Impuestos')
                            ->numeric()
                            ->disabled()
                            ->suffix('€')
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->disabled()
                            ->suffix('€')
                            ->reactive()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->columnSpan(1),
                    ]),
                Forms\Components\Toggle::make('is_refund')
                    ->hiddenOn(['edit', 'create'])
                    ->default(false)
                    ->label(label: '¿Es reembolso?'),
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->disabledOn(['edit'])
                    ->searchable()
                    ->inlineLabel()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Client::query()
                            ->select([
                                'id',
                                DB::raw("CONCAT(name, ' ', surname, ' - ',document ) AS full_name")
                            ])
                            ->where('document', 'like', "%{$search}%")
                            ->orderBy('document')
                            ->limit(15)
                            ->pluck('full_name', 'id')
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        if (!$value) {
                            return null;
                        }
                        $client = Client::query()
                            ->select([
                                'id',
                                DB::raw("CONCAT(name, ' ', surname, ' - ',document ) AS full_name")
                            ])
                            ->where('id', $value)
                            ->first();
                        return $client ? $client->full_name : null;
                    })
                    ->placeholder('Buscar únicamente por DNI/NIF'),
                Forms\Components\Select::make('store_id')
                    ->label('Tienda')
                    ->disabledOn(['edit'])
                    ->hiddenOn('create')
                    ->inlineLabel()
                    ->default(fn() => session('store_id'))
                    ->relationship('store', 'name')
                    ->searchable(),
                Forms\Components\Select::make('company_id')
                    ->label('Empresa')
                    ->inlineLabel()
                    ->relationship('company', 'name')
                    ->searchable(),
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->disabledOn(['edit'])
                    ->hiddenOn('create')
                    ->inlineLabel()
                    ->default(fn() => auth()->user()->id)
                    ->relationship('user', 'name')
                    ->searchable(),
                Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('payment_method_id')
                            ->label('Método de pago')
                            ->inlineLabel()
                            ->disabled(
                                fn($get) =>
                                !($get('created_at')
                                && Carbon::parse($get('created_at'))->isToday() ? true : false)
                            )
                            ->columnSpan(1)
                            ->relationship('paymentMethod', 'name'),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Fecha de creación')
                            ->columnSpan(1)
                            ->inlineLabel()
                            ->hiddenOn('create')
                            ->disabledOn(['edit']),
                    ]),
                Card::make('Opciones')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_down_payment')
                            ->label('¿Es anticipo?')
                            ->default(false)
                            ->disabledOn(['create', 'edit'])
                            ->columnSpan(1),
                        Toggle::make('is_refund')
                            ->label('¿Es reembolso?')
                            ->default(false)
                            ->disabledOn(['create', "edit"])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_down_payment')
                    ->label('Anticipo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Empresa')
                    ->sortable()
                    ->default("-")
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Importe')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Método')
                    ->sortable(),
            ])
            ->query(function(){
                if(PermissionHelper::isNotAdmin()) {
                    return Invoice::query()
                        ->where('store_id', session('store_id'))
                        ->where('user_id', auth()->user()->id);
                }
                return Invoice::query();
            });
    }

    public static function getRelations(): array
    {
        return [
            InvoiceItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
