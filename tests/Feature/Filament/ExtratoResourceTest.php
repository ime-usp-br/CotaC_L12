<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ExtratoResource;
use App\Filament\Resources\ExtratoResource\Pages;
use App\Models\Pedido;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExtratoResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_access_extratos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get(ExtratoResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_operador_can_access_extratos()
    {
        $operador = User::factory()->create();
        $operador->assignRole('Operador');

        $this->actingAs($operador)
            ->get(ExtratoResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_user_without_permission_cannot_access_extratos()
    {
        $user = User::factory()->create();
        // No role assigned

        $this->actingAs($user)
            ->get(ExtratoResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_extratos_table_lists_pedidos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $pedido = Pedido::factory()->create();

        $this->actingAs($admin)
            ->get(ExtratoResource::getUrl('index'))
            ->assertSuccessful()
            ->assertSee($pedido->id);
    }

    public function test_export_pdf_action_exists()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        \Livewire\Livewire::actingAs($admin)
            ->test(Pages\ListExtratos::class)
            ->assertActionExists('export_pdf');
    }
}
