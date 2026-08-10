<?php

namespace App\Console\Commands;

use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use Illuminate\Console\Command;

class SyncStockLevelsCommand extends Command
{
    protected $signature = 'stock:sync-levels {--fix : Fix discrepancies automatically}';

    protected $description = 'Sync stock levels based on stock movements';

    public function handle(): int
    {
        $this->info('Starting stock level synchronization...');

        $stockItems = StockItem::with('stockMovements')->get();

        $bar = $this->output->createProgressBar($stockItems->count());
        $bar->start();

        $discrepancies = 0;
        $fixed = 0;

        foreach ($stockItems as $stockItem) {
            $calculatedQuantity = $stockItem->stockMovements()
                ->where('status', 'approved')
                ->sum('quantity');

            if (abs($stockItem->quantity_on_hand - $calculatedQuantity) > 0.001) {
                $discrepancies++;
                $this->newLine();
                $this->warn("Discrepancy found for {$stockItem->product->sku} in {$stockItem->warehouse->name}");
                $this->line("  Current: {$stockItem->quantity_on_hand} | Calculated: {$calculatedQuantity}");

                if ($this->option('fix')) {
                    $stockItem->update([
                        'quantity_on_hand' => $calculatedQuantity,
                        'version' => $stockItem->version + 1,
                    ]);
                    $fixed++;
                    $this->info("  Fixed: Updated to {$calculatedQuantity}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Synchronization complete.");
        $this->info("Total stock items checked: {$stockItems->count()}");
        $this->info("Discrepancies found: {$discrepancies}");

        if ($this->option('fix')) {
            $this->info("Discrepancies fixed: {$fixed}");
        }

        return Command::SUCCESS;
    }
}
