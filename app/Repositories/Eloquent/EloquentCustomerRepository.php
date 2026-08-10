<?php

namespace App\Repositories\Eloquent;

use App\Models\Customers\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(
        protected Customer $model,
    ) {}

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Customer
    {
        return $this->model->find($id);
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function findByNumber(string $customerNumber): ?Customer
    {
        return $this->model->where('customer_number', $customerNumber)->first();
    }

    public function update(string $id, array $data): Customer
    {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(string $id): bool
    {
        $customer = $this->findById($id);
        return $customer->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (! empty($filters['customer_number'])) {
            $query->where('customer_number', 'like', "%{$filters['customer_number']}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['credit_status'])) {
            $query->where('credit_status', $filters['credit_status']);
        }

        if (! empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', 'like', "%{$filters['city']}%");
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByAssignedTo(string $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)->get();
    }
}
