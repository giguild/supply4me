<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['warehouses', 'branches', 'companies', 'customer_shipping_addresses'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('country', 100)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach (['warehouses', 'branches', 'companies', 'customer_shipping_addresses'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('country', 2)->nullable()->change();
            });
        }
    }
};