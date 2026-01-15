<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for Super Admin
        Permission::create(['name' => 'manage-establishments']);
        Permission::create(['name' => 'manage-subscriptions']);
        Permission::create(['name' => 'view-all-stats']);

        // Create permissions for Manager
        Permission::create(['name' => 'manage-servers']);
        Permission::create(['name' => 'manage-categories']);
        Permission::create(['name' => 'manage-menu-items']);
        Permission::create(['name' => 'manage-tables']);
        Permission::create(['name' => 'view-orders']);
        Permission::create(['name' => 'view-statistics']);

        // Create permissions for Server
        Permission::create(['name' => 'update-order-status']);
        Permission::create(['name' => 'release-table']);

        // Create roles and assign permissions

        // Super Admin - can do everything
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo([
            'manage-establishments',
            'manage-subscriptions',
            'view-all-stats',
        ]);

        // Manager - can manage their establishment
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'manage-servers',
            'manage-categories',
            'manage-menu-items',
            'manage-tables',
            'view-orders',
            'view-statistics',
        ]);

        // Server - can manage orders
        $server = Role::create(['name' => 'server']);
        $server->givePermissionTo([
            'view-orders',
            'update-order-status',
            'release-table',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
