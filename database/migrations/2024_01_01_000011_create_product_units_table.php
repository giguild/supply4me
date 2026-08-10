<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('name');
            $table->string('short_name');
            $table->string('base_unit_id', 36)->nullable();
            $table->decimal('conversion_factor', 15, 6)->default(1);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('base_unit_id')->references('id')->on('product_units')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
