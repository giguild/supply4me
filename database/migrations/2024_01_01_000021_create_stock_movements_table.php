<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('stock_item_id', 36);
            $table->enum('movement_type', ['purchase', 'sale', 'transfer', 'adjustment', 'return', 'consumption', 'production']);
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_before', 15, 2);
            $table->decimal('quantity_after', 15, 2);
            $table->string('reference_type')->nullable();
            $table->string('reference_id', 36)->nullable();
            $table->string('from_warehouse_id', 36)->nullable();
            $table->string('to_warehouse_id', 36)->nullable();
            $table->string('from_bin_id', 36)->nullable();
            $table->string('to_bin_id', 36)->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('performed_by', 36);
            $table->string('approved_by', 36)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('approved');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
