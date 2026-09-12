<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('draft', 'pending', 'confirmed', 'processing', 'picking', 'packing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled', 'on_hold') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'on_hold') NOT NULL DEFAULT 'draft'");
    }
};