<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PickList;
use App\Models\PickingPacking\PickListItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PickListController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PickList::where('company_id', $request->user()->company_id)
            ->with(['warehouse', 'order', 'picker']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('pick_list_number', 'like', "%{$search}%");
        }

        $pickLists = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('PickingPacking/PickLists', [
            'pickLists' => $pickLists,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing'])
            ->with('customer')
            ->get();

        return Inertia::render('PickingPacking/CreatePickList', [
            'warehouses' => $warehouses,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_id' => 'required|exists:orders,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_to_pick' => 'required|numeric|min:0.01',
        ]);

        $companyId = $request->user()->company_id;

        $pickList = PickList::create([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'order_id' => $validated['order_id'],
            'status' => 'pending',
            'picker_id' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            PickListItem::create([
                'pick_list_id' => $pickList->id,
                'order_id' => $validated['order_id'],
                'order_item_id' => $item['order_item_id'],
                'product_id' => $item['product_id'],
                'quantity_to_pick' => $item['quantity_to_pick'],
                'quantity_picked' => 0,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('pick-lists.index')->with('success', 'Pick list created successfully');
    }

    public function show(Request $request, PickList $pickList): Response
    {
        $pickList->load([
            'warehouse',
            'order.customer',
            'items' => fn ($q) => $q->with(['product', 'orderItem']),
            'picker',
        ]);

        return Inertia::render('PickingPacking/Show', [
            'pickList' => $pickList,
        ]);
    }

    public function update(Request $request, PickList $pickList): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.id' => 'required_with:items|exists:pick_list_items,id',
            'items.*.quantity_picked' => 'nullable|numeric|min:0',
            'items.*.status' => 'nullable|string|max:50',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($request->filled('status')) {
            $updateData = ['status' => $validated['status']];
            if ($validated['status'] === 'in_progress' && is_null($pickList->started_at)) {
                $updateData['started_at'] = now();
            }
            if ($validated['status'] === 'completed') {
                $updateData['completed_at'] = now();
            }
            $pickList->update($updateData);
        }

        if ($request->filled('notes')) {
            $pickList->update(['notes' => $validated['notes']]);
        }

        if ($request->filled('items')) {
            foreach ($validated['items'] as $item) {
                PickListItem::where('id', $item['id'])->update(
                    array_filter([
                        'quantity_picked' => $item['quantity_picked'] ?? null,
                        'status' => $item['status'] ?? null,
                        'notes' => $item['notes'] ?? null,
                        'picked_at' => ($item['status'] ?? null) === 'picked' ? now() : null,
                    ], fn ($v) => $v !== null)
                );
            }
        }

        return redirect()->route('pick-lists.show', $pickList)->with('success', 'Pick list updated successfully');
    }
}
