<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_bins', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('zone_id', 36);
            $table->string('code');
            $table->string('aisle')->nullable();
            $table->string('rack')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->decimal('current_quantity', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('zone_id')->references('id')->on('warehouse_zones')->onDelete('cascade');
            $table->unique(['zone_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_bins');
    }
};
