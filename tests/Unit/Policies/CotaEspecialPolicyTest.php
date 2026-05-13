<?php

namespace Tests\Unit\Policies;

use App\Models\CotaEspecial;
use App\Models\User;
use App\Policies\CotaEspecialPolicy;
use Tests\TestCase;

class CotaEspecialPolicyTest extends TestCase
{
    private CotaEspecialPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CotaEspecialPolicy;
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

    public function test_view_any_permitido_com_permissao_gerenciar_cotas(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');

        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_any_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();

        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');
        $cota = new CotaEspecial;

        $this->assertTrue($this->policy->view($user, $cota));
    }

    public function test_create_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');
        $cota = new CotaEspecial;

        $this->assertTrue($this->policy->update($user, $cota));
    }

    public function test_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');
        $cota = new CotaEspecial;

        $this->assertTrue($this->policy->delete($user, $cota));
    }

    public function test_restore_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');
        $cota = new CotaEspecial;

        $this->assertTrue($this->policy->restore($user, $cota));
    }

    public function test_force_delete_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');
        $cota = new CotaEspecial;

        $this->assertTrue($this->policy->forceDelete($user, $cota));
    }

    public function test_delete_any_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('gerenciar_cotas');

        $this->assertTrue($this->policy->deleteAny($user));
    }
}
