<?php

namespace App\Http\Requests\OTP;

use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['email']
        ];
    }

    //customize err
    public function messages()
    {
        return [
            'email.email' =>'Please enter the correct email format',
        ];
    }
}
