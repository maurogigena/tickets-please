<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\v1\TicketResource;
use App\Policies\V1\TicketPolicy;

class TicketController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

    /**
     * Get all tickets
     * 
     * @group Manging Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a ticket
     * 
     * Creates a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     * 
     * @group Manging Tickets
     */
    public function store(StoreTicketRequest $request)
    {
        // policy
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to store that resource');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // copied from the index()
        if($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */

    // PATCH AND PUT BOTH UPDATING DATA BUT ARE NOT THE SAME, SO WE HAVE TO SEPARATE PATCH IN A UPDATE() FUNCTION AND PUT IN A REPLACE() FUNCTION

    public function update(UpdateTicketRequest $request, Ticket $ticket) //, $ticket_id
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
    
            // return a pretty JSON with the data 
            return new TicketResource($ticket);
        }
                
        return $this->notAuthorized('You are not authorized to update that resource.');
    }

    public function replace(ReplaceTicketRequest $request, Ticket $ticket) //$ticket_id
    {
        //policy
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());

            // return a pretty JSON with the data 
            return new TicketResource($ticket);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket) //$ticket_id
    {
        // policy
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }

        return $this->notAuthorized('You are not authorized to delete that resource');
    }
}
