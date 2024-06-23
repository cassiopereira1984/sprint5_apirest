<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);
        // $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']); 
        // $user = Role::create(['name' => 'player', 'guard_name' => 'api']);

        Permission::create(['name' => 'login'])->syncRoles([$admin, $user]);
        Permission::create(['name' => 'register'])->syncRoles([$admin, $user]);
        Permission::create(['name' => 'show'])->syncRoles([$admin, $user]);
        Permission::create(['name' => 'logout'])->syncRoles([$admin, $user]);
        Permission::create(['name' => 'update'])->syncRoles([$admin, $user]);
    }
}
