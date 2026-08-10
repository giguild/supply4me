<?php

namespace App\Actions\Inventory;

use App\Enums\Inventory\MovementType;
use App\Enums\Inventory\TransferStatus;
use App\Events\Inventory\StockTransferred;
use App\Models\Core\User;
use App\Models\Inventory\StockItem;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use Illuminate\Support\Facades\DB;

class TransferStockAction
{
    public function execute(array $data, User $user): StockTransfer
    {
        return DB::transaction(function () use ($data, $user) {
            $transfer = StockTransfer::create([
                'company_id' => $data['company_id'],
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'status' => TransferStatus::PendingApproval,
                'shipped_by' => $user->id,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $itemData) {
                $stockItem = StockItem::where('product_id', $itemData['product_id'])
                    ->where('warehouse_id', $data['from_warehouse_id'])
                    ->where('company_id', $data['company_id'])
                    ->first();

                if (! $stockItem) {
                    throw new \App\Exceptions\StockItemNotFoundException(
                        "Stock item not found for product {$itemData['product_id']}"
                    );
                }

                $availableQuantity = $stockItem->quantity_on_hand - $stockItem->quantity_reserved;

                if ($availableQuantity < $itemData['quantity']) {
                    throw new \App\Exceptions\InsufficientStockException(
                        "Insufficient stock for transfer. Available: {$availableQuantity}, Requested: {$itemData['quantity']}"
                    );
                }

                $previousQuantity = $stockItem->quantity_on_hand;

                $stockItem->decrement('quantity_on_hand', $itemData['quantity']);
                $stockItem->incrementVersion();

                StockMovement::create([
                    'company_id' => $stockItem->company_id,
                    'stock_item_id' => $stockItem->id,
                    'movement_type' => MovementType::Transfer,
                    'quantity' => $itemData['quantity'],
                    'quantity_before' => $previousQuantity,
                    'quantity_after' => $previousQuantity - $itemData['quantity'],
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $transfer->id,
                    'from_warehouse_id' => $data['from_warehouse_id'],
                    'to_warehouse_id' => $data['to_warehouse_id'],
                    'reason' => "Transfer to warehouse {$data['to_warehouse_id']}",
                    'performed_by' => $user->id,
                ]);

                StockTransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $itemData['product_id'],
                    'variant_id' => $itemData['variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'quantity_received' => 0,
                    'bin_id' => $itemData['bin_id'] ?? null,
                ]);
            }

            event(new StockTransferred($transfer, $user));

            return $transfer;
        });
    }
}
