<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CotaRegular - Define cotas mensais padrão baseadas no vínculo USP.
 *
 * Esta entidade não possui relacionamentos diretos com outras tabelas.
 * A lógica de negócio buscará registros desta tabela pelo campo 'vinculo'.
 */
class CotaRegular extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vinculo',
        'valor',
    ];
}
