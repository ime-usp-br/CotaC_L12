<?php

namespace Tests\Feature;

use App\Filament\Widgets\ConsumptionByCategoryWidget;
use App\Filament\Widgets\OrdersChartWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopProductsWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        \Spatie\Permission\Models\Permission::create(['name' => 'ver_extratos']);
        \Spatie\Permission\Models\Permission::create(['name' => 'ver_auditoria']);
        \Spatie\Permission\Models\Permission::create(['name' => 'gerenciar_cotas']);
        \Spatie\Permission\Models\Permission::create(['name' => 'gerenciar_usuarios']);
        \Spatie\Permission\Models\Permission::create(['name' => 'gerenciar_produtos']);
        $role->givePermissionTo(['ver_extratos', 'ver_auditoria', 'gerenciar_cotas', 'gerenciar_usuarios', 'gerenciar_produtos']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSee('Dashboard');
    }

    public function test_stats_overview_widget_can_be_rendered()
    {
        Livewire::test(StatsOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee('Total Consumidores')
            ->assertSee('Pedidos Hoje')
            ->assertSee('Cotas Consumidas Hoje');
    }

    public function test_orders_chart_widget_can_be_rendered()
    {
        Livewire::test(OrdersChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Pedidos por Dia');
    }

    public function test_top_products_widget_can_be_rendered()
    {
        Livewire::test(TopProductsWidget::class)
            ->assertSuccessful()
            ->assertSee('Produtos Mais Vendidos');
    }

    public function test_consumption_by_category_widget_can_be_rendered()
    {
        Livewire::test(ConsumptionByCategoryWidget::class)
            ->assertSuccessful()
            ->assertSee('Consumo por Categoria');
    }

    public function test_recent_activity_widget_can_be_rendered()
    {
        Livewire::test(RecentActivityWidget::class)
            ->assertSuccessful()
            ->assertSee('Atividade Recente');
    }
}
