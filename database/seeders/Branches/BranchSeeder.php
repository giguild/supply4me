<?php

namespace Database\Seeders\Branches;

use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'HQ'],
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => 'Headquarters',
                    'type' => 'headquarters',
                    'email' => "hq@{$company->slug}.com",
                    'phone' => '+234-801-000-0001',
                    'address_line_1' => '100 Marina Road',
                    'city' => 'Lagos',
                    'state' => 'LA',
                    'postal_code' => '100001',
                    'country' => 'NG',
                    'is_main' => true,
                    'status' => 'active',
                ]
            );

            Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'WH-01'],
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => 'Main Warehouse',
                    'type' => 'warehouse',
                    'email' => "warehouse@{$company->slug}.com",
                    'phone' => '+234-801-000-0002',
                    'address_line_1' => '200 Industrial Estate',
                    'city' => 'Ogba',
                    'state' => 'LA',
                    'postal_code' => '100002',
                    'country' => 'NG',
                    'is_main' => false,
                    'status' => 'active',
                ]
            );

            Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'ST-01'],
                [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => 'Downtown Store',
                    'type' => 'store',
                    'email' => "store@{$company->slug}.com",
                    'phone' => '+234-801-000-0003',
                    'address_line_1' => '300 Broad Street',
                    'city' => 'Lagos Island',
                    'state' => 'LA',
                    'postal_code' => '100003',
                    'country' => 'NG',
                    'is_main' => false,
                    'status' => 'active',
                ]
            );
        }
    }
}
