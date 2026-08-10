<?php

namespace App\Repositories\Eloquent;

use App\Enums\Orders\OrderStatus;
use App\Models\Orders\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected Order $model,
    ) {}

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Order
    {
        return $this->model->with(['items', 'customer', 'branch', 'warehouse'])->find($id);
    }

    public function findByNumber(string $orderNumber): ?Order
    {
        return $this->model->where('order_number', $orderNumber)->first();
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Order
    {
        $order = $this->findById($id);
        $order->update($data);
        return $order->fresh();
    }

    public function delete(string $id): bool
    {
        $order = $this->findById($id);
        return $order->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['customer', 'branch', 'assignedTo']);

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['order_number'])) {
            $query->where('order_number', 'like', "%{$filters['order_number']}%");
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['fulfillment_status'])) {
            $query->where('fulfillment_status', $filters['fulfillment_status']);
        }

        if (! empty($filters['order_type'])) {
            $query->where('order_type', $filters['order_type']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (! empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (! empty($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }

        if (! empty($filters['min_total'])) {
            $query->where('total_amount', '>=', $filters['min_total']);
        }

        if (! empty($filters['max_total'])) {
            $query->where('total_amount', '<=', $filters['max_total']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByCustomer(string $customerId): Collection
    {
        return $this->model->where('customer_id', $customerId)->latest()->get();
    }

    public function findByStatus(OrderStatus $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }
}
