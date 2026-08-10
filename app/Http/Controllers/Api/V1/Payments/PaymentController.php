<?php

namespace App\Http\Controllers\Api\V1\Payments;

use App\Actions\Payments\CreatePaymentAction;
use App\Actions\Payments\ApprovePaymentAction;
use App\Actions\Payments\RejectPaymentAction;
use App\Actions\Payments\AllocatePaymentAction;
use App\Actions\Payments\RefundPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use App\Resources\Payments\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected CreatePaymentAction $createPaymentAction,
        protected ApprovePaymentAction $approvePaymentAction,
        protected RejectPaymentAction $rejectPaymentAction,
        protected AllocatePaymentAction $allocatePaymentAction,
        protected RefundPaymentAction $refundPaymentAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->with(['customer', 'allocations'])
            ->when($request->search, fn ($q, $s) => $q->where('payment_number', 'like', "%{$s}%"))
            ->when($request->customer_id, fn ($q, $c) => $q->where('customer_id', $c))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->method, fn ($q, $m) => $q->where('payment_method', $m))
            ->when($request->date_from, fn ($q, $d) => $q->where('payment_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('payment_date', '<=', $d))
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->paginated($payments, PaymentResource::collection($payments->items()));
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->createPaymentAction->execute($request->validated());

        return $this->created(
            new PaymentResource($payment->load('customer')),
            'Payment created successfully'
        );
    }

    public function show(Payment $payment): JsonResponse
    {
        return $this->success(
            new PaymentResource($payment->load(['customer', 'allocations.invoice']))
        );
    }

    public function approve(Payment $payment): JsonResponse
    {
        $payment = $this->approvePaymentAction->execute($payment);

        return $this->success(
            new PaymentResource($payment->fresh()),
            'Payment approved successfully'
        );
    }

    public function reject(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment = $this->rejectPaymentAction->execute($payment, $validated['reason']);

        return $this->success(
            new PaymentResource($payment->fresh()),
            'Payment rejected successfully'
        );
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        $payment = $this->refundPaymentAction->execute($payment, $validated);

        return $this->success(
            new PaymentResource($payment->fresh()),
            'Payment refunded successfully'
        );
    }

    public function allocate(Request $request, Payment $payment): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $allocation = $this->allocatePaymentAction->execute($payment, $validated);

        return $this->created(
            $allocation->load('invoice'),
            'Payment allocated successfully'
        );
    }

    public function allocations(Payment $payment): JsonResponse
    {
        return $this->success(
            $payment->allocations()->with('invoice')->get()
        );
    }
}
