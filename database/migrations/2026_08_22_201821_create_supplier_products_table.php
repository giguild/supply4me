<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->string('supplier_id', 36);
            $table->string('product_id', 36);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->integer('minimum_quantity')->nullable();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->primary(['supplier_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
