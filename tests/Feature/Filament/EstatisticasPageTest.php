<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Estatisticas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstatisticasPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_estatisticas_page()
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
            ->get(Estatisticas::getUrl())
            ->assertSuccessful()
            ->assertSee('Estatísticas');
    }

    public function test_widgets_are_rendered_on_estatisticas_page()
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
            ->get(Estatisticas::getUrl())
            ->assertSuccessful()
            ->assertSeeLivewire(\App\Filament\Widgets\StatsOverviewWidget::class)
            ->assertSeeLivewire(\App\Filament\Widgets\OrdersChartWidget::class)
            ->assertSeeLivewire(\App\Filament\Widgets\TopProductsWidget::class)
            ->assertSeeLivewire(\App\Filament\Widgets\ConsumptionByCategoryWidget::class)
            ->assertSeeLivewire(\App\Filament\Widgets\RecentActivityWidget::class);
    }
}
