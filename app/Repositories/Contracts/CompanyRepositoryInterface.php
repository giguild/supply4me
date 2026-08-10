<?php

namespace App\Repositories\Contracts;

use App\Models\Companies\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function create(array $data): Company;

    public function findById(string $id): ?Company;

    public function findBySlug(string $slug): ?Company;

    public function update(string $id, array $data): Company;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
