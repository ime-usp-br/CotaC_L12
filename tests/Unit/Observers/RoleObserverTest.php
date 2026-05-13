<?php

namespace Tests\Unit\Observers;

use App\Models\Role;
use Tests\TestCase;

class RoleObserverTest extends TestCase
{
    public function test_criar_role_gera_audit_log(): void
    {
        Role::create(['name' => 'test_role_observer', 'guard_name' => 'web']);

        $this->assertDatabaseHas('audits', [
            'event' => 'created',
            'auditable_type' => Role::class,
        ]);
    }

    public function test_atualizar_role_gera_audit_log(): void
    {
        $role = Role::create(['name' => 'test_role_update', 'guard_name' => 'web']);

        $role->name = 'test_role_updated';
        $role->save();

        $this->assertDatabaseHas('audits', [
            'event' => 'updated',
            'auditable_type' => Role::class,
        ]);
    }

    public function test_deletar_role_gera_audit_log(): void
    {
        $role = Role::create(['name' => 'test_role_delete', 'guard_name' => 'web']);
        $role->delete();

        $this->assertDatabaseHas('audits', [
            'event' => 'deleted',
            'auditable_type' => Role::class,
        ]);
    }
}
