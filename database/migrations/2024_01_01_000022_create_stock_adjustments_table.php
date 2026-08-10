<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('warehouse_id', 36);
            $table->string('adjustment_number');
            $table->enum('adjustment_type', ['damage', 'expiry', 'theft', 'correction', 'recount', 'other']);
            $table->text('reason');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->string('performed_by', 36);
            $table->string('approved_by', 36)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'adjustment_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
