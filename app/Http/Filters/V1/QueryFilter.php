<?php

namespace App\Http\Filters\V1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected $builder;
    protected $request;
    protected $sortable = [];

    
    // this allows the client to access to all params of the URL, like ?filter[status]=A&sort=createdAt
    public function __construct(Request $request) {
        $this->request = $request;
    }


    // call and pass the value if the request have params that match with methods within the class
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        // run for all params of the URL - $key=sort/status/include - $value=A/X/createdAt/-title
        foreach($this->request->all() as $key => $value)
        {
            // if the method (as 'sort') exists, call it and pass it the $value -as sort=title
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }


    // similar than apply() but apply() take the filters of the request, and filter() take the filters of an array
    protected function filter($arr)
    {
        foreach ($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    } 


    protected function sort($value)
    {
        // separate the string value in commas, and then save each one into an array called $sortAttributes
        $sortAttributes = explode(',', $value);

        foreach ($sortAttributes as $sortAttribute) {
            $direction = 'asc';

            // if the string start with '-' at the pos 0, change the direction to 'desc' - then separate the - to the string and read the attribute
            if(strpos($sortAttribute, '-') === 0) {
                $direction = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }

            // Security - check if this field is sortable
            if (!in_array($sortAttribute, $this->sortable) && !array_key_exists($sortAttribute, $this->sortable)) {
                continue;
            }

            // Translate the Alias - =createdAt -> created_at
            $columnName = $this->sortable[$sortAttribute] ?? null;

            if($columnName === null) {
                $columnName = $sortAttribute;
            }

            // Sort the results, through this field, in the chosen direction
            $this->builder->orderBy($columnName, $direction);
        }
    }
}