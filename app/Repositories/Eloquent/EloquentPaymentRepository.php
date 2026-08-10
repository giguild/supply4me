<?php

namespace App\Repositories\Eloquent;

use App\Enums\Payments\PaymentStatus;
use App\Models\Payments\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function __construct(
        protected Payment $model,
    ) {}

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Payment
    {
        return $this->model->with(['customer', 'supplier', 'allocations'])->find($id);
    }

    public function findByNumber(string $paymentNumber): ?Payment
    {
        return $this->model->where('payment_number', $paymentNumber)->first();
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Payment
    {
        $payment = $this->findById($id);
        $payment->update($data);
        return $payment->fresh();
    }

    public function delete(string $id): bool
    {
        $payment = $this->findById($id);
        return $payment->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['customer', 'supplier']);

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['payment_number'])) {
            $query->where('payment_number', 'like', "%{$filters['payment_number']}%");
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['reference'])) {
            $query->where('reference', 'like', "%{$filters['reference']}%");
        }

        if (! empty($filters['date_from'])) {
            $query->where('payment_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('payment_date', '<=', $filters['date_to']);
        }

        if (! empty($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (! empty($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByCustomer(string $customerId): Collection
    {
        return $this->model->where('customer_id', $customerId)->latest()->get();
    }

    public function findByStatus(PaymentStatus $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }
}
