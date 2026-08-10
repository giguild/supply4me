<?php

namespace App\Http\Controllers\Api\V1\PickingPacking;

use App\Actions\PickingPacking\GeneratePickListAction;
use App\Actions\PickingPacking\PickItemAction;
use App\Http\Controllers\Controller;
use App\Models\PickingPacking\PickList;
use App\Models\PickingPacking\PickListItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PickListController extends Controller
{
    public function __construct(
        protected GeneratePickListAction $generatePickListAction,
        protected PickItemAction $pickItemAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pickLists = PickList::query()
            ->with(['order', 'warehouse', 'items.product'])
            ->when($request->search, fn ($q, $s) => $q->where('pick_list_number', 'like', "%{$s}%"))
            ->when($request->order_id, fn ($q, $o) => $q->where('order_id', $o))
            ->when($request->warehouse_id, fn ($q, $w) => $q->where('warehouse_id', $w))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->picker_id, fn ($q, $p) => $q->where('picker_id', $p))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Pick lists retrieved successfully',
            'data' => $pickLists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'priority' => 'sometimes|string|in:low,normal,high,urgent',
            'notes' => 'nullable|string|max:1000',
        ]);

        $pickList = $this->generatePickListAction->execute($validated);

        return $this->created(
            $pickList->load(['order', 'warehouse', 'items.product']),
            'Pick list generated successfully'
        );
    }

    public function show(PickList $pickList): JsonResponse
    {
        return $this->success(
            $pickList->load(['order', 'warehouse', 'items.product', 'picker'])
        );
    }

    public function start(PickList $pickList): JsonResponse
    {
        if ($pickList->status !== 'pending' && $pickList->status !== 'draft') {
            return $this->error('Pick list cannot be started in current status', 422);
        }

        $pickList->update([
            'status' => 'in_progress',
            'picker_id' => auth()->id(),
            'started_at' => now(),
        ]);

        return $this->success(
            $pickList->fresh(),
            'Pick list started'
        );
    }

    public function complete(PickList $pickList): JsonResponse
    {
        if ($pickList->status !== 'in_progress') {
            return $this->error('Only in-progress pick lists can be completed', 422);
        }

        $allPicked = $pickList->items()->where('status', '!=', 'picked')->count() === 0;

        if (!$allPicked) {
            return $this->error('Not all items have been picked', 422);
        }

        $pickList->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $this->success(
            $pickList->fresh(),
            'Pick list completed'
        );
    }

    public function pickItem(Request $request, PickList $pickList, PickListItem $item): JsonResponse
    {
        if ($item->pick_list_id !== $pickList->id) {
            abort(403, 'Item does not belong to this pick list');
        }

        $validated = $request->validate([
            'quantity_picked' => 'required|integer|min:1',
            'bin_location' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $item = $this->pickItemAction->execute($item, $validated);

        return $this->success($item->fresh()->load('product'), 'Item picked successfully');
    }

    public function items(PickList $pickList): JsonResponse
    {
        return $this->success($pickList->items()->with('product')->get());
    }
}
