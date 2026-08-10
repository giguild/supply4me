<?php

namespace App\Repositories\Eloquent;

use App\Models\Products\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        protected Product $model,
    ) {}

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Product
    {
        return $this->model->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(string $id): bool
    {
        $product = $this->findById($id);
        return $product->delete();
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

        if (! empty($filters['sku'])) {
            $query->where('sku', 'like', "%{$filters['sku']}%");
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['is_sellable'])) {
            $query->where('is_sellable', $filters['is_sellable']);
        }

        if (isset($filters['is_purchasable'])) {
            $query->where('is_purchasable', $filters['is_purchasable']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('sku', 'like', "%{$filters['search']}%")
                    ->orWhere('barcode', 'like', "%{$filters['search']}%");
            });
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByCategory(string $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)->get();
    }
}
