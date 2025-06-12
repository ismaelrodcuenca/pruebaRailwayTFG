<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashDeskResource\Pages;
use App\Filament\Resources\CashDeskResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\CashDesk;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use SebastianBergmann\CodeCoverage\Util\Percentage;

class CashDeskResource extends Resource
{
    protected static ?string $model = CashDesk::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $label = 'Cajas';
    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::isSalesperson();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               
                Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('cash_float')
                            ->label('Fondo')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('cash_amount')
                            ->label('Efectivo Contabilizado')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('card_amount')
                            ->label('Totales datáfono')
                            ->numeric()
                            ->required(),
                        
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Cierre')
                    ->alignCenter()
                    ->dateTime('Y/m/d - H:i'),
                Tables\Columns\TextColumn::make('cash_float')
                    ->label('Fondo')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('cash_amount')
                    ->label('Efectivo')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('card_amount')
                    ->label('Tarjeta')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('measured_cash_amount')
                    ->label('Ef. contado')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('measured_card_amount')
                    ->label('Tar. contada')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('difference_in_cash_amount')
                    ->alignCenter()
                    ->label('Diff. efectivo'),
                Tables\Columns\TextColumn::make('difference_in_card_amount')
                    ->alignCenter()
                    ->label('Diff. tarjeta'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creado por')
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('Fecha de Cierre')
                    ->default(now()->subDay()->format('Y-m-d')),
                Filter::make('store_id')
                    ->default(session()->get('store_id'))
                    ->hidden(),
            ])
            ->query(function () {
                if (PermissionHelper::isNotAdmin() && CashDesk::count() > 0) {
                    return CashDesk::query()
                        ->where('store_id', session('store_id'));
                }
                return CashDesk::query();
            })
            ->defaultSort('created_at', 'desc');
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

            'index' => Pages\ListCashDesks::route('/'),
            
            'create' => Pages\CreateCashDesk::route('/create'),
        ];
    }
}
