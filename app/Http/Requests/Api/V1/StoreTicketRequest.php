<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\BaseTicketRequest;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // verifies if the current route is tickets.store
        $isTicketsController = $this->routeIs('tickets.store');
        // defines the path of the author, if it's a JSON:API or simple format
        $authorIdAttr = $isTicketsController ? 'data.relationships.author.data.id' : 'author';
        // use the Auth facade to be able to generate the scribe -get the authenticated user
        $user = Auth::user();
        // the author must exists and be a valid ID
        $authorRule = 'required|integer|exists:users,id';

        // Body for the store() method in TicketController
        $rules = [
            // example ->
            // '...' => is 'required' and if is not an 'string' = X error
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            $authorIdAttr => $authorRule . '|size:' . $user->id
        ];

        // if the route is JSON:API, validates 'relationships' structure too
        if ($isTicketsController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        // reasign the rule for the author to be sure that be iqual than the current
        $rules[$authorIdAttr] = $authorRule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttr] = $authorRule;
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    public function bodyParameters()
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => "The ticket's title (method)",
                'example' => 'No-example'
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example' => 'No-example'
            ],
            'data.attributes.status' => [
                'description' => "The ticket's status",
                'example' => 'No-example'
            ]
        ]; 

        if ($this->routeIs('tickets.store')) {
           $documentation['data.relationships.author.data.id'] = [
            'descirption' => 'The author assigned to the ticket.',
            'example' => 'No-example'
           ]; 
        } else {
            $documentation['author'] = [
                'descirption' => 'The author assigned to the ticket.',
                'example' => 'No-example'
            ];
        }
        return $documentation;
    }
}
