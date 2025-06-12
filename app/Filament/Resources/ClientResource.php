<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Filament\Resources\ClientResource\RelationManagers\InvoicesRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\Client;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ClientResource\RelationManagers\DeviceRelationManager;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $label = 'Cliente';   
        public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    } 
    public static function getGloballySearchableAttributes(): array
    {
        return PermissionHelper::hasRole() ? [
            'document',
            'name',
            'surname',
            'phone_number'
        ] : [];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Documento' => $record->document,
            'Nombre' => ($record->name . " " . $record->surname),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type_id')
                    ->label('Tipo de documento')
                    ->relationship('documentType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('document')
                    ->label('Documento')
                    ->required()
                    ->unique()
                    ->rule(function (callable $get) {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get) {
                            $docTypeId = $get('document_type_id');
                            $docType = \App\Models\DocumentType::find($docTypeId)?->name;

                            $type = strtoupper($docType ?? '');

                            if (!in_array($type, ['DNI', 'NIE'])) {
                                return; // No aplicamos validación si no es DNI o NIE
                            }

                            $value = strtoupper($value);
                            if (!preg_match('/^[XYZ]?\d{5,8}[A-Z]$/', $value)) {
                                $fail('El formato del documento no es válido.');
                                return;
                            }

                            // Reemplazo para NIE si aplica
                            $number = str_replace(['X', 'Y', 'Z'], [0, 1, 2], substr($value, 0, -1));
                            $letter = substr($value, -1);
                            $expected = substr('TRWAGMYFPDXBNJZSQVHLCKET', $number % 23, 1);

                            if ($letter !== $expected) {
                                $fail('La letra del documento no corresponde al número.');
                            }
                        };
                    }),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                Forms\Components\TextInput::make('surname')
                    ->label('Apellido')
                    ->required(),
                Forms\Components\TextInput::make('surname2')
                    ->label('Segundo apellido')
                    ->nullable(),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Numero de telefono')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('phone_number_2')
                    ->label('Número de teléfono secundario')
                    ->nullable()
                    ->tel(),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Código postal')
                    ->nullable(),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Tipo de documento')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('document')
                    ->label('Documento')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->label('Apellido')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname2')
                    ->label('2º Apellido')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telefono')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number_2')
                    ->label('Telefono 2')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->label('Código postal')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DeviceRelationManager::class,
            InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
