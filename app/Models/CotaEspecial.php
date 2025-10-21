<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CotaEspecial - Armazena cotas mensais que se sobrepõem às cotas regulares.
 *
 * Cada consumidor pode ter apenas uma cota especial, que substitui
 * a cota regular baseada no vínculo.
 */
class CotaEspecial extends Model
{
    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'cota_especiais';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'consumidor_codpes',
        'valor',
    ];

    /**
     * Retorna o consumidor ao qual esta cota especial pertence.
     *
     * @return BelongsTo<Consumidor, $this>
     */
    public function consumidor(): BelongsTo
    {
        return $this->belongsTo(Consumidor::class, 'consumidor_codpes', 'codpes');
    }
}
