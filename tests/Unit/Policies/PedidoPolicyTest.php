<?php

namespace Tests\Unit\Policies;

use App\Models\Pedido;
use App\Models\User;
use App\Policies\PedidoPolicy;
use Tests\TestCase;

class PedidoPolicyTest extends TestCase
{
    private PedidoPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PedidoPolicy;
    }

    private function createUserWithoutPermission(): User
    {
        $user = User::factory()->create();
        $user = \Mockery::mock($user)->makePartial();
        $user->shouldReceive('hasPermissionTo')->andReturn(false);

        return $user;
    }

    private function createUserWithPermission(string $permissionName): User
    {
        $user = User::factory()->create();
        $user = \Mockery::mock($user)->makePartial();
        $user->shouldReceive('hasPermissionTo')
            ->with($permissionName)
            ->andReturn(true);

        return $user;
    }

    public function test_view_any_permitido_com_permissao_ver_extratos(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');

        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_any_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();

        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_permitido_com_permissao_ver_extratos(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $pedido = new Pedido;

        $this->assertTrue($this->policy->view($user, $pedido));
    }

    public function test_view_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();
        $pedido = new Pedido;

        $this->assertFalse($this->policy->view($user, $pedido));
    }

    public function test_create_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');

        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $pedido = new Pedido;

        $this->assertFalse($this->policy->update($user, $pedido));
    }

    public function test_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $pedido = new Pedido;

        $this->assertFalse($this->policy->delete($user, $pedido));
    }

    public function test_restore_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $pedido = new Pedido;

        $this->assertFalse($this->policy->restore($user, $pedido));
    }

    public function test_force_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $pedido = new Pedido;

        $this->assertFalse($this->policy->forceDelete($user, $pedido));
    }

    public function test_delete_any_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');

        $this->assertFalse($this->policy->deleteAny($user));
    }
}
