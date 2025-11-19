<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * Get the audits for the role.
     *
     * @return MorphMany<Audit, $this>
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
