<?php

namespace App\Http\Controllers\Inertia;

use App\Enums\Orders\OrderStatus;
use App\Enums\PickingPacking\PackingStatus;
use App\Enums\PickingPacking\PickListStatus;
use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\PickingPacking\PackingList;
use App\Models\PickingPacking\PackingListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PackingListController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PackingList::where('company_id', $request->user()->company_id)
            ->with(['order', 'warehouse', 'packer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('packing_list_number', 'like', "%{$search}%");
        }

        $packingLists = $query->latest()->paginate($request->get('per_page', 15));

        $stats = [
            'ready_to_pack' => Order::where('company_id', $request->user()->company_id)
                ->whereIn('status', ['confirmed', 'processing', 'picking'])
                ->whereDoesntHave('packingList')
                ->whereHas('pickList', fn ($q) => $q->where('status', 'completed'))
                ->count(),
            'pending_lists' => PackingList::where('company_id', $request->user()->company_id)
                ->whereIn('status', [PackingStatus::Draft->value, PackingStatus::InProgress->value])
                ->count(),
            'packed_lists' => PackingList::where('company_id', $request->user()->company_id)
                ->where('status', PackingStatus::Packed->value)
                ->count(),
            'verified_lists' => PackingList::where('company_id', $request->user()->company_id)
                ->where('status', PackingStatus::Verified->value)
                ->count(),
        ];

        return Inertia::render('PackingLists/Index', [
            'packingLists' => $packingLists,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request): Response
    {
        $companyId = $request->user()->company_id;

        $orders = Order::where('company_id', $companyId)
            ->whereIn('status', ['confirmed', 'processing', 'picking'])
            ->whereDoesntHave('packingList')
            ->whereHas('pickList', fn ($q) => $q->where('status', 'completed'))
            ->with('customer')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('PackingLists/Create', [
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items.product', 'pickList')
            ->where('company_id', $request->user()->company_id)
            ->findOrFail($validated['order_id']);

        if (! in_array($order->status->value, ['confirmed', 'processing', 'picking'], true)) {
            return back()->with('error', 'Only confirmed, processing or picking orders can be packed.');
        }

        $pickList = $order->pickList;

        if ($pickList?->status->value !== PickListStatus::Completed->value) {
            return back()->with('error', 'This order must be picked before it can be packed.');
        }

        if (PackingList::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'This order already has a packing list.');
        }

        $packingList = PackingList::create([
            'company_id' => $order->company_id,
            'order_id' => $order->id,
            'pick_list_id' => $pickList?->id,
            'warehouse_id' => $order->warehouse_id
                ?? \App\Models\Inventory\Warehouse::where('company_id', $order->company_id)->value('id'),
            'status' => PackingStatus::InProgress,
            'packer_id' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($order->items as $orderItem) {
            PackingListItem::create([
                'packing_list_id' => $packingList->id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'variant_id' => $orderItem->variant_id,
                'quantity' => $orderItem->quantity,
                'weight' => $orderItem->product?->weight,
                'dimensions' => $orderItem->product?->dimensions,
            ]);
        }

        $order->update(['status' => OrderStatus::Packing]);

        return redirect()->route('packing-lists.show', $packingList)->with('success', 'Packing list created successfully');
    }

    public function show(Request $request, PackingList $packingList): Response
    {
        $packingList->load([
            'order.customer',
            'warehouse',
            'packer',
            'items' => fn ($q) => $q->with(['product', 'orderItem']),
        ]);

        return Inertia::render('PackingLists/Show', [
            'packingList' => $packingList,
        ]);
    }

    public function pack(Request $request, PackingList $packingList): RedirectResponse
    {
        if ($packingList->status->value !== PackingStatus::InProgress->value) {
            return back()->with('error', 'Only in-progress packing lists can be marked as packed.');
        }

        $packingList->update([
            'status' => PackingStatus::Packed,
            'packed_at' => now(),
            'completed_at' => now(),
        ]);

        $packingList->order()->update(['status' => OrderStatus::ReadyToShip]);

        return back()->with('success', 'Packing list marked as packed');
    }

    public function verify(Request $request, PackingList $packingList): RedirectResponse
    {
        if ($packingList->status->value !== PackingStatus::Packed->value) {
            return back()->with('error', 'Only packed packing lists can be verified.');
        }

        $packingList->update([
            'status' => PackingStatus::Verified,
        ]);

        return back()->with('success', 'Packing list verified');
    }

    public function update(Request $request, PackingList $packingList): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->filled('notes')) {
            $packingList->update(['notes' => $validated['notes']]);
        }

        return back()->with('success', 'Packing list updated');
    }
}