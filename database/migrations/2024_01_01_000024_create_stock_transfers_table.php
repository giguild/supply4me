<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('transfer_number');
            $table->string('from_warehouse_id', 36);
            $table->string('to_warehouse_id', 36);
            $table->enum('status', ['draft', 'pending', 'in_transit', 'received', 'cancelled'])->default('draft');
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('shipped_by', 36)->nullable();
            $table->string('received_by', 36)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('shipped_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'transfer_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
