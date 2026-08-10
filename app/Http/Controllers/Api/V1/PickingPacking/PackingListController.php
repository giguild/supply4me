<?php

namespace App\Http\Controllers\Api\V1\PickingPacking;

use App\Actions\PickingPacking\PackOrderAction;
use App\Actions\PickingPacking\VerifyPackingAction;
use App\Http\Controllers\Controller;
use App\Models\PickingPacking\PackingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackingListController extends Controller
{
    public function __construct(
        protected PackOrderAction $packOrderAction,
        protected VerifyPackingAction $verifyPackingAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $packingLists = PackingList::query()
            ->with(['order', 'pickList', 'items.product'])
            ->when($request->search, fn ($q, $s) => $q->where('packing_list_number', 'like', "%{$s}%"))
            ->when($request->order_id, fn ($q, $o) => $q->where('order_id', $o))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Packing lists retrieved successfully',
            'data' => $packingLists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pick_list_id' => 'required|exists:pick_lists,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $packingList = $this->packOrderAction->execute($validated);

        return $this->created(
            $packingList->load(['order', 'pickList', 'items.product']),
            'Packing list created successfully'
        );
    }

    public function show(PackingList $packingList): JsonResponse
    {
        return $this->success(
            $packingList->load(['order', 'pickList', 'items.product', 'packer'])
        );
    }

    public function pack(Request $request, PackingList $packingList): JsonResponse
    {
        if ($packingList->status !== 'pending') {
            return $this->error('Only pending packing lists can be packed', 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.packing_list_item_id' => 'required|exists:packing_list_items,id',
            'items.*.quantity_packed' => 'required|integer|min:1',
            'items.*.package_number' => 'nullable|string|max:100',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $packingList->update([
            'status' => 'packing',
            'packer_id' => auth()->id(),
            'packed_at' => now(),
        ]);

        return $this->success(
            $packingList->fresh(),
            'Items packed successfully'
        );
    }

    public function verify(Request $request, PackingList $packingList): JsonResponse
    {
        if ($packingList->status !== 'packing') {
            return $this->error('Only packing lists in packing status can be verified', 422);
        }

        $validated = $request->validate([
            'verified' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $packingList = $this->verifyPackingAction->execute($packingList, $validated);

        return $this->success(
            $packingList->fresh(),
            $validated['verified'] ? 'Packing verified successfully' : 'Packing verification failed'
        );
    }

    public function items(PackingList $packingList): JsonResponse
    {
        return $this->success($packingList->items()->with('product')->get());
    }
}
