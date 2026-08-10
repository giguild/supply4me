<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_note_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('grn_id', 36);
            $table->string('purchase_order_item_id', 36)->nullable();
            $table->string('product_id', 36);
            $table->string('variant_id', 36)->nullable();
            $table->decimal('expected_quantity', 15, 2);
            $table->decimal('received_quantity', 15, 2);
            $table->decimal('accepted_quantity', 15, 2)->default(0);
            $table->decimal('rejected_quantity', 15, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('bin_id', 36)->nullable();
            $table->enum('condition', ['good', 'damaged', 'expired', 'wrong_item'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('grn_id')->references('id')->on('goods_received_notes')->onDelete('cascade');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
            $table->foreign('bin_id')->references('id')->on('warehouse_bins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};
