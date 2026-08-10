<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('delivery_number');
            $table->string('order_id', 36);
            $table->string('shipment_id', 36)->nullable();
            $table->string('driver_id', 36)->nullable();
            $table->string('customer_id', 36);
            $table->text('delivery_address');
            $table->decimal('delivery_latitude', 10, 8)->nullable();
            $table->decimal('delivery_longitude', 11, 8)->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time_start')->nullable();
            $table->time('scheduled_time_end')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->time('actual_delivery_time')->nullable();
            $table->enum('status', ['pending', 'assigned', 'in_transit', 'delivered', 'failed', 'rescheduled', 'cancelled'])->default('pending');
            $table->text('delivery_notes')->nullable();
            $table->text('driver_notes')->nullable();
            $table->string('signature_url')->nullable();
            $table->text('proof_of_delivery')->nullable();
            $table->integer('attempt_count')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->text('failure_reason')->nullable();
            $table->date('rescheduled_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unique(['company_id', 'delivery_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
