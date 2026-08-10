<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('warehouse_id', 36);
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->string('bin_id', 36)->nullable();
            $table->decimal('quantity_on_hand', 15, 2)->default(0);
            $table->decimal('quantity_reserved', 15, 2)->default(0);
            $table->decimal('quantity_on_order', 15, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->timestamp('last_counted_at')->nullable();
            $table->timestamp('last_received_at')->nullable();
            $table->timestamp('last_sold_at')->nullable();
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'quarantine'])->default('active');
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            $table->foreign('bin_id')->references('id')->on('warehouse_bins')->onDelete('set null');
            $table->unique(['warehouse_id', 'product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
