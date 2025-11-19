<?php

namespace App\Filament\Resources\CotaEspecialResource\Pages;

use App\Filament\Resources\CotaEspecialResource;
use App\Models\Consumidor;
use Filament\Resources\Pages\CreateRecord;
use Uspdev\Replicado\Pessoa;

class CreateCotaEspecial extends CreateRecord
{
    protected static string $resource = CotaEspecialResource::class;

    /**
     * Mutate form data before creating the record.
     * Creates the Consumidor if it doesn't exist.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $codpes = $data['consumidor_codpes'];

        // Check if consumidor already exists
        $consumidor = Consumidor::find($codpes);

        if (! $consumidor) {
            // Fetch person data from Replicado
            try {
                $pessoa = Pessoa::dump($codpes);
                
                if ($pessoa) {
                    // Create the consumidor
                    Consumidor::create([
                        'codpes' => $codpes,
                        'nome' => $pessoa['nompesttd'] ?? 'Nome não disponível',
                    ]);
                } else {
                    throw new \Exception("Pessoa com codpes {$codpes} não encontrada no Replicado.");
                }
            } catch (\Exception $e) {
                throw new \Exception("Erro ao buscar dados do Replicado: {$e->getMessage()}");
            }
        }

        return $data;
    }
}
