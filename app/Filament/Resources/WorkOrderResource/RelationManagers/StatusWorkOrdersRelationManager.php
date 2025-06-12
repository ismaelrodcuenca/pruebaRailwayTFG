<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

use app\Helpers\PermissionHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatusWorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'statusWorkOrders';

    protected static ?string $title = 'Estados';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_id')
                    ->label('Estado')
                    ->relationship('status', 'name')
                    ->required(),
                    Forms\Components\Select::make('work_order_id')
                    ->default($this->ownerRecord->id)
                    ->hidden(),
            ]);
    }

        public function table(Table $table): Table
        {
            return $table
                ->recordTitleAttribute('id')
                ->columns([
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Fecha de creación')
                        ->dateTime(),
                    Tables\Columns\TextColumn::make('status.name')
                        ->label('Estado'),
                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Usuario'),
                ])
                ->defaultSort('created_at', 'desc');
        }
    }
