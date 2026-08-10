<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('shipment_number');
            $table->string('order_id', 36);
            $table->string('warehouse_id', 36);
            $table->string('carrier_id', 36)->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipping_label_url')->nullable();
            $table->enum('status', ['draft', 'label_created', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('draft');
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('weight', 10, 3)->nullable();
            $table->text('dimensions')->nullable();
            $table->boolean('signature_required')->default(false);
            $table->decimal('insured_value', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('carrier_id')->references('id')->on('shipping_carriers')->onDelete('set null');
            $table->unique(['company_id', 'shipment_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
