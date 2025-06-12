<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Filament\Resources\PaymentMethodResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\PaymentMethod;
use constants;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $label = 'Métodos de Pago';

    public static ?string $navigationGroup = 'Miscelanea';

   public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isDeveloper();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(constants::NAME)->required(),
            ]);
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(constants::NAME_TYPO)
                ->sortable()
                ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
