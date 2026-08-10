<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            Core\PermissionSeeder::class,
            Core\RoleSeeder::class,
            Core\UserSeeder::class,
            Companies\CompanySeeder::class,
            Branches\BranchSeeder::class,
            Products\ProductUnitSeeder::class,
            Products\ProductCategorySeeder::class,
            Products\ProductBrandSeeder::class,
            Settings\SettingSeeder::class,
        ]);
    }
}
