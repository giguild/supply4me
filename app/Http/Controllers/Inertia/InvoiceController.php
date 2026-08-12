<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\InvoiceItem;
use App\Models\Invoicing\InvoiceStatusHistory;
use App\Models\Orders\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Invoice::where('company_id', $request->user()->company_id)
            ->with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $orders = Order::where('company_id', $request->user()->company_id)
            ->whereNotIn('status', ['cancelled'])
            ->with('customer')
            ->get();

        return Inertia::render('Invoices/Create', [
            'customers' => $customers,
            'orders' => $orders,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'type' => 'nullable|string|max:50',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $companyId = $request->user()->company_id;

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;
            $subtotal += $lineTotal;
            $discountAmount += $itemDiscount;
        }

        $invoice = Invoice::create([
            'company_id' => $companyId,
            'customer_id' => $validated['customer_id'],
            'order_id' => $validated['order_id'] ?? null,
            'type' => $validated['type'] ?? 'standard',
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $subtotal - $discountAmount + $taxAmount,
            'amount_paid' => 0,
            'balance_due' => $subtotal - $discountAmount + $taxAmount,
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;

            $product = \App\Models\Products\Product::find($item['product_id']);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'tax_amount' => 0,
                'total_amount' => $lineTotal,
            ]);
        }

        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'status' => 'draft',
            'previous_status' => null,
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
    }

    public function show(Request $request, Invoice $invoice): Response
    {
        $invoice->load([
            'customer',
            'order',
            'items.product',
            'items.unit',
            'payments',
            'statusHistory' => fn ($q) => $q->with('performedBy')->latest(),
            'createdBy',
        ]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Request $request, Invoice $invoice): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $orders = Order::where('company_id', $request->user()->company_id)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $invoice->load('items.product');

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'customers' => $customers,
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, Invoice $invoice): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'type' => 'nullable|string|max:50',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $subtotal = 0;
        $taxAmount = 0;
        $discountAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;
            $subtotal += $lineTotal;
            $discountAmount += $itemDiscount;
        }

        $total = $subtotal - $discountAmount + $taxAmount;

        $invoice->update([
            'customer_id' => $validated['customer_id'],
            'order_id' => $validated['order_id'] ?? $invoice->order_id,
            'type' => $validated['type'] ?? $invoice->type,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $total,
            'balance_due' => $total - $invoice->amount_paid,
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'] ?? $invoice->notes,
            'terms' => $validated['terms'] ?? $invoice->terms,
        ]);

        $invoice->items()->delete();

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $itemDiscount = $lineTotal * (($item['discount_percentage'] ?? 0) / 100);
            $lineTotal -= $itemDiscount;

            $product = \App\Models\Products\Product::find($item['product_id']);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'sku' => $product->sku,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'tax_amount' => 0,
                'total_amount' => $lineTotal,
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully');
    }

    public function destroy(Request $request, Invoice $invoice): \Illuminate\Http\RedirectResponse
    {
        $invoice->items()->delete();
        $invoice->statusHistory()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function send(Request $request, Invoice $invoice): \Illuminate\Http\RedirectResponse
    {
        $previousStatus = $invoice->status->value;
        $invoice->update(['status' => 'sent']);

        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'status' => 'sent',
            'previous_status' => $previousStatus,
            'notes' => $request->get('notes'),
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice sent successfully');
    }

    public function void(Request $request, Invoice $invoice): \Illuminate\Http\RedirectResponse
    {
        $previousStatus = $invoice->status->value;
        $invoice->update(['status' => 'voided']);

        InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'status' => 'voided',
            'previous_status' => $previousStatus,
            'notes' => $request->get('notes'),
            'performed_by' => $request->user()->id,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice voided successfully');
    }
}
