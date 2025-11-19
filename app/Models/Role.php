<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * Get the audits for the role.
     *
     * @return MorphMany<Audit, \Illuminate\Database\Eloquent\Model>
     */
    public function audits(): MorphMany
    {
        // @phpstan-ignore-next-line return.type
        return $this->morphMany(Audit::class, 'auditable');
    }
}
