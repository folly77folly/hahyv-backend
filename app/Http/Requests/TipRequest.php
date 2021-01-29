<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipRequest extends FormRequest
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
            //
        return [
            'creator_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'between:0.5,100.00', 'regex:/^\d*(\.\d{1,2})?$/'],
        ];
    }
}
