<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    // public function failedValidation(Validator $validator)
    // {
    //     $response = [
    //         "status" => "failure",
    //         "status_code" => 400,
    //         "message" => "Bad Request",
    //         "errors" => $validator->errors(),
    //     ];
    //     throw new HttpResponseException(response()->json($response,400));
    // }

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
            'email'=> 'required',
            'provider' => 'nullable',
            'password'=> 'required_without:provider'
        ];
    }
}
