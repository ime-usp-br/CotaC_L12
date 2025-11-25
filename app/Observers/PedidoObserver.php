<?php

namespace App\Observers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Cache;

/**
 * Observer para invalidar cache quando pedidos são criados ou atualizados.
 */
class PedidoObserver
{
    /**
     * Handle the Pedido "created" event.
     */
    public function created(Pedido $pedido): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle the Pedido "updated" event.
     */
    public function updated(Pedido $pedido): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Clear all dashboard-related cache.
     */
    protected function clearDashboardCache(): void
    {
        // Clear individual cache keys
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.top_products');
        Cache::forget('dashboard.consumption_by_category');
    }
}
