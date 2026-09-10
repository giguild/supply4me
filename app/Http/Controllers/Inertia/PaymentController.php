<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use App\Models\Suppliers\Supplier;
use App\Services\NotificationService;
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
            'payment_type' => 'nullable|string|max:50',
            'payment_method' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'nullable|string|max:3',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date',
            'receipt' => 'required|file|image|max:5120',
        ]);

        $companyId = $request->user()->company_id;

        $validated['company_id'] = $companyId;
        $validated['status'] = 'pending';
        $validated['received_by'] = $request->user()->id;

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('payment-receipts', 'public');
        }

        unset($validated['receipt']);

        $payment = Payment::create($validated);

        if ($request->filled('invoice_id')) {
            PaymentAllocation::create([
                'payment_id' => $payment->id,
                'invoice_id' => $validated['invoice_id'],
                'amount' => $validated['amount'],
            ]);
        }

        app(NotificationService::class)->paymentReceived($payment);

        return redirect()->route('payments.index')->with('success', 'Payment created successfully');
    }

    public function show(Request $request, Payment $payment): Response
    {
        $payment->load([
            'customer',
            'supplier',
            'allocations.invoice.payments',
            'approvedBy',
            'receivedBy',
            'branch',
        ]);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function download(Request $request, Payment $payment): \Symfony\Component\HttpFoundation\Response
    {
        $payment->load(['customer', 'supplier', 'allocations.invoice', 'approvedBy', 'receivedBy', 'company', 'branch']);

        $filename = preg_replace('/[^A-Za-z0-9\-_.]/', '-', $payment->payment_number) . '.pdf';

        return Pdf::loadView('payments.receipt', ['payment' => $payment])
            ->setPaper('a4', 'portrait')
            ->download($filename);
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
            'payment_type' => 'nullable|string|max:50',
            'payment_method' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0.01',
            'currency_code' => 'nullable|string|max:3',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date',
            'receipt' => 'nullable|file|image|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('payment-receipts', 'public');
        }

        unset($validated['receipt']);

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
            'approved_at' => now(),
        ]);

        $this->syncInvoicePayment($payment);

        $this->sendReceiptEmail($payment);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment approved — invoice updated');
    }

    public function markPartial(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
        ]);

        $payment->update([
            'status' => 'completed',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'amount' => $request->paid_amount,
            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . "Partial payment of ₦" . number_format($request->paid_amount, 2) . " approved by admin",
        ]);

        $this->syncInvoicePayment($payment);

        $this->sendReceiptEmail($payment);

        return redirect()->route('payments.show', $payment)->with('success', 'Partial payment recorded — invoice updated');
    }

    private function syncInvoicePayment(Payment $payment): void
    {
        $allocation = $payment->allocations()->first();
        if (!$allocation) return;

        $invoice = $allocation->invoice;
        if (!$invoice) return;

        $totalPaid = $invoice->payments()
            ->where('status', 'completed')
            ->sum('payments.amount');

        $invoice->update([
            'paid_amount' => $totalPaid,
            'due_amount' => $invoice->total_amount - $totalPaid,
            'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'partial',
        ]);

        if ($invoice->order) {
            $invoice->order->update([
                'payment_status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'partial',
            ]);
        }
    }

    private function sendReceiptEmail(Payment $payment): void
    {
        $payment->load('customer');

        if ($payment->customer && $payment->customer->email) {
            \Illuminate\Support\Facades\Mail::to($payment->customer->email)->send(new \App\Mail\PaymentReceiptMail($payment));
        }
    }

    public function reject(Request $request, Payment $payment): \Illuminate\Http\RedirectResponse
    {
        $payment->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
        ]);

        $this->syncInvoicePayment($payment);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment rejected');
    }
}
