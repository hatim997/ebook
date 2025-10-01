<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view archived user']);
        Permission::create(['name' => 'create archived user']);
        Permission::create(['name' => 'update archived user']);
        Permission::create(['name' => 'delete archived user']);

        Permission::create(['name' => 'view setting']);
        Permission::create(['name' => 'create setting']);
        Permission::create(['name' => 'update setting']);
        Permission::create(['name' => 'delete setting']);

        Permission::create(['name' => 'view book']);
        Permission::create(['name' => 'create book']);
        Permission::create(['name' => 'update book']);
        Permission::create(['name' => 'delete book']);

        Permission::create(['name' => 'view book law']);
        Permission::create(['name' => 'create book law']);
        Permission::create(['name' => 'update book law']);
        Permission::create(['name' => 'delete book law']);

        Permission::create(['name' => 'view user purchases']);
        Permission::create(['name' => 'create user purchases']);
        Permission::create(['name' => 'update user purchases']);
        Permission::create(['name' => 'delete user purchases']);

        Permission::create(['name' => 'view notification']);
        Permission::create(['name' => 'create notification']);
        Permission::create(['name' => 'update notification']);
        Permission::create(['name' => 'delete notification']);


        // Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // give all permissions to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();

        $superAdminRole->givePermissionTo($allPermissionNames);

        // give permissions to admin role.
        $adminRole->givePermissionTo(['view role']);
        $adminRole->givePermissionTo(['view permission']);
        $adminRole->givePermissionTo(['create user', 'view user', 'update user']);


        // Create User and assign Role to it.

        $superAdminUser = User::firstOrCreate([
                    'email' => 'superadmin@gmail.com',
                ], [
                    'name' => 'Super Admin',
                    'email' => 'superadmin@gmail.com',
                    'username' => 'superadmin',
                    'password' => Hash::make ('superadmin@gmail.com'),
                    'email_verified_at' => now(),
                ]);

        $superAdminUser->assignRole($superAdminRole);

        $superAdminProfile = $superAdminUser->profile()->firstOrCreate([
            'user_id' => $superAdminUser->id,
        ], [
            'user_id' => $superAdminUser->id,
            'first_name' => $superAdminUser->name,
        ]);

        $adminUser = User::firstOrCreate([
                            'email' => 'admin@gmail.com'
                        ], [
                            'name' => 'Admin',
                            'username' => 'admin',
                            'email' => 'admin@gmail.com',
                            'password' => Hash::make ('admin@gmail.com'),
                            'email_verified_at' => now(),
                        ]);

        $adminUser->assignRole($adminRole);

        $adminUserProfile = $adminUser->profile()->firstOrCreate([
            'user_id' => $adminUser->id,
        ], [
            'user_id' => $adminUser->id,
            'first_name' => $adminUser->name,
        ]);


        $simpleUser = User::firstOrCreate([
                            'email' => 'user@gmail.com'
                        ], [
                            'name' => 'User',
                            'username' => 'user123',
                            'email' => 'user@gmail.com',
                            'password' => Hash::make ('12345678'),
                            'email_verified_at' => now(),
                        ]);

        $simpleUser->assignRole($userRole);

        $simpleUserProfile = $simpleUser->profile()->firstOrCreate([
            'user_id' => $simpleUser->id,
        ], [
            'user_id' => $simpleUser->id,
            'first_name' => $simpleUser->name,
        ]);
    }
}
