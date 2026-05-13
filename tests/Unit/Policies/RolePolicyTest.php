<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    private RolePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RolePolicy;
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
        $role = new Role;

        $this->assertTrue($this->policy->view($user, $role));
    }

    public function test_create_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $role = new Role;

        $this->assertTrue($this->policy->update($user, $role));
    }

    public function test_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $role = new Role;
        $role->name = 'Editor';

        $this->assertTrue($this->policy->delete($user, $role));
    }

    public function test_delete_negado_para_role_admin(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $role = new Role;
        $role->name = 'Admin';

        $this->assertFalse($this->policy->delete($user, $role));
    }

    public function test_restore_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $role = new Role;

        $this->assertTrue($this->policy->restore($user, $role));
    }

    public function test_force_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');
        $role = new Role;

        $this->assertTrue($this->policy->forceDelete($user, $role));
    }

    public function test_delete_any_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_usuarios');

        $this->assertTrue($this->policy->deleteAny($user));
    }
}
