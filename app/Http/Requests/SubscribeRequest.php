<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
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
            'creator_id' => ['required', 'exists:users,id'],
            // 'card_id' => ['required', 'exists:cards,id'],
            'amount' => ['required'],
            'trxref' => ['required'],
            'reference' => ['required'],
            'subscription_id' => ['required', 'exists:subscription_rates,id']
        ];
    }

    public function messages()
    {
        return [
            //
            'subscription_id.required' => 'The Creator selected is not available',
            'subscription_id.exists' => 'The Creator selected is not available',
            'trxref.required' => 'Transaction reference is required',
            'reference.required' => 'Reference number is required',
        ];
    }
}
