<?php

namespace App\Http\Controllers\Api\V1\Customers;

use App\Actions\Customers\CreateCustomerAction;
use App\Actions\Customers\UpdateCustomerAction;
use App\Actions\Customers\UpdateCreditStatusAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use App\Models\Customers\Customer;
use App\Resources\Customers\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CreateCustomerAction $createCustomerAction,
        protected UpdateCustomerAction $updateCustomerAction,
        protected UpdateCreditStatusAction $updateCreditStatusAction
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->withCount(['contacts', 'orders'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->credit_status, fn ($q, $s) => $q->where('credit_status', $s))
            ->paginate($request->get('per_page', 15));

        return $this->paginated($customers, CustomerResource::collection($customers->items()));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->createCustomerAction->execute($request->validated());

        return $this->created(
            new CustomerResource($customer),
            'Customer created successfully'
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->success(
            new CustomerResource($customer->load(['contacts', 'addresses', 'orders' => fn ($q) => $q->latest()->limit(5)]))
        );
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->updateCustomerAction->execute($customer, $request->validated());

        return $this->success(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->noContent('Customer deleted successfully');
    }

    public function creditStatus(Customer $customer): JsonResponse
    {
        return $this->success([
            'credit_limit' => $customer->credit_limit,
            'credit_used' => $customer->credit_used,
            'credit_available' => $customer->credit_limit - $customer->credit_used,
            'credit_status' => $customer->credit_status,
        ]);
    }

    public function updateCreditStatus(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'credit_status' => 'required|string|in:active,suspended,blocked',
        ]);

        $customer = $this->updateCreditStatusAction->execute($customer, $validated);

        return $this->success(
            new CustomerResource($customer),
            'Credit status updated successfully'
        );
    }

    public function notes(Customer $customer): JsonResponse
    {
        return $this->success($customer->notes()->latest()->get());
    }

    public function addNote(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'type' => 'sometimes|string|in:general,important,warning',
        ]);

        $note = $customer->notes()->create([
            'note' => $validated['note'],
            'type' => $validated['type'] ?? 'general',
            'user_id' => auth()->id(),
        ]);

        return $this->created($note, 'Note added successfully');
    }

    public function deleteNote(Customer $customer, int $noteId): JsonResponse
    {
        $customer->notes()->where('id', $noteId)->delete();

        return $this->noContent('Note deleted successfully');
    }
}
