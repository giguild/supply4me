<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('estimated_time')->nullable();
            $table->string('delivery_time')->nullable();
            $table->boolean('signature_required')->default(false);
            $table->json('metadata')->nullable();
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['estimated_time', 'delivery_time', 'signature_required', 'metadata']);
        });

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};