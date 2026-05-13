<?php

namespace Tests\Unit\Policies;

use App\Models\Consumidor;
use App\Models\User;
use App\Policies\ConsumidorPolicy;
use Tests\TestCase;

class ConsumidorPolicyTest extends TestCase
{
    private ConsumidorPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ConsumidorPolicy;
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

    public function test_view_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $consumidor = new Consumidor;

        $this->assertTrue($this->policy->view($user, $consumidor));
    }

    public function test_view_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();
        $consumidor = new Consumidor;

        $this->assertFalse($this->policy->view($user, $consumidor));
    }

    public function test_create_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');

        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $consumidor = new Consumidor;

        $this->assertFalse($this->policy->update($user, $consumidor));
    }

    public function test_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $consumidor = new Consumidor;

        $this->assertFalse($this->policy->delete($user, $consumidor));
    }

    public function test_restore_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $consumidor = new Consumidor;

        $this->assertFalse($this->policy->restore($user, $consumidor));
    }

    public function test_force_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');
        $consumidor = new Consumidor;

        $this->assertFalse($this->policy->forceDelete($user, $consumidor));
    }

    public function test_delete_any_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_extratos');

        $this->assertFalse($this->policy->deleteAny($user));
    }
}
