<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\BaseTicketRequest;

class ReplaceTicketRequest extends BaseTicketRequest
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
        // Body for the store() method in TicketController
        $rules = [
            // example ->
            // '...' => is 'required' and if is not an 'string' = X error
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            'data.relationships.author.data.id' => 'required|integer'
        ];

        return $rules;
    }

    // explain the client why the status value is invalid if they set a value which has not be A, C, H, or X
    public function messages()
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H, or X',
        ];
    }
}
