<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Resources\v1\TicketResource;
use App\Models\Ticket;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Policies\V1\TicketPolicy;

class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;
    
    /**
     * Get all tickets
     * 
     * Retrieves all tickets created by a specific user.
     * 
     * @group Managing Tickets by Author
     * 
     * @urlParam author_id integer required The author's ID. No-example
     * 
     * @response 200 {"data":[{"type":"user","id":3,"attributes":{"name":"Mr. Henri Beatty MD","email":"bmertz@example.net","isManager":false,"emailVerifiedAt":"2024-03-14T04:41:51.000000Z","createdAt":"2024-03-14T04:41:51.000000Z","udpatedAt":"2024-03-14T04:41:51.000000Z"},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/3"}}],"links":{"first":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","last":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"links":[{"url":null,"label":"&laquo; Previous","active":false},{"url":"http:\/\/localhost:8000\/api\/v1\/authors?page=1","label":"1","active":true},{"url":null,"label":"Next &raquo;","active":false}],"path":"http:\/\/localhost:8000\/api\/v1\/authors","per_page":15,"to":1,"total":10}}
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name
     * @queryParam filter[name] Filter by name. Wildcards are supported. 
     * @queryParam filter[email] Filter by email. Wildcards are supported.
     */
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }
    
    /**
     * Store a new ticket for a specific author.
     *
     * @urlParam author_id int required The ID of the author. Example: 123
     * @bodyParam data.attributes.title string required The title of the ticket. Example: Bug in login
     * @bodyParam data.attributes.description string required A description of the issue. Example: Login fails with error 500.
     * @bodyParam data.attributes.status string required Must be one of A, C, H, X. Example: A
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id'
            ])));
        }
                
        return $this->notAuthorized('You are not authorized to create that resource');
    }

    /**
     * Update the specified resource in storage.
     */

    // PATCH AND PUT BOTH UPDATING DATA BUT ARE NOT THE SAME, SO WE HAVE TO SEPARATE PATCH IN A UPDATE() FUNCTION AND PUT IN A REPLACE() FUNCTION

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // policy
        if ($this->isAble('update', $ticket)) {
            
            $ticket->update($request->mappedAttributes());
            // return a pretty JSON with the data 
            return new TicketResource($ticket);
        }
                
        return $this->notAuthorized('You are not authorized to update that resource');
    }

    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        // policy
        if ($this->isAble('replace', $ticket)) {
            
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // policy
        if($this->isAble('delete', $ticket)) {
        
            $ticket->delete();

            return $this->ok('Ticket Successfully deleted');
        }

        return $this->notAuthorized('You are not authorized to delete that resource');
    }
}
