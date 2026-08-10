<?php

namespace App\Repositories\Eloquent;

use App\Models\Suppliers\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function __construct(
        protected Supplier $model,
    ) {}

    public function create(array $data): Supplier
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Supplier
    {
        return $this->model->find($id);
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Supplier
    {
        $supplier = $this->findById($id);
        $supplier->update($data);
        return $supplier->fresh();
    }

    public function delete(string $id): bool
    {
        $supplier = $this->findById($id);
        return $supplier->delete();
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

        if (! empty($filters['supplier_number'])) {
            $query->where('supplier_number', 'like', "%{$filters['supplier_number']}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['rating'])) {
            $query->where('rating', '>=', $filters['rating']);
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }
}
