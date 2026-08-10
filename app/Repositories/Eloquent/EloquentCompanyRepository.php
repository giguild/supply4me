<?php

namespace App\Repositories\Eloquent;

use App\Models\Companies\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentCompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(
        protected Company $model,
    ) {}

    public function create(array $data): Company
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Company
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Company
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function update(string $id, array $data): Company
    {
        $company = $this->findById($id);
        $company->update($data);
        return $company->fresh();
    }

    public function delete(string $id): bool
    {
        $company = $this->findById($id);
        return $company->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (! empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (! empty($filters['currency_code'])) {
            $query->where('currency_code', $filters['currency_code']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }
}
