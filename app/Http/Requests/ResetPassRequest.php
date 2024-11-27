<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPassRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'otp' => 'required|size:6',
            'password' => 'required|string|min:8|',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Please enter your email',
            'email.email' =>'Please enter the correct email format',
            'password.required' => 'PLease enter your password',
            'password.min' => 'PLease enter more than 8 character',
            'otp.required'=>'OTP is null',
            'otp.size'=>'OTP is 6 character'
        ];
    }
}
