<?php

namespace Tests\Unit\Observers;

use App\Models\Permission;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class PermissionObserverTest extends TestCase
{
    public function test_criar_permissao_gera_audit_log(): void
    {
        Permission::create(['name' => 'test_permission_observer', 'guard_name' => 'web']);

        $this->assertDatabaseHas('audits', [
            'event' => 'created',
            'auditable_type' => Permission::class,
        ]);
    }

    public function test_atualizar_permissao_gera_audit_log(): void
    {
        $permission = Permission::create(['name' => 'test_perm_update', 'guard_name' => 'web']);

        $permission->name = 'test_perm_updated';
        $permission->save();

        $this->assertDatabaseHas('audits', [
            'event' => 'updated',
            'auditable_type' => Permission::class,
        ]);
    }

    public function test_deletar_permissao_gera_audit_log(): void
    {
        $permission = Permission::create(['name' => 'test_perm_delete', 'guard_name' => 'web']);
        $permission->delete();

        $this->assertDatabaseHas('audits', [
            'event' => 'deleted',
            'auditable_type' => Permission::class,
        ]);
    }

    public function test_atualizar_sem_mudancas_relevantes_nao_gera_audit_log(): void
    {
        $permission = Permission::create(['name' => 'test_perm_no_audit', 'guard_name' => 'web']);

        // Força updated_at a mudar sem alterar dados relevantes
        $countBefore = Audit::where('auditable_type', Permission::class)
            ->where('event', 'updated')
            ->count();

        $permission->touch();

        $countAfter = Audit::where('auditable_type', Permission::class)
            ->where('event', 'updated')
            ->count();

        $this->assertEquals($countBefore, $countAfter);
    }
}
