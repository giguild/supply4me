<?php

namespace App\Http\Controllers\Api\V1\Invoices;

use App\Actions\Invoicing\GenerateInvoiceAction;
use App\Actions\Invoicing\SendInvoiceAction;
use App\Actions\Invoicing\VoidInvoiceAction;
use App\Http\Controllers\Controller;
use App\Models\Invoicing\Invoice;
use App\Resources\Invoicing\InvoiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected GenerateInvoiceAction $generateInvoiceAction,
        protected SendInvoiceAction $sendInvoiceAction,
        protected VoidInvoiceAction $voidInvoiceAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::query()
            ->with(['customer', 'items'])
            ->when($request->search, fn ($q, $s) => $q->where('invoice_number', 'like', "%{$s}%"))
            ->when($request->customer_id, fn ($q, $c) => $q->where('customer_id', $c))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->where('invoice_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('invoice_date', '<=', $d))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->paginated($invoices, InvoiceResource::collection($invoices->items()));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $invoice = $this->generateInvoiceAction->execute($validated);

        return $this->created(
            new InvoiceResource($invoice->load(['customer', 'items'])),
            'Invoice generated successfully'
        );
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return $this->success(
            new InvoiceResource($invoice->load(['customer', 'items.product', 'payments', 'order']))
        );
    }

    public function send(Invoice $invoice): JsonResponse
    {
        $invoice = $this->sendInvoiceAction->execute($invoice);

        return $this->success(
            new InvoiceResource($invoice->fresh()),
            'Invoice sent successfully'
        );
    }

    public function void(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $invoice = $this->voidInvoiceAction->execute($invoice, $validated['reason']);

        return $this->success(
            new InvoiceResource($invoice->fresh()),
            'Invoice voided successfully'
        );
    }

    public function items(Invoice $invoice): JsonResponse
    {
        return $this->success(
            $invoice->items()->with('product')->get()
        );
    }

    public function payments(Invoice $invoice): JsonResponse
    {
        return $this->success(
            $invoice->payments()->latest()->get()
        );
    }
}
