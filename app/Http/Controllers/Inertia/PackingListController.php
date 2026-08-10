<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PackingList;
use App\Models\PickingPacking\PackingListItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackingListController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PackingList::where('company_id', $request->user()->company_id)
            ->with(['warehouse', 'order', 'packer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('packing_list_number', 'like', "%{$search}%");
        }

        $packingLists = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('PickingPacking/PackingLists', [
            'packingLists' => $packingLists,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'picking'])
            ->with('customer')
            ->get();

        return Inertia::render('PickingPacking/CreatePackingList', [
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
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $companyId = $request->user()->company_id;

        $packingList = PackingList::create([
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'order_id' => $validated['order_id'],
            'status' => 'pending',
            'packer_id' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            PackingListItem::create([
                'packing_list_id' => $packingList->id,
                'order_item_id' => $item['order_item_id'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('packing-lists.index')->with('success', 'Packing list created successfully');
    }

    public function show(Request $request, PackingList $packingList): Response
    {
        $packingList->load([
            'warehouse',
            'order.customer',
            'items' => fn ($q) => $q->with(['product', 'orderItem']),
            'packer',
        ]);

        return Inertia::render('PickingPacking/Show', [
            'packingList' => $packingList,
        ]);
    }

    public function update(Request $request, PackingList $packingList): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('status')) {
            $updateData = ['status' => $validated['status']];
            if ($validated['status'] === 'in_progress' && is_null($packingList->started_at)) {
                $updateData['started_at'] = now();
            }
            if ($validated['status'] === 'completed') {
                $updateData['completed_at'] = now();
            }
            $packingList->update($updateData);
        }

        if ($request->filled('notes')) {
            $packingList->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('packing-lists.show', $packingList)->with('success', 'Packing list updated successfully');
    }
}
