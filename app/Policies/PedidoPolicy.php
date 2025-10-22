<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

class PedidoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Pedidos podem ser visualizados por quem tem permissão ver_extratos
        return $user->hasPermissionTo('ver_extratos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pedido $pedido): bool
    {
        // Pedidos podem ser visualizados por quem tem permissão ver_extratos
        return $user->hasPermissionTo('ver_extratos');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Pedidos não podem ser criados manualmente no admin
        // São criados via interface do balcão
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pedido $pedido): bool
    {
        // Pedidos não podem ser editados no admin
        // O status é alterado apenas via interface de entrega
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pedido $pedido): bool
    {
        // Pedidos não podem ser excluídos
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pedido $pedido): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pedido $pedido): bool
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
