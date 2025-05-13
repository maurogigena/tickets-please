<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    // in case that create a new ticket, only can save the fields in the array
    protected $fillable = ['title','description','status','user_id'];
    
    // SENSITIVE DATA - SECURITY LARACAST COURSE
    // example: there's a unique sensitive key attribute on each ticket, so to protect that data, just implements the code below:
    // protected $hidden = ['key'];
    // but this code is unusefull because the most safety way to protect data is create a resource response like TicketResource.

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // get the filtered request -apply the defined filters to the request
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }
}
