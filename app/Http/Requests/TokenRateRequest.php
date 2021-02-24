<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokenRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'rate' => ['required', 'numeric', 'regex:/^\d*(\.\d{1,2})?$/'],
             'unit' => ['required', 'integer', 'gte:1']
        ];
    }

    
    public function messages()
    {
        return [
            'rate.required' => 'You must provide the rate of the token to dollar',
            'rate.integer' => 'The token provided must be a number',
            'unit.integer' => 'The unit provided must be an integer'
        ];
    }
}
