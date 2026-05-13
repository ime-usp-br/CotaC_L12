<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy;
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

    public function test_view_any_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_any_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();

        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $model = User::factory()->create();

        $this->assertTrue($this->policy->view($user, $model));
    }

    public function test_create_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $model = User::factory()->create();

        $this->assertTrue($this->policy->update($user, $model));
    }

    public function test_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $model = User::factory()->create();

        $this->assertTrue($this->policy->delete($user, $model));
    }

    public function test_delete_negado_quando_tentar_deletar_si_mesmo(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertFalse($this->policy->delete($user, $user));
    }

    public function test_restore_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $model = User::factory()->create();

        $this->assertTrue($this->policy->restore($user, $model));
    }

    public function test_force_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $model = User::factory()->create();

        $this->assertTrue($this->policy->forceDelete($user, $model));
    }

    public function test_delete_any_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertTrue($this->policy->deleteAny($user));
    }
}
