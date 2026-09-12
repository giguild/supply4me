<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── stock_adjustments ─────────────────────────────────────────────
        DB::statement("ALTER TABLE stock_adjustments CHANGE adjustment_type `type` ENUM('cycle_count','physical_count','damage','expiry','shrinkage','other') NOT NULL");
        DB::statement("ALTER TABLE stock_adjustments ADD rejected_by VARCHAR(36) NULL AFTER approved_at");
        DB::statement("ALTER TABLE stock_adjustments ADD rejected_at TIMESTAMP NULL AFTER rejected_by");
        DB::statement("ALTER TABLE stock_adjustments ADD rejection_reason TEXT NULL AFTER rejected_at");
        DB::statement("ALTER TABLE stock_adjustments ADD CONSTRAINT stock_adjustments_rejected_by_foreign FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL");

        // ── stock_adjustment_items ────────────────────────────────────────
        DB::statement("ALTER TABLE stock_adjustment_items DROP FOREIGN KEY stock_adjustment_items_stock_adjustment_id_foreign");
        DB::statement("ALTER TABLE stock_adjustment_items CHANGE stock_adjustment_id adjustment_id VARCHAR(36) NOT NULL");
        DB::statement("ALTER TABLE stock_adjustment_items CHANGE quantity_adjusted `difference` DECIMAL(15,2) NOT NULL");
        DB::statement("ALTER TABLE stock_adjustment_items ADD bin_id VARCHAR(36) NULL AFTER variant_id");
        DB::statement("ALTER TABLE stock_adjustment_items ADD CONSTRAINT stock_adjustment_items_adjustment_id_foreign FOREIGN KEY (adjustment_id) REFERENCES stock_adjustments(id) ON DELETE CASCADE");

        // ── stock_transfers ───────────────────────────────────────────────
        DB::statement("ALTER TABLE stock_transfers MODIFY status ENUM('draft','pending_approval','approved','in_transit','received','cancelled') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE stock_transfers ADD shipped_at TIMESTAMP NULL AFTER received_date");
        DB::statement("ALTER TABLE stock_transfers CHANGE received_date received_at TIMESTAMP NULL");
        DB::statement("ALTER TABLE stock_transfers ADD approved_by VARCHAR(36) NULL AFTER shipped_by");
        DB::statement("ALTER TABLE stock_transfers ADD approved_at TIMESTAMP NULL AFTER approved_by");
        DB::statement("ALTER TABLE stock_transfers ADD CONSTRAINT stock_transfers_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL");

        // ── stock_transfer_items ──────────────────────────────────────────
        DB::statement("ALTER TABLE stock_transfer_items DROP FOREIGN KEY stock_transfer_items_stock_transfer_id_foreign");
        DB::statement("ALTER TABLE stock_transfer_items CHANGE stock_transfer_id transfer_id VARCHAR(36) NOT NULL");
        DB::statement("ALTER TABLE stock_transfer_items CHANGE quantity_sent quantity DECIMAL(15,2) NOT NULL");
        DB::statement("ALTER TABLE stock_transfer_items ADD `condition` ENUM('good','damaged','expired') NOT NULL DEFAULT 'good' AFTER quantity_received");
        DB::statement("ALTER TABLE stock_transfer_items ADD bin_id VARCHAR(36) NULL AFTER variant_id");
        DB::statement("ALTER TABLE stock_transfer_items ADD CONSTRAINT stock_transfer_items_transfer_id_foreign FOREIGN KEY (transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stock_transfer_items DROP FOREIGN KEY stock_transfer_items_transfer_id_foreign");
        DB::statement("ALTER TABLE stock_transfer_items DROP COLUMN bin_id");
        DB::statement("ALTER TABLE stock_transfer_items DROP COLUMN `condition`");
        DB::statement("ALTER TABLE stock_transfer_items CHANGE quantity quantity_sent DECIMAL(15,2) NOT NULL");
        DB::statement("ALTER TABLE stock_transfer_items CHANGE transfer_id stock_transfer_id VARCHAR(36) NOT NULL");
        DB::statement("ALTER TABLE stock_transfer_items ADD CONSTRAINT stock_transfer_items_stock_transfer_id_foreign FOREIGN KEY (stock_transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE");

        DB::statement("ALTER TABLE stock_transfers DROP FOREIGN KEY stock_transfers_approved_by_foreign");
        DB::statement("ALTER TABLE stock_transfers DROP COLUMN approved_at");
        DB::statement("ALTER TABLE stock_transfers DROP COLUMN approved_by");
        DB::statement("ALTER TABLE stock_transfers CHANGE received_at received_date DATE NULL");
        DB::statement("ALTER TABLE stock_transfers DROP COLUMN shipped_at");
        DB::statement("ALTER TABLE stock_transfers MODIFY status ENUM('draft','pending','in_transit','received','cancelled') NOT NULL DEFAULT 'draft'");

        DB::statement("ALTER TABLE stock_adjustment_items DROP FOREIGN KEY stock_adjustment_items_adjustment_id_foreign");
        DB::statement("ALTER TABLE stock_adjustment_items DROP COLUMN bin_id");
        DB::statement("ALTER TABLE stock_adjustment_items CHANGE `difference` quantity_adjusted DECIMAL(15,2) NOT NULL");
        DB::statement("ALTER TABLE stock_adjustment_items CHANGE adjustment_id stock_adjustment_id VARCHAR(36) NOT NULL");
        DB::statement("ALTER TABLE stock_adjustment_items ADD CONSTRAINT stock_adjustment_items_stock_adjustment_id_foreign FOREIGN KEY (stock_adjustment_id) REFERENCES stock_adjustments(id) ON DELETE CASCADE");

        DB::statement("ALTER TABLE stock_adjustments DROP FOREIGN KEY stock_adjustments_rejected_by_foreign");
        DB::statement("ALTER TABLE stock_adjustments DROP COLUMN rejection_reason");
        DB::statement("ALTER TABLE stock_adjustments DROP COLUMN rejected_at");
        DB::statement("ALTER TABLE stock_adjustments DROP COLUMN rejected_by");
        DB::statement("ALTER TABLE stock_adjustments CHANGE `type` adjustment_type ENUM('damage','expiry','theft','correction','recount','other') NOT NULL");
    }
};