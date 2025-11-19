<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_admin_user_can_access_admin_panel(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
        ]);
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $response = $this->get('/admin');

        $response->assertOk();
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
        ]);
        $user->assignRole('usp_user');

        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login/local');
    }

    public function test_user_with_admin_role_can_access_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $panel = filament()->getDefaultPanel();

        $this->assertTrue($admin->canAccessPanel($panel));
    }

    public function test_user_without_admin_role_cannot_access_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('usp_user');

        $panel = filament()->getDefaultPanel();

        $this->assertFalse($user->canAccessPanel($panel));
    }

    /**
     * Testa que Operador não pode gerenciar produtos (sem permissão gerenciar_produtos).
     */
    public function test_operador_nao_pode_gerenciar_produtos(): void
    {
        $operador = User::factory()->create();
        $operador->assignRole('Operador');

        $this->assertFalse($operador->can('viewAny', \App\Models\Produto::class));
        $this->assertFalse($operador->can('create', \App\Models\Produto::class));
        $this->assertFalse($operador->hasPermissionTo('gerenciar_produtos'));
    }

    /**
     * Testa que Admin pode gerenciar produtos (tem permissão gerenciar_produtos).
     */
    public function test_admin_pode_gerenciar_produtos(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->assertTrue($admin->can('viewAny', \App\Models\Produto::class));
        $this->assertTrue($admin->can('create', \App\Models\Produto::class));
        $this->assertTrue($admin->hasPermissionTo('gerenciar_produtos'));
    }

    /**
     * Testa que Operador pode ver extratos (tem permissão ver_extratos).
     */
    public function test_operador_pode_ver_extratos(): void
    {
        $operador = User::factory()->create();
        $operador->assignRole('Operador');

        $this->assertTrue($operador->hasPermissionTo('ver_extratos'));
        $this->assertFalse($operador->hasPermissionTo('gerenciar_produtos'));
        $this->assertFalse($operador->hasPermissionTo('gerenciar_usuarios'));
    }

    /**
     * Testa que usuário sem autenticação não pode acessar recursos Filament.
     */
    public function test_usuario_sem_autenticacao_nao_acessa_recursos_filament(): void
    {
        // Tentar acessar recurso de produtos sem autenticação
        $response = $this->get('/admin/produtos');

        // Deve redirecionar para login
        $response->assertRedirect('/login/local');
    }
}
