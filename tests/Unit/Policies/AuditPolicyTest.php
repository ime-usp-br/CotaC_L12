<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\AuditPolicy;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class AuditPolicyTest extends TestCase
{
    private AuditPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AuditPolicy;
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
        $user = $this->createUserWithPermission('ver_auditoria');

        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_any_negado_sem_permissao(): void
    {
        $user = $this->createUserWithoutPermission();

        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_permitido_com_permissao(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');
        $audit = new Audit;

        $this->assertTrue($this->policy->view($user, $audit));
    }

    public function test_create_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');

        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');
        $audit = new Audit;

        $this->assertFalse($this->policy->update($user, $audit));
    }

    public function test_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');
        $audit = new Audit;

        $this->assertFalse($this->policy->delete($user, $audit));
    }

    public function test_restore_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');
        $audit = new Audit;

        $this->assertFalse($this->policy->restore($user, $audit));
    }

    public function test_force_delete_negado_sempre(): void
    {
        $user = $this->createUserWithPermission('ver_auditoria');
        $audit = new Audit;

        $this->assertFalse($this->policy->forceDelete($user, $audit));
    }
}
