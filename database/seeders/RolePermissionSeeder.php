<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────────
        // PERMISSIONS
        // ──────────────────────────────────────────
        $permissions = [
            // Dashboard
            'view-dashboard',
            'view-analytics',

            // Products & Inventory
            'manage-products',
            'view-products',
            'manage-categories',
            'manage-stock',
            'view-stock-movements',
            'adjust-stock',

            // POS / Sales
            'create-sale',
            'view-sales',
            'void-sale',
            'apply-discount',

            // Customers
            'manage-customers',
            'view-customers',
            'manage-loyalty',

            // Suppliers & Purchase Orders
            'manage-suppliers',
            'manage-purchase-orders',
            'receive-purchase-orders',

            // Payments
            'manage-payments',
            'verify-payments',

            // Financial Reports
            'view-reports',
            'view-cash-flow',
            'manage-cash-flow',
            'view-profit-loss',
            'export-reports',

            // User Management
            'manage-users',
            'view-users',

            // Settings
            'manage-settings',

            // Audit Trail
            'view-activity-log',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ──────────────────────────────────────────
        // ROLES
        // ──────────────────────────────────────────

        // Owner - Full access
        $owner = Role::create(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());

        // Admin - Manage everything except user management & settings
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'manage-products', 'view-products', 'manage-categories',
            'manage-stock', 'view-stock-movements', 'adjust-stock',
            'create-sale', 'view-sales', 'void-sale', 'apply-discount',
            'manage-customers', 'view-customers', 'manage-loyalty',
            'manage-suppliers', 'manage-purchase-orders', 'receive-purchase-orders',
            'manage-payments', 'verify-payments',
            'view-reports', 'view-cash-flow', 'manage-cash-flow',
            'view-profit-loss', 'export-reports',
            'view-users',
            'view-activity-log',
        ]);

        // Kasir - POS operations only
        $kasir = Role::create(['name' => 'kasir']);
        $kasir->givePermissionTo([
            'view-dashboard',
            'view-products',
            'create-sale', 'view-sales',
            'view-customers',
            'view-stock-movements',
        ]);
    }
}
