<?php

namespace App\Http\Requests\Api\V1;

class StoreUserRequest extends BaseUserRequest
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
        return [
            // example ->
            // '...' => is 'required' and if is not an 'string' = X error
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'required|email',
            'data.attributes.isManager' => 'required|boolean',
            'data.attributes.password' => 'required|string'
        ];
    }
}
