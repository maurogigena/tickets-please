<?php

namespace App\Http\Filters\V1;

class TicketFilter extends QueryFilter
{
    // this TicketFilter class filters, sorts and includes relationships in database queries using Eloquent automatically, based in request parameters
    // it's a clean and reusable way to manage filters from the frontend without adding extra logic to the controller 
    // example: GET /api/v1/tickets?status=open&createdAt=2024-05-01,2024-05-15&title=*bug*

    // Sort relationship
    protected $sortable = [
        'title',
        'status',
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

    // Status filter
    public function status($value)
    {
        return $this->builder->whereIn('status', explode(',', $value));
    }

    // Title filter
    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeStr);
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