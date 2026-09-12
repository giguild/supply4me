<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── pick_lists ────────────────────────────────────────────────────
        if (Schema::hasColumn('pick_lists', 'pick_number')) {
            DB::statement('ALTER TABLE pick_lists CHANGE pick_number pick_list_number VARCHAR(255) NOT NULL');
        }
        if (! Schema::hasColumn('pick_lists', 'order_id')) {
            DB::statement('ALTER TABLE pick_lists ADD order_id VARCHAR(36) NULL AFTER warehouse_id');
        }
        DB::statement("ALTER TABLE pick_lists MODIFY status ENUM('draft','pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement('ALTER TABLE pick_lists ADD CONSTRAINT pick_lists_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL');

        // ── pick_list_items ───────────────────────────────────────────────
        DB::statement("ALTER TABLE pick_list_items MODIFY status ENUM('pending','picking','picked','short') NOT NULL DEFAULT 'pending'");

        // ── packing_lists ─────────────────────────────────────────────────
        if (Schema::hasColumn('packing_lists', 'pack_number')) {
            DB::statement('ALTER TABLE packing_lists CHANGE pack_number packing_list_number VARCHAR(255) NOT NULL');
        }
        if (! Schema::hasColumn('packing_lists', 'packed_at')) {
            DB::statement('ALTER TABLE packing_lists ADD packed_at TIMESTAMP NULL AFTER started_at');
        }
        if (! Schema::hasColumn('packing_lists', 'pick_list_id')) {
            DB::statement('ALTER TABLE packing_lists ADD pick_list_id VARCHAR(36) NULL AFTER order_id');
        }
        DB::statement("ALTER TABLE packing_lists MODIFY status ENUM('draft','in_progress','packed','verified') NOT NULL DEFAULT 'draft'");
        DB::statement('ALTER TABLE packing_lists ADD CONSTRAINT packing_lists_pick_list_id_foreign FOREIGN KEY (pick_list_id) REFERENCES pick_lists(id) ON DELETE SET NULL');

        // ── packing_list_items ────────────────────────────────────────────
        if (Schema::hasColumn('packing_list_items', 'quantity_packed')) {
            DB::statement('ALTER TABLE packing_list_items CHANGE quantity_packed quantity DECIMAL(15,2) NOT NULL');
        }
        DB::statement('ALTER TABLE packing_list_items DROP COLUMN package_number');
        if (! Schema::hasColumn('packing_list_items', 'package_type')) {
            DB::statement('ALTER TABLE packing_list_items ADD package_type VARCHAR(255) NULL AFTER quantity');
        }
        if (! Schema::hasColumn('packing_list_items', 'tracking_number')) {
            DB::statement('ALTER TABLE packing_list_items ADD tracking_number VARCHAR(255) NULL AFTER package_type');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE packing_list_items DROP COLUMN tracking_number');
        DB::statement('ALTER TABLE packing_list_items DROP COLUMN package_type');
        DB::statement('ALTER TABLE packing_list_items ADD package_number VARCHAR(255) NULL AFTER quantity');
        DB::statement('ALTER TABLE packing_list_items CHANGE quantity quantity_packed DECIMAL(15,2) NOT NULL');

        DB::statement("ALTER TABLE packing_lists MODIFY status ENUM('draft','packing','completed','cancelled') NOT NULL DEFAULT 'draft'");
        DB::statement('ALTER TABLE packing_lists DROP FOREIGN KEY packing_lists_pick_list_id_foreign');
        DB::statement('ALTER TABLE packing_lists DROP COLUMN pick_list_id');
        DB::statement('ALTER TABLE packing_lists DROP COLUMN packed_at');
        DB::statement('ALTER TABLE packing_lists CHANGE packing_list_number pack_number VARCHAR(255) NOT NULL');

        DB::statement("ALTER TABLE pick_list_items MODIFY status ENUM('pending','picked','partial','cancelled') NOT NULL DEFAULT 'pending'");

        DB::statement('ALTER TABLE pick_lists DROP FOREIGN KEY pick_lists_order_id_foreign');
        DB::statement("ALTER TABLE pick_lists MODIFY status ENUM('draft','assigned','picking','completed','cancelled') NOT NULL DEFAULT 'draft'");
        DB::statement('ALTER TABLE pick_lists DROP COLUMN order_id');
        DB::statement('ALTER TABLE pick_lists CHANGE pick_list_number pick_number VARCHAR(255) NOT NULL');
    }
};