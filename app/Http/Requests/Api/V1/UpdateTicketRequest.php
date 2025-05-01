<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\BaseTicketRequest;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class UpdateTicketRequest extends BaseTicketRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Body for the store() method in TicketController
        $rules = [
            // example ->
            // '...' => is 'required' and if is not an 'string' = X error
            'data.attributes.title' => 'sometimes|string',
            'data.attributes.description' => 'sometimes|string',
            'data.attributes.status' => 'sometimes|string|in:A,C,H,X',
            'data.relationships.author.data.id' => 'prohibited'
        ];

        // use the Auth facade to be able to generate the scribe
        if (Auth::user()->tokenCan(Abilities::UpdateOwnTicket)) {
            $rules['data.relationships.author.data.id'] = 'sometimes|integer';
        }

        return $rules;
    }
}
