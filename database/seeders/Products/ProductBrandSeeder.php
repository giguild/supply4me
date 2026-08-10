<?php

namespace Database\Seeders\Products;

use App\Models\Companies\Company;
use App\Models\Products\ProductBrand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductBrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Coca-Cola',
            'PepsiCo',
            'Nestle',
            'Unilever',
            'Procter & Gamble',
            'Samsung',
            'Apple',
            'Microsoft',
            'Sony',
            'LG',
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            foreach ($brands as $brandName) {
                ProductBrand::firstOrCreate(
                    ['company_id' => $company->id, 'slug' => Str::slug($brandName)],
                    [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'name' => $brandName,
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
