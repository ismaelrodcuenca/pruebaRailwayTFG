<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Helpers\PermissionHelper;
use App\Models\DocumentType;
use constants;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    public static ?string $navigationGroup = 'Miscelanea';
    protected static ?string $label = 'Tipo De Documento';

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
                EditAction::make(),
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
