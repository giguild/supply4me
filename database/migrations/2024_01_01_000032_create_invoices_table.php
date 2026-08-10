<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('invoice_number');
            $table->string('customer_id', 36);
            $table->string('order_id', 36)->nullable();
            $table->enum('invoice_type', ['sales', 'credit_note', 'debit_note', 'proforma'])->default('sales');
            $table->enum('status', ['draft', 'sent', 'viewed', 'paid', 'partial', 'overdue', 'cancelled', 'void'])->default('draft');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('currency_code', 3)->default('NGN');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->integer('payment_terms_days')->default(0);
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('created_by', 36);
            $table->integer('version')->default(1);
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unique(['company_id', 'invoice_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
