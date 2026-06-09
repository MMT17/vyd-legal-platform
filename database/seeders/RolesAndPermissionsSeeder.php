<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles and permissions for Plataforma Legal VyD.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'convenios.listar',
            'convenios.crear',
            'convenios.ver',
            'convenios.editar',
            'convenios.eliminar',
            'convenios.cambiar_estado',
            'convenios.importar',
            'convenios.exportar',

            'querellas.listar',
            'querellas.crear',
            'querellas.ver',
            'querellas.editar',
            'querellas.eliminar',
            'querellas.cambiar_estado',
            'querellas.importar',
            'querellas.exportar',

            'procesos.ver',
            'procesos.crear',
            'procesos.editar',
            'procesos.eliminar',

            'documentos.ver',
            'documentos.crear',
            'documentos.editar',
            'documentos.eliminar',
            'documentos.descargar',

            'contactos.ver',
            'contactos.crear',
            'contactos.editar',
            'contactos.eliminar',

            'usuarios.listar',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'reportes.ver',
            'auditoria.ver',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'administrador' => $permissions,
            'abogado_admin' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
                'auditoria.ver',
            ],
            'abogado_editor' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
            ],
            'abogado_operativo' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
            ],
            'cliente_chilquinta_admin' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'convenios.importar',
                'convenios.exportar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'querellas.importar',
                'querellas.exportar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
            ],
            'cliente_chilquinta_lectura' => [
                'convenios.listar',
                'convenios.ver',
                'querellas.listar',
                'querellas.ver',
                'procesos.ver',
                'documentos.ver',
                'documentos.descargar',
            ],
        ];

        foreach ($roles as $role => $rolePermissions) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ])->syncPermissions($rolePermissions);
        }

        $this->syncLegacyRoles();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function syncLegacyRoles(): void
    {
        $legacyRoles = [
            'abogado' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
            ],
            'cliente_lectura' => [
                'convenios.listar',
                'convenios.ver',
                'querellas.listar',
                'querellas.ver',
                'procesos.ver',
                'documentos.ver',
                'documentos.descargar',
            ],
            'cliente_edicion' => [
                'convenios.listar',
                'convenios.ver',
                'querellas.listar',
                'querellas.ver',
                'procesos.ver',
                'documentos.ver',
                'documentos.crear',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
            ],
            'cliente_admin' => [
                'reportes.ver',
                'convenios.listar',
                'convenios.ver',
                'convenios.editar',
                'convenios.importar',
                'convenios.exportar',
                'querellas.listar',
                'querellas.ver',
                'querellas.editar',
                'querellas.importar',
                'querellas.exportar',
                'procesos.ver',
                'procesos.crear',
                'procesos.editar',
                'documentos.ver',
                'documentos.crear',
                'documentos.editar',
                'documentos.descargar',
                'contactos.ver',
                'contactos.crear',
                'contactos.editar',
            ],
        ];

        foreach ($legacyRoles as $role => $permissions) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ])->syncPermissions($permissions);
        }
    }
}
