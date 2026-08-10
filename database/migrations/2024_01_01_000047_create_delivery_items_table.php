<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('delivery_id', 36);
            $table->string('order_item_id', 36);
            $table->string('product_id', 36);
            $table->decimal('quantity_delivered', 15, 2)->default(0);
            $table->decimal('quantity_returned', 15, 2)->default(0);
            $table->enum('condition', ['good', 'damaged', 'partial', 'wrong_item'])->default('good');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
