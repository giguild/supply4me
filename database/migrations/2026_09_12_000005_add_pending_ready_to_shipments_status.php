<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY status ENUM('draft', 'label_created', 'pending', 'ready', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY status ENUM('draft', 'label_created', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned') NOT NULL DEFAULT 'draft'");
    }
};