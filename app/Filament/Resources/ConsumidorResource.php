<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumidorResource\Pages;
use App\Models\Consumidor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConsumidorResource extends Resource
{
    protected static ?string $model = Consumidor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nome';

    protected static ?string $modelLabel = 'Consumidor';

    protected static ?string $pluralModelLabel = 'Consumidores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codpes')
                    ->label(__('N° USP'))
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit'),
                TextInput::make('nome')
                    ->label(__('Nome'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codpes')
                    ->label(__('N° USP'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nome')
                    ->label(__('Nome'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Criado em'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Atualizado em'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListConsumidors::route('/'),
            'create' => Pages\CreateConsumidor::route('/create'),
            'edit' => Pages\EditConsumidor::route('/{record}/edit'),
        ];
    }
}
