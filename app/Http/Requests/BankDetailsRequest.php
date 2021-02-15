<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankDetailsRequest extends FormRequest
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
            'bank_id' => ['required', 'string'],
            'bank_name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'account_no' => ['required','max:10', 'string'],
            'bvn' => ['required', 'max:11', 'string'],
            'address'=> ['string','string'],
            'phone_no' => ['required','string'],
            'zip_code' => ['nullable','string'],
            'country_id' => ['required','exists:countries,id'],
            'instagram' => ['nullable', 'string'],
            'twitter' => ['nullable', 'string'],
            'date_of_birth' => ['required','date','before:-18 years'],
            'identification_image' => ['required','string'],
            'identification_exp_date' => ['required','date','after:today'],
        ];

    }

    public function messages(){
        return [
            'date_of_birth.before' => "You must be above 18 years"
        ];
    }
}
