<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use App\Models\Suppliers\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Payment::where('company_id', $request->user()->company_id)
            ->with(['customer', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate($request->get('per_page', 15));

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(Request $request): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $suppliers = Supplier::where('company_id', $request->user()->company_id)->get();
        $invoices = Invoice::where('company_id', $request->user()->company_id)
            ->whereNotIn('status', ['paid', 'voided'])
            ->with('customer')
            ->get();

        return Inertia::render('Payments/Create', [
            'customers' => $customers,
            'suppliers' => $suppliers,
            'invoices' => $invoices,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'type' => 'nullable|string|max:50',
            'method' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'nullable|string|max:3',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date',
        ]);

        $companyId = $request->user()->company_id;

        $validated['company_id'] = $companyId;
        $validated['status'] = 'pending';
        $validated['received_by'] = $request->user()->id;

        $payment = Payment::create($validated);

        if ($request->filled('invoice_id')) {
            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'invoice_id' => $validated['invoice_id'],
                'amount' => $validated['amount'],
            ]);
        }

        return redirect()->route('payments.index')->with('success', 'Payment created successfully');
    }

    public function show(Request $request, Payment $payment): Response
    {
        $payment->load([
            'customer',
            'supplier',
            'allocations.invoice',
            'approvedBy',
            'receivedBy',
            'branch',
        ]);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function edit(Request $request, Payment $payment): Response
    {
        $customers = Customer::where('company_id', $request->user()->company_id)->get();
        $suppliers = Supplier::where('company_id', $request->user()->company_id)->get();
        $invoices = Invoice::where('company_id', $request->user()->company_id)
            ->whereNotIn('status', ['paid', 'voided'])
            ->get();

        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'invoices' => $invoices,
        ]);
    }

    public function update(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'type' => 'nullable|string|max:50',
            'method' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'nullable|string|max:3',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date',
        ]);

        $payment->update($validated);

        $payment->allocations()->delete();

        if ($request->filled('invoice_id')) {
            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'invoice_id' => $validated['invoice_id'],
                'amount' => $validated['amount'],
            ]);
        }

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $payment->allocations()->delete();
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully');
    }

    public function approve(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $payment->update([
            'status' => 'completed',
            'approved_by' => $request->user()->id,
            'cleared_date' => now()->toDateString(),
        ]);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment approved successfully');
    }

    public function reject(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $payment->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment rejected successfully');
    }
}
