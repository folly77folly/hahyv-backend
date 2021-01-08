<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountNumberRequest extends FormRequest
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
            //
            'account_number' => ['required', 'digits:10'],
            'bank_code' => ['required', 'max:3'],
        ];
    }
}
