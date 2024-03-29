<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardPaymentRequest extends FormRequest
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
            'card_id' => ['required', 'exists:cards,id', 'integer'],
            'amount' => ['required','gt:0.5'],
            'description' => ['required', 'string']
        ];
    }

    public function messages(){
        return [
            'card_id.exists' => 'Please add a card and set it as default',
            'card_id.required' => 'Please add a card and set it as default'
        ];
    }
}
