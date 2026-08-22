<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['model_type', 'model_id']);
            $table->string('model_id', 36)->change();
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['model_type', 'model_id']);
            $table->unsignedBigInteger('model_id')->change();
            $table->index(['model_type', 'model_id']);
        });
    }
};
