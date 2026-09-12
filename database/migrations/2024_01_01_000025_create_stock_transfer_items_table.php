<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('transfer_id', 36);
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->string('bin_id', 36)->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_received', 15, 2)->nullable();
            $table->enum('condition', ['good', 'damaged', 'expired'])->default('good');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
    }
};