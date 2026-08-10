<?php

namespace Database\Seeders\Core;

use App\Models\Core\User;
use App\Models\Companies\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'supply4me-demo'],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Supply4Me Demo Company',
                'registration_number' => 'REG-001',
                'tax_number' => 'TAX-001',
                'email' => 'admin@supply4me.com',
                'phone' => '+1-555-0100',
                'address_line_1' => '123 Business Avenue',
                'city' => 'Lagos',
                'state' => 'LA',
                'postal_code' => '100001',
                'country' => 'NG',
                'currency_code' => 'NGN',
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'admin@supply4me.com'],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'company_id' => $company->id,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
                'job_title' => 'System Administrator',
                'department' => 'Administration',
            ]
        );

        $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdminRole && !$user->hasRole('super_admin', 'web')) {
            $user->assignRole($superAdminRole);
        }
    }
}
