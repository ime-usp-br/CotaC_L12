<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use OwenIt\Auditing\Contracts\Auditable;

/**
 * Consumidor - Representa uma pessoa que realizou pedidos no sistema.
 *
 * Esta entidade armazena apenas informações mínimas (codpes e nome),
 * pois os dados completos são consultados em tempo real via Replicado.
 *
 * @use HasFactory<\Database\Factories\ConsumidorFactory>
 */
class Consumidor extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\ConsumidorFactory> */
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'consumidores';

    /**
     * A chave primária customizada para esta tabela.
     *
     * @var string
     */
    protected $primaryKey = 'codpes';

    /**
     * Indica se a chave primária é auto-incrementável.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * O tipo de dado da chave primária.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'codpes',
        'nome',
    ];

    /**
     * Retorna a cota especial do consumidor, se houver.
     *
     * @return HasOne<CotaEspecial, $this>
     */
    public function cotaEspecial(): HasOne
    {
        return $this->hasOne(CotaEspecial::class, 'consumidor_codpes', 'codpes');
    }

    /**
     * Retorna todos os pedidos realizados por este consumidor.
     *
     * @return HasMany<Pedido, $this>
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'consumidor_codpes', 'codpes');
    }
}
