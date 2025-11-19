<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * Get the audits for the permission.
     *
     * @return MorphMany<Audit, $this>
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }
}
