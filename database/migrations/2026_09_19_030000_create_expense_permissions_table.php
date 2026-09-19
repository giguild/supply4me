<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'expense.view',
            'expense.create',
            'expense.update',
            'expense.delete',
            'expense.approve',
            'expense-category.view',
            'expense-category.create',
            'expense-category.update',
            'expense-category.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'expense.view', 'expense.create', 'expense.update', 'expense.delete', 'expense.approve',
            'expense-category.view', 'expense-category.create', 'expense-category.update', 'expense-category.delete',
        ])->delete();
    }
};
