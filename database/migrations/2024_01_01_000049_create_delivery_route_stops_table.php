<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_route_stops', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('route_id', 36);
            $table->string('delivery_id', 36);
            $table->integer('stop_order');
            $table->time('estimated_arrival')->nullable();
            $table->time('actual_arrival')->nullable();
            $table->enum('status', ['pending', 'arrived', 'delivered', 'skipped', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('delivery_routes')->onDelete('cascade');
            $table->foreign('delivery_id')->references('id')->on('deliveries')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_route_stops');
    }
};
