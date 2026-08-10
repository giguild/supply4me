<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('order_number');
            $table->string('customer_id', 36);
            $table->string('branch_id', 36);
            $table->string('warehouse_id', 36);
            $table->string('price_list_id', 36)->nullable();
            $table->enum('order_type', ['sales', 'return', 'exchange'])->default('sales');
            $table->enum('status', ['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'on_hold'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded', 'overpaid'])->default('unpaid');
            $table->enum('fulfillment_status', ['unfulfilled', 'partial', 'fulfilled', 'returned'])->default('unfulfilled');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->date('order_date');
            $table->date('requested_delivery_date')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('currency_code', 3)->default('NGN');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->string('shipping_address_id', 36)->nullable();
            $table->text('billing_address')->nullable();
            $table->integer('payment_terms_days')->default(0);
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('po_number')->nullable();
            $table->string('assigned_to', 36)->nullable();
            $table->integer('version')->default(1);
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
