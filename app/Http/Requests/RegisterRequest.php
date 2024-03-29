<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{

    public function failedValidation(Validator $validator)
    {
        $response = [
            "status" => "failure",
            "status_code" => 400,
            "message" => "Bad Request",
            "errors" => $validator->errors(),
        ];
        throw new HttpResponseException(response()->json($response,400));
    }
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
            'name'=> 'required|max:50',
            'username' => 'required|string|unique:users|alpha_dash',
            'email'=> 'required|email|unique:users',
            "otp"=> "string",
            "provider_name" => 'nullable',
            "provider_id" => 'nullable',
            'password'=> ['required','confirmed','min:8','regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'],
            "ip_address" => 'nullable',
        ];
    }
}
