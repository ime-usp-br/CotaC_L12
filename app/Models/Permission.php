<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * Get the audits for the permission.
     *
     * @return MorphMany<Audit, \Illuminate\Database\Eloquent\Model>
     */
    public function audits(): MorphMany
    {
        // @phpstan-ignore-next-line return.type
        return $this->morphMany(Audit::class, 'auditable');
    }
}
