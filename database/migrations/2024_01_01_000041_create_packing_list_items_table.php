<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_list_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('packing_list_id', 36);
            $table->string('order_item_id', 36);
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->decimal('quantity_packed', 15, 2);
            $table->string('package_number')->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->text('dimensions')->nullable();
            $table->timestamps();

            $table->foreign('packing_list_id')->references('id')->on('packing_lists')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_list_items');
    }
};
