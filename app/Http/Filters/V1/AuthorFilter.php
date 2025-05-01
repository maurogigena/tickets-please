<?php

namespace App\Http\Filters\V1;

class AuthorFilter extends QueryFilter
{
    // Sort relationship
    protected $sortable = [
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    // createdAt filter
    public function createdAt($value)
    {
        $dates = explode (',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }

    // Include
    public function include ($value)
    {
        return $this->builder->with($value);
    }

    // Id filter
    public function id($value)
    {
        return $this->builder->whereIn('id', explode(',', $value));
    }

    // Title filter
    public function email($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('email', 'like', $likeStr);
    }

    // Email filter
    public function name($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('name', 'like', $likeStr);
    }

    // updatedAt Filter
    public function updatedAt($value)
    {
        $dates = explode (',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }
}