<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait IsFilterable
{
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }

            $method = 'filter' . str_replace('_', '', ucwords($field, '_'));

            if (method_exists($this, $method)) {
                $this->{$method}($query, $value);
            } elseif (in_array($field, $this->filterable ?? [])) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
}
