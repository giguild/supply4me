<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pick_lists', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('pick_number');
            $table->string('warehouse_id', 36);
            $table->enum('status', ['draft', 'assigned', 'picking', 'completed', 'cancelled'])->default('draft');
            $table->string('picker_id', 36)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('picker_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'pick_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pick_lists');
    }
};
