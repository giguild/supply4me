<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('grn_number');
            $table->string('purchase_order_id', 36)->nullable();
            $table->string('supplier_id', 36);
            $table->string('warehouse_id', 36);
            $table->date('receiving_date');
            $table->enum('status', ['draft', 'pending', 'inspecting', 'accepted', 'partial', 'rejected'])->default('draft');
            $table->string('received_by', 36);
            $table->string('checked_by', 36)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('received_by')->references('id')->on('users');
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'grn_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};
