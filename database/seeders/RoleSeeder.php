<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Crear/asegurar roles (idempotente) con guard 'web'
        $admin = Role::findOrCreate('admin', 'web');
        $user  = Role::findOrCreate('user',  'web'); // si prefieres 'cliente', cámbialo aquí

        // 2) Crear/asegurar permisos con guard 'web'
        $categoriaIndex   = Permission::findOrCreate('categoria.index',  'web');
        $categoriaCreate  = Permission::findOrCreate('categoria.create', 'web');
        $categoriaUpdate  = Permission::findOrCreate('categoria.update', 'web');
        $categoriaDestroy = Permission::findOrCreate('categoria.destroy','web');

        $productoIndex    = Permission::findOrCreate('producto.index',   'web');
        $productoCreate   = Permission::findOrCreate('producto.create',  'web');
        $productoUpdate   = Permission::findOrCreate('producto.update',  'web');
        $productoDestroy  = Permission::findOrCreate('producto.destroy', 'web');

        // 3) Asignar permisos a roles (idempotente)
        //    Opción A: por permiso (sobrescribe roles de ese permiso a la lista exacta)
        $categoriaIndex->syncRoles([$admin, $user]);
        $categoriaCreate->syncRoles([$admin]);
        $categoriaUpdate->syncRoles([$admin]);
        $categoriaDestroy->syncRoles([$admin]);

        $productoIndex->syncRoles([$admin, $user]);
        $productoCreate->syncRoles([$admin]);
        $productoUpdate->syncRoles([$admin]);
        $productoDestroy->syncRoles([$admin]);

        // (Alternativa B)
        // $admin->givePermissionTo([
        //   'categoria.index','categoria.create','categoria.update','categoria.destroy',
        //   'producto.index','producto.create','producto.update','producto.destroy',
        // ]);
        // $user->givePermissionTo(['categoria.index','producto.index']);
    }
}
