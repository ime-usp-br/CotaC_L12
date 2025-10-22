<?php

namespace App\Policies;

use App\Models\CotaEspecial;
use App\Models\User;

class CotaEspecialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CotaEspecial $cotaEspecial): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CotaEspecial $cotaEspecial): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CotaEspecial $cotaEspecial): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CotaEspecial $cotaEspecial): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CotaEspecial $cotaEspecial): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }

    /**
     * Determine whether the user can delete multiple models at once.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('gerenciar_cotas');
    }
}
