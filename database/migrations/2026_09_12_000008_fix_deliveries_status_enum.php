<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE deliveries MODIFY status ENUM('pending', 'assigned', 'in_transit', 'out_for_delivery', 'delivered', 'partial_delivery', 'failed', 'failed_attempt', 'rescheduled', 'returned', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE deliveries MODIFY status ENUM('pending', 'assigned', 'in_transit', 'delivered', 'failed', 'rescheduled', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};