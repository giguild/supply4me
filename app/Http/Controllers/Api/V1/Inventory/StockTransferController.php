<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Actions\Inventory\ReceiveTransferStockAction;
use App\Actions\Inventory\TransferStockAction;
use App\Enums\Inventory\TransferStatus;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StockTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function __construct(
        protected TransferStockAction $transferStockAction,
        protected ReceiveTransferStockAction $receiveTransferStockAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $transfers = StockTransfer::query()
            ->with(['items.product', 'fromWarehouse', 'toWarehouse'])
            ->when($request->search, fn ($q, $s) => $q->where('transfer_number', 'like', "%{$s}%"))
            ->when($request->from_warehouse_id, fn ($q, $w) => $q->where('from_warehouse_id', $w))
            ->when($request->to_warehouse_id, fn ($q, $w) => $q->where('to_warehouse_id', $w))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Stock transfers retrieved successfully',
            'data' => $transfers,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'notes' => 'nullable|string|max:1000',
            'expected_date' => 'nullable|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $transfer = $this->transferStockAction->execute($validated);

        return $this->created($transfer->load(['items.product', 'fromWarehouse', 'toWarehouse']), 'Transfer created successfully');
    }

    public function show(StockTransfer $stockTransfer): JsonResponse
    {
        return $this->success(
            $stockTransfer->load(['items.product', 'fromWarehouse', 'toWarehouse', 'approvedBy', 'shippedBy', 'receivedBy'])
        );
    }

    public function approve(StockTransfer $stockTransfer): JsonResponse
    {
        if ($stockTransfer->status !== TransferStatus::PendingApproval) {
            return $this->error('Only pending transfers can be approved', 422);
        }

        $stockTransfer->update([
            'status' => TransferStatus::Approved,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $this->success(
            $stockTransfer->fresh()->load(['items.product', 'fromWarehouse', 'toWarehouse']),
            'Transfer approved successfully'
        );
    }

    public function ship(StockTransfer $stockTransfer): JsonResponse
    {
        if ($stockTransfer->status !== TransferStatus::Approved) {
            return $this->error('Only approved transfers can be shipped', 422);
        }

        $stockTransfer->update([
            'status' => TransferStatus::InTransit,
            'shipped_at' => now(),
        ]);

        return $this->success(
            $stockTransfer->fresh(),
            'Transfer shipped successfully'
        );
    }

    public function receive(Request $request, StockTransfer $stockTransfer): JsonResponse
    {
        if ($stockTransfer->status !== TransferStatus::InTransit) {
            return $this->error('Only in-transit transfers can be received', 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.stock_transfer_item_id' => 'required|exists:stock_transfer_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.condition' => 'sometimes|in:good,damaged,expired',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $items = array_map(function ($item) {
            $item['transfer_item_id'] = $item['stock_transfer_item_id'];
            return $item;
        }, $validated['items']);

        $transferItems = $stockTransfer->items->keyBy('id');
        foreach ($items as $item) {
            if ($transferItems->has($item['transfer_item_id'])) {
                continue;
            }

            return $this->error('One or more items do not belong to this transfer', 422);
        }

        $transfer = $this->receiveTransferStockAction->execute($stockTransfer, $items, $request->user());

        return $this->success(
            $transfer->load(['items.product', 'fromWarehouse', 'toWarehouse']),
            'Transfer received successfully'
        );
    }
}