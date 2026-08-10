<?php

namespace App\Repositories\Eloquent;

use App\Enums\Delivery\DeliveryStatus;
use App\Models\Delivery\Delivery;
use App\Repositories\Contracts\DeliveryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentDeliveryRepository implements DeliveryRepositoryInterface
{
    public function __construct(
        protected Delivery $model,
    ) {}

    public function create(array $data): Delivery
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Delivery
    {
        return $this->model->with(['order', 'shipment', 'driver', 'customer', 'items'])->find($id);
    }

    public function findByNumber(string $deliveryNumber): ?Delivery
    {
        return $this->model->where('delivery_number', $deliveryNumber)->first();
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Delivery
    {
        $delivery = $this->findById($id);
        $delivery->update($data);
        return $delivery->fresh();
    }

    public function delete(string $id): bool
    {
        $delivery = $this->findById($id);
        return $delivery->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['order', 'driver', 'customer']);

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['delivery_number'])) {
            $query->where('delivery_number', 'like', "%{$filters['delivery_number']}%");
        }

        if (! empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (! empty($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['scheduled_date_from'])) {
            $query->where('scheduled_date', '>=', $filters['scheduled_date_from']);
        }

        if (! empty($filters['scheduled_date_to'])) {
            $query->where('scheduled_date', '<=', $filters['scheduled_date_to']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('delivery_number', 'like', "%{$filters['search']}%")
                    ->orWhere('delivery_notes', 'like', "%{$filters['search']}%");
            });
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByDriver(string $driverId): Collection
    {
        return $this->model->where('driver_id', $driverId)->latest()->get();
    }

    public function findByStatus(DeliveryStatus $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }
}
