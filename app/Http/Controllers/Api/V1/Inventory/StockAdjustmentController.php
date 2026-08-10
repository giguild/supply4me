<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Actions\Inventory\AdjustStockAction;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Resources\Inventory\StockAdjustmentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function __construct(
        protected AdjustStockAction $adjustStockAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $adjustments = StockAdjustment::query()
            ->with(['items.product', 'warehouse'])
            ->when($request->search, fn ($q, $s) => $q->where('adjustment_number', 'like', "%{$s}%"))
            ->when($request->warehouse_id, fn ($q, $w) => $q->where('warehouse_id', $w))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->paginated($adjustments, StockAdjustmentResource::collection($adjustments->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'type' => 'required|string|in:increase,decrease,recount',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $adjustment = $this->adjustStockAction->execute($validated);

        return $this->created(
            new StockAdjustmentResource($adjustment->load(['items.product', 'warehouse'])),
            'Stock adjustment created successfully'
        );
    }

    public function show(StockAdjustment $stockAdjustment): JsonResponse
    {
        return $this->success(
            new StockAdjustmentResource($stockAdjustment->load(['items.product', 'warehouse', 'approvedBy']))
        );
    }

    public function approve(StockAdjustment $stockAdjustment): JsonResponse
    {
        if ($stockAdjustment->status !== 'pending') {
            return $this->error('Only pending adjustments can be approved', 422);
        }

        $stockAdjustment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $this->success(
            new StockAdjustmentResource($stockAdjustment->fresh()),
            'Adjustment approved successfully'
        );
    }

    public function reject(Request $request, StockAdjustment $stockAdjustment): JsonResponse
    {
        if ($stockAdjustment->status !== 'pending') {
            return $this->error('Only pending adjustments can be rejected', 422);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $stockAdjustment->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        return $this->success(
            new StockAdjustmentResource($stockAdjustment->fresh()),
            'Adjustment rejected successfully'
        );
    }
}
