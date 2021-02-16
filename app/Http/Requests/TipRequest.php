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
            'amount' => ['required', 'numeric', 'between:500,2000', 'regex:/^\d*(\.\d{1,2})?$/'],
        ];
    }

    public function messages()
    {
            //
        return [
            'amount.between' => "The amount must be between N500.00 and N2,000.00.",
        ];
    }
}
