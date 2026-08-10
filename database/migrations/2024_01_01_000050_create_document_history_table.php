<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_history', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('company_id', 36);
            $table->string('mediable_type');
            $table->string('mediable_id', 36);
            $table->unsignedBigInteger('media_id');
            $table->integer('version')->default(1);
            $table->enum('action', ['uploaded', 'replaced', 'deleted']);
            $table->string('performed_by', 36);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users');
            $table->index(['mediable_type', 'mediable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_history');
    }
};
