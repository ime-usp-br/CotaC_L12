<?php

namespace App\Policies;

use App\Models\Consumidor;
use App\Models\User;

class ConsumidorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Consumidores podem ser visualizados por quem tem permissão ver_extratos
        return $user->hasPermissionTo('ver_extratos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Consumidor $consumidor): bool
    {
        // Consumidores podem ser visualizados por quem tem permissão ver_extratos
        return $user->hasPermissionTo('ver_extratos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Consumidores não podem ser criados manualmente no admin
        // São criados automaticamente ao fazer pedidos
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Consumidor $consumidor): bool
    {
        // Consumidores não podem ser editados no admin
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Consumidor $consumidor): bool
    {
        // Consumidores não podem ser excluídos
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Consumidor $consumidor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Consumidor $consumidor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete multiple models at once.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }
}
