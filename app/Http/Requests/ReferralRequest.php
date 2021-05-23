<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ReferralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->role_id == 1;
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
            'number_to_refer' => ['required', 'integer', 'gte:0'],
            'amount' => ['required', 'numeric', 'gte:0', 'regex:/^\d*(\.\d{1,2})?$/'],
        ];
    }
}
