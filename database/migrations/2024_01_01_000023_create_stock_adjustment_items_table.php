<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('adjustment_id', 36);
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->string('bin_id', 36)->nullable();
            $table->decimal('quantity_before', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->decimal('quantity_after', 15, 2);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('adjustment_id')->references('id')->on('stock_adjustments')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
    }
};
