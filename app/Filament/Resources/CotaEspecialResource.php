<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotaEspecialResource\Pages;
use App\Models\CotaEspecial;
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
use UnitEnum;

class CotaEspecialResource extends Resource
{
    protected static ?string $model = CotaEspecial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static UnitEnum|string|null $navigationGroup = 'Gerenciamento';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Cotas Especiais';

    protected static ?string $modelLabel = 'Cota Especial';

    protected static ?string $pluralModelLabel = 'Cotas Especiais';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('consumidor_codpes')
                    ->label(__('Nº USP (codpes)'))
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->minLength(6)
                    ->maxLength(8)
                    ->helperText(__('Número USP do consumidor')),
                TextInput::make('valor')
                    ->label(__('Valor'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText(__('Valor da cota mensal especial em unidades'))
                    ->suffix('unidades'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('consumidor_codpes')
                    ->label(__('Nº USP'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('consumidor.nome')
                    ->label(__('Nome'))
                    ->searchable()
                    ->sortable()
                    ->default(__('Consumidor não cadastrado')),
                TextColumn::make('valor')
                    ->label(__('Valor'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' unidades'),
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
            ])
            ->defaultSort('consumidor_codpes', 'asc');
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
            'index' => Pages\ListCotaEspeciais::route('/'),
            'create' => Pages\CreateCotaEspecial::route('/create'),
            'edit' => Pages\EditCotaEspecial::route('/{record}/edit'),
        ];
    }
}
