<?php

namespace Database\Seeders\Companies;

use App\Models\Companies\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['slug' => 'global-distributors'],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Global Distributors Inc.',
                'registration_number' => 'GD-2024-001',
                'tax_number' => 'TAX-GD-12345',
                'email' => 'info@globaldist.com',
                'phone' => '+234-801-234-5678',
                'address_line_1' => '456 Commerce Boulevard',
                'city' => 'Lagos',
                'state' => 'LA',
                'postal_code' => '100001',
                'country' => 'NG',
                'currency_code' => 'NGN',
                'status' => 'active',
            ]
        );

        Company::firstOrCreate(
            ['slug' => 'regional-supply'],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Regional Supply Co.',
                'registration_number' => 'RS-2024-002',
                'tax_number' => 'TAX-RS-67890',
                'email' => 'contact@regionalsupply.com',
                'phone' => '+234-802-345-6789',
                'address_line_1' => '789 Industrial Park',
                'city' => 'Abuja',
                'state' => 'FC',
                'postal_code' => '900001',
                'country' => 'NG',
                'currency_code' => 'NGN',
                'status' => 'active',
            ]
        );
    }
}
