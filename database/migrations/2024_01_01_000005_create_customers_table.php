<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('customer_number');
            $table->string('name');
            $table->string('trade_name')->nullable();
            $table->enum('customer_type', ['individual', 'business', 'government', 'ngo'])->default('business');
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms_days')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('assigned_to', 36)->nullable();
            $table->string('price_list_id', 36)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted'])->default('active');
            $table->enum('credit_status', ['good', 'warning', 'suspended', 'blocked'])->default('good');
            $table->text('notes')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->unique(['company_id', 'customer_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
