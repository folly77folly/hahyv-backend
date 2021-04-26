<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRateRequest extends FormRequest
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
            'subscription_type_id' => ['required', 'exists:subscription_types,id'],
            'amount' => ['required', 'numeric', 'regex:/^\d*(\.\d{1,2})?$/']
        ];
    }
}
