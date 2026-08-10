<?php

namespace Database\Seeders\Products;

use App\Models\Companies\Company;
use App\Models\Products\ProductUnit;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'short_name' => 'pc', 'conversion_factor' => 1],
            ['name' => 'Box', 'short_name' => 'box', 'conversion_factor' => 1],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'conversion_factor' => 1],
            ['name' => 'Litre', 'short_name' => 'L', 'conversion_factor' => 1],
            ['name' => 'Metre', 'short_name' => 'm', 'conversion_factor' => 1],
            ['name' => 'Dozen', 'short_name' => 'dz', 'conversion_factor' => 12],
            ['name' => 'Pack', 'short_name' => 'pack', 'conversion_factor' => 1],
            ['name' => 'Carton', 'short_name' => 'ctn', 'conversion_factor' => 1],
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            foreach ($units as $unit) {
                ProductUnit::firstOrCreate(
                    ['company_id' => $company->id, 'short_name' => $unit['short_name']],
                    [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'name' => $unit['name'],
                        'conversion_factor' => $unit['conversion_factor'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
