<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'company' => [
                'company.view',
                'company.create',
                'company.update',
                'company.delete',
                'company.manage',
            ],
            'branch' => [
                'branch.view',
                'branch.create',
                'branch.update',
                'branch.delete',
                'branch.manage',
            ],
            'user' => [
                'user.view',
                'user.create',
                'user.update',
                'user.delete',
                'user.manage',
                'user.assign-role',
            ],
            'customer' => [
                'customer.view',
                'customer.create',
                'customer.update',
                'customer.delete',
                'customer.manage',
                'customer.view-credit',
                'customer.update-credit',
            ],
            'supplier' => [
                'supplier.view',
                'supplier.create',
                'supplier.update',
                'supplier.delete',
                'supplier.manage',
            ],
            'product' => [
                'product.view',
                'product.create',
                'product.update',
                'product.delete',
                'product.manage',
                'product.view-category',
                'product.manage-category',
                'product.view-brand',
                'product.manage-brand',
            ],
            'order' => [
                'order.view',
                'order.create',
                'order.update',
                'order.delete',
                'order.confirm',
                'order.cancel',
                'order.manage',
                'order.view-items',
                'order.manage-items',
            ],
            'payment' => [
                'payment.view',
                'payment.create',
                'payment.update',
                'payment.delete',
                'payment.approve',
                'payment.reject',
                'payment.refund',
                'payment.manage',
            ],
            'invoice' => [
                'invoice.view',
                'invoice.create',
                'invoice.update',
                'invoice.delete',
                'invoice.send',
                'invoice.void',
                'invoice.manage',
            ],
            'stock' => [
                'stock.view',
                'stock.update',
                'stock.adjust',
                'stock.transfer',
                'stock.manage',
                'stock.view-movements',
            ],
            'grn' => [
                'grn.view',
                'grn.create',
                'grn.update',
                'grn.complete',
                'grn.manage',
            ],
            'picklist' => [
                'picklist.view',
                'picklist.create',
                'picklist.update',
                'picklist.complete',
                'picklist.manage',
            ],
            'packinglist' => [
                'packinglist.view',
                'packinglist.create',
                'packinglist.update',
                'packinglist.complete',
                'packinglist.manage',
            ],
            'shipment' => [
                'shipment.view',
                'shipment.create',
                'shipment.update',
                'shipment.track',
                'shipment.manage',
            ],
            'delivery' => [
                'delivery.view',
                'delivery.create',
                'delivery.update',
                'delivery.assign-driver',
                'delivery.manage',
                'delivery.view-routes',
                'delivery.manage-routes',
            ],
            'report' => [
                'report.view-sales',
                'report.view-inventory',
                'report.view-financial',
                'report.export',
                'report.manage',
            ],
            'setting' => [
                'setting.view',
                'setting.update',
                'setting.manage',
            ],
        ];

        foreach ($permissions as $group => $items) {
            foreach ($items as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}
