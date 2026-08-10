<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_zones', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('warehouse_id', 36);
            $table->string('name');
            $table->string('code');
            $table->enum('type', ['storage', 'receiving', 'shipping', 'picking', 'staging', 'hazmat', 'cold_storage'])->default('storage');
            $table->boolean('temperature_controlled')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->unique(['warehouse_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_zones');
    }
};
