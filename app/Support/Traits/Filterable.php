<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply filters from request query parameters.
     * Usage: Product::filter(request()->all())->get()
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_null($value) || $value === '') continue;

            match(true) {
                method_exists($this, 'filter' . ucfirst($field))
                    => $this->{'filter' . ucfirst($field)}($query, $value),
                in_array($field, $this->filterable ?? [])
                    => $query->where($field, $value),
                default => null,
            };
        }

        return $query;
    }
}
