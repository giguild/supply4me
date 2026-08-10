<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => [
                'company.*',
                'branch.*',
                'user.*',
                'customer.*',
                'supplier.*',
                'product.*',
                'order.*',
                'payment.*',
                'invoice.*',
                'stock.*',
                'grn.*',
                'picklist.*',
                'packinglist.*',
                'shipment.*',
                'delivery.*',
                'report.*',
                'setting.*',
            ],
            'admin' => [
                'company.view',
                'branch.*',
                'user.*',
                'customer.*',
                'supplier.*',
                'product.*',
                'order.*',
                'payment.*',
                'invoice.*',
                'stock.*',
                'grn.*',
                'picklist.*',
                'packinglist.*',
                'shipment.*',
                'delivery.*',
                'report.*',
                'setting.*',
            ],
            'manager' => [
                'branch.view',
                'user.view',
                'customer.*',
                'supplier.*',
                'product.*',
                'order.*',
                'payment.view',
                'payment.approve',
                'invoice.*',
                'stock.*',
                'grn.*',
                'picklist.*',
                'packinglist.*',
                'shipment.*',
                'delivery.*',
                'report.*',
            ],
            'sales_rep' => [
                'customer.*',
                'product.view',
                'order.view',
                'order.create',
                'order.update',
                'payment.view',
                'invoice.view',
                'report.view-sales',
            ],
            'warehouse_staff' => [
                'product.view',
                'stock.*',
                'grn.*',
                'picklist.*',
                'packinglist.*',
                'shipment.view',
                'delivery.view',
            ],
            'accountant' => [
                'customer.view',
                'customer.view-credit',
                'supplier.view',
                'order.view',
                'payment.*',
                'invoice.*',
                'report.view-financial',
                'report.export',
            ],
            'driver' => [
                'delivery.view',
                'delivery.update',
            ],
            'viewer' => [
                'company.view',
                'branch.view',
                'user.view',
                'customer.view',
                'supplier.view',
                'product.view',
                'order.view',
                'payment.view',
                'invoice.view',
                'stock.view',
                'grn.view',
                'picklist.view',
                'packinglist.view',
                'shipment.view',
                'delivery.view',
                'report.view-sales',
                'report.view-inventory',
                'report.view-financial',
            ],
        ];

        foreach ($roles as $name => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            $permissionNames = [];
            foreach ($permissions as $permission) {
                if (str_contains($permission, '*')) {
                    $module = str_replace('.*', '', $permission);
                    $modulePermissions = \Spatie\Permission\Models\Permission::where('name', 'like', $module . '.%')
                        ->pluck('name')
                        ->toArray();
                    $permissionNames = array_merge($permissionNames, $modulePermissions);
                } else {
                    $permissionNames[] = $permission;
                }
            }

            $permissionNames = array_unique($permissionNames);
            $role->syncPermissions($permissionNames);
        }
    }
}
