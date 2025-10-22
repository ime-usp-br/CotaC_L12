<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder para popular a tabela de roles com os papéis padrão da aplicação.
 */
class RoleSeeder extends Seeder
{
    /**
     * Executa o seeder para o banco de dados.
     *
     * Cria os papéis (roles) padrão para o sistema CotaC:
     * - 'usp_user' e 'external_user': papéis do starter kit
     * - 'Admin' (ADM): administrador com acesso total
     * - 'Operador' (OPR): operador com acesso restrito a consultas
     *
     * Cria as permissões do sistema e as atribui aos papéis adequados.
     *
     * Limpa o cache de permissões antes de criar os papéis para garantir consistência.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'gerenciar_cotas',
            'gerenciar_produtos',
            'gerenciar_usuarios',
            'ver_extratos',
            'ver_auditoria',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles for local authentication (starter kit)
        Role::firstOrCreate(['name' => 'usp_user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'external_user', 'guard_name' => 'web']);

        // Create roles for CotaC system
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $operadorRole = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);

        // Assign all permissions to Admin
        $adminRole->syncPermissions($permissions);

        // Assign only ver_extratos permission to Operador
        $operadorRole->syncPermissions(['ver_extratos']);
    }
}
