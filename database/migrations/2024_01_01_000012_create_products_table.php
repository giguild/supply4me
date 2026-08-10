<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('sku');
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('category_id', 36)->nullable();
            $table->string('brand_id', 36)->nullable();
            $table->string('unit_id', 36)->nullable();
            $table->enum('product_type', ['standard', 'service', 'bundle', 'digital'])->default('standard');
            $table->boolean('is_sellable')->default(true);
            $table->boolean('is_purchasable')->default(true);
            $table->boolean('is_stockable')->default(true);
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('weight_unit', 2)->nullable();
            $table->text('dimensions')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('minimum_price', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('shelf_life_days')->nullable();
            $table->integer('warranty_days')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->text('tags')->nullable();
            $table->text('attributes')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('product_brands')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('product_units')->onDelete('set null');
            $table->unique(['company_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
