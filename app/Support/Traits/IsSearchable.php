<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsSearchable
{
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $columns = $this->searchable ?? [];

        if (empty($columns)) {
            return $query;
        }

        $query->where(function (Builder $q) use ($search, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', "%{$search}%");
            }
        });

        return $query;
    }
}
