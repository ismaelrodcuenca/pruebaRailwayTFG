<?php
 namespace App\Filament\Resources;

use App\Filament\Resources\StatusResource\Pages;
use App\Filament\Resources\StatusResource\RelationManagers;
use App\Helpers\PermissionHelper;
use App\Models\Status;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use constants;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    public static ?string $navigationGroup = 'Miscelanea';
    protected static ?string $label = 'Estado';

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
            'index' => Pages\ListStatuses::route('/'),
            'create' => Pages\CreateStatus::route('/create'),
            'edit' => Pages\EditStatus::route('/{record}/edit'),
        ];
    }
}
