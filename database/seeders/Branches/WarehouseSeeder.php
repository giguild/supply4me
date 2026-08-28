<?php

namespace Database\Seeders\Branches;

use App\Models\Branches\Branch;
use App\Models\Companies\Company;
use App\Models\Inventory\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $warehouseBranch = Branch::where('company_id', $company->id)
                ->where('type', 'warehouse')
                ->first();

            if (! $warehouseBranch) {
                $warehouseBranch = Branch::create([
                    'company_id' => $company->id,
                    'code' => 'WH-01',
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
                ]);
            }

            Warehouse::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'WH-MAIN'],
                [
                    'branch_id' => $warehouseBranch->id,
                    'name' => 'Main Warehouse',
                    'type' => 'main',
                    'address_line_1' => '200 Industrial Estate',
                    'city' => 'Ogba',
                    'state' => 'LA',
                    'postal_code' => '100002',
                    'country' => 'NG',
                    'capacity' => 10000,
                    'status' => 'active',
                ]
            );
        }
    }
}
