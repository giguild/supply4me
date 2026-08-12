<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('branch_id', 36)->nullable()->change();
            $table->string('warehouse_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('branch_id', 36)->nullable(false)->change();
            $table->string('warehouse_id', 36)->nullable(false)->change();
        });
    }
};
