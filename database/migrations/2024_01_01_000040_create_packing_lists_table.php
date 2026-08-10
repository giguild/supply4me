<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('pack_number');
            $table->string('order_id', 36);
            $table->string('warehouse_id', 36);
            $table->enum('status', ['draft', 'packing', 'completed', 'cancelled'])->default('draft');
            $table->string('packer_id', 36)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('packer_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'pack_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_lists');
    }
};
