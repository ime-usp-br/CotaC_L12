<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotaRegularResource\Pages;
use App\Models\CotaRegular;
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

class CotaRegularResource extends Resource
{
    protected static ?string $model = CotaRegular::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Gerenciamento';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Cotas Regulares';

    protected static ?string $modelLabel = 'Cota Regular';

    protected static ?string $pluralModelLabel = 'Cotas Regulares';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vinculo')
                    ->label(__('Vínculo'))
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText(__('Tipo de vínculo USP (ex: DOCENTE, SERVIDOR, ALUNOPOS)')),
                TextInput::make('valor')
                    ->label(__('Valor'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText(__('Valor da cota mensal em unidades'))
                    ->suffix('unidades'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vinculo')
                    ->label(__('Vínculo'))
                    ->searchable()
                    ->sortable(),
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
            ->defaultSort('vinculo', 'asc');
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
            'index' => Pages\ListCotaRegulares::route('/'),
            'create' => Pages\CreateCotaRegular::route('/create'),
            'edit' => Pages\EditCotaRegular::route('/{record}/edit'),
        ];
    }
}
