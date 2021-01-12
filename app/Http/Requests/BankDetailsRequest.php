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
            'bank_id' => ['required'],
            'bank_name' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'account_no' => ['required','digits:10'],
            'bvn' => ['required','digits:11'],
            'phone_no' => ['required'],
            'zip_code' => ['nullable'],
            'country_id' => ['required','exists:countries,id'],
            'instagram' => ['nullable'],
            'twitter' => ['nullable'],
            'date_of_birth' => ['required','date'],
            'identification_image' => ['required'],
            'identification_exp_date' => ['required','date'],
        ];
    }
}
