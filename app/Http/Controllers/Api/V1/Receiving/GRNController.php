<?php

namespace App\Http\Controllers\Api\V1\Receiving;

use App\Actions\Receiving\CreateGRNAction;
use App\Actions\Receiving\ReceiveGoodsAction;
use App\Actions\Receiving\CompleteReceivingAction;
use App\Http\Controllers\Controller;
use App\Models\Receiving\GoodsReceivedNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GRNController extends Controller
{
    public function __construct(
        protected CreateGRNAction $createGRNAction,
        protected ReceiveGoodsAction $receiveGoodsAction,
        protected CompleteReceivingAction $completeReceivingAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $grns = GoodsReceivedNote::query()
            ->with(['supplier', 'warehouse', 'items.product'])
            ->when($request->search, fn ($q, $s) => $q->where('grn_number', 'like', "%{$s}%"))
            ->when($request->supplier_id, fn ($q, $s) => $q->where('supplier_id', $s))
            ->when($request->warehouse_id, fn ($q, $w) => $q->where('warehouse_id', $w))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'GRNs retrieved successfully',
            'data' => $grns,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'expected_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.expected_quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $grn = $this->createGRNAction->execute($validated);

        return $this->created(
            $grn->load(['supplier', 'warehouse', 'items.product']),
            'GRN created successfully'
        );
    }

    public function show(GoodsReceivedNote $grn): JsonResponse
    {
        return $this->success(
            $grn->load(['supplier', 'warehouse', 'items.product', 'receivedBy'])
        );
    }

    public function receive(Request $request, GoodsReceivedNote $grn): JsonResponse
    {
        if ($grn->status !== 'in_progress' && $grn->status !== 'draft') {
            return $this->error('GRN cannot receive items in current status', 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.grn_item_id' => 'required|exists:goods_received_note_items,id',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.condition' => 'required|string|in:good,damaged,expired',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        if ($grn->status === 'draft') {
            $grn->update(['status' => 'in_progress']);
        }

        $grn = $this->receiveGoodsAction->execute($grn, $validated);

        return $this->success(
            $grn->fresh()->load(['items.product']),
            'Goods received successfully'
        );
    }

    public function complete(GoodsReceivedNote $grn): JsonResponse
    {
        $grn = $this->completeReceivingAction->execute($grn);

        return $this->success(
            $grn->fresh(),
            'GRN completed successfully'
        );
    }
}
