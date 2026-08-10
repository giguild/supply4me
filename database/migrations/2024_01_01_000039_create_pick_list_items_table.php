<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pick_list_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('pick_list_id', 36);
            $table->string('order_id', 36);
            $table->string('order_item_id', 36);
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->string('bin_id', 36)->nullable();
            $table->decimal('quantity_to_pick', 15, 2);
            $table->decimal('quantity_picked', 15, 2)->default(0);
            $table->enum('status', ['pending', 'picked', 'partial', 'cancelled'])->default('pending');
            $table->timestamp('picked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('pick_list_id')->references('id')->on('pick_lists')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            $table->foreign('bin_id')->references('id')->on('warehouse_bins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pick_list_items');
    }
};
