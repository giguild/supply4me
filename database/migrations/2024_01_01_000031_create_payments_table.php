<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('payment_number');
            $table->string('customer_id', 36)->nullable();
            $table->string('supplier_id', 36)->nullable();
            $table->enum('payment_type', ['incoming', 'outgoing'])->default('incoming');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'check', 'mobile_money', 'other'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->string('check_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('NGN');
            $table->decimal('exchange_rate', 10, 6)->default(1);
            $table->date('payment_date');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending');
            $table->string('approved_by', 36)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('branch_id', 36)->nullable();
            $table->string('received_by', 36)->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'payment_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
