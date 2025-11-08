<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Asegura que existan los roles (por si RoleSeeder no corrió antes)
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('user',  'web'); // cambia a 'cliente' si usas ese nombre

        // Admin (usa ENV si están definidos; si no, defaults seguros)
        $adminEmail = env('ADMIN_EMAIL', 'admin@gmail.com');
        $adminPass  = env('ADMIN_PASSWORD', '123456789');

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],                                   // clave única
            ['name' => 'Admin', 'password' => Hash::make($adminPass)]   // update si existe
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Usuario estándar
        $userEmail = env('USER_EMAIL', 'user@gmail.com');
        $userPass  = env('USER_PASSWORD', '12345678');

        $user = User::updateOrCreate(
            ['email' => $userEmail],
            ['name' => 'User', 'password' => Hash::make($userPass)]
        );
        if (! $user->hasRole('user')) { // o 'cliente'
            $user->assignRole('user');
        }
    }
}
