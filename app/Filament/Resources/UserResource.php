<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\StoreRelationManager;
use App\Helpers\PermissionHelper;
use App\Models\User;
use constants;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

        public static function getNavigationIcon(): ?string
        {
            if(PermissionHelper::isNotAdmin()){
                return 'heroicon-s-user-circle';
            } 
            return 'heroicon-s-user-group';
        }

    public static function getLabel(): string
    {
        if(PermissionHelper::isAdmin()){
            return "Usuarios";
        }
        return "Mi Usuario ";
    }


    public static function shouldRegisterNavigation(): bool
    {
        return PermissionHelper::hasRole();
    }

    public static function getEloquentQuery(): Builder
    {
        return PermissionHelper::isNotAdmin() ? parent::getEloquentQuery()
            ->where('id', auth()->user()->id) : parent::getEloquentQuery();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->label(constants::NAME)
                ->inlineLabel(true)
                ->required()
                ->columnSpan(1),
                TextInput::make('email')
                ->label(constants::EMAIL)
                ->required()
                ->columnSpan(1)
                ->inlineLabel(true),

                TextInput::make('password')->label(constants::PASSWORD)
                ->dehydrated(fn($get)=> $get('password') !== null && $get('password') !== '' ? true : false
                )
                ->columnSpan(1)
                ->inlineLabel(true)
                ->helperText(function(){
                    if(str_contains(request()->route()->getName(), 'edit')) {
                        return 'Dejar en blanco si no desea cambiar la contraseña.';
                    }
                    return "Predeterminado: user12345 ";
                })
                ->password()
                ->inlineLabel(true)
                ->hiddenOn('view'),
                Toggle::make('active')
                    ->label(null)
                    ->onIcon('heroicon-o-check')
                    ->default(true)
                    ->columnSpan(1)
                    ->required()
                    ->disabled(PermissionHelper::isNotAdmin())
                    ->inlineLabel(true),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(constants::NAME)
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($record) => $record->active ? '' : Color::hex('#c3c3c3' )),
                TextColumn::make('email')
                    ->label(constants::EMAIL)
                    ->sortable()
                    ->color(fn ($record) => $record->active ? '' : Color::hex('#c3c3c3' ))
                    ->alignCenter()
                    ->searchable(),
                IconColumn::make('active')
                    ->label('¿Activo?')
                    ->alignCenter()
                    ->visible(fn () => PermissionHelper::isAdmin())
                    ->hidden(PermissionHelper::isNotAdmin())
                    ->icon(fn ($record) => $record->active ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record->active ? 'success' : 'danger')
                    ->boolean()
                    ->inline(false)
                    ->toggleable(true, true),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Usuarios activos')
                    ->default(true)
                    ->visible(fn () => PermissionHelper::isAdmin())
                    ->query(fn (Builder $query): Builder => $query->where('active', true)),
            ])
            ->actions([
                Action::make("changeActiveStatus")
                    ->label(fn($record) => $record->active ? 'Desactivar' : 'Activar')
                    ->icon('heroicon-o-user-minus')
                    ->color(fn($record) => $record->active ?'danger':'success')
                    ->action(fn ($record) => User::where('id', $record->id)
                        ->update(['active' => !$record->active]))
                    ->requiresConfirmation()
                    ->visible(PermissionHelper::isAdmin()),
                    \Filament\Tables\Actions\EditAction::make()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StoreRelationManager::class,
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
