<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpiryRequest extends FormRequest
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
            'user_id' => ['required', 'exists:subscribers_lists,user_id'],
            'expiry' => ['required', 'date'],
        ];
    }
}
