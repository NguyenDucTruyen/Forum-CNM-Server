<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

class SignInRequest extends FormRequest
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
            //
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','min:8'],
            'otp'=>['required']
        ];
    }
    //customize err
    public function messages()
    {
        return[
            'firstName.required'=>'Please enter your name',
            'lastName.required'=>'Please enter your name',
            'email.email' =>'Please enter the correct email format',
            'email.required'=>'Please enter your email',
            'password.required'=>'PLease enter your password',
            'password.min'=> 'PLease enter more than 8 character',
            'otp.required'=> 'Please enter your OTP code'
        ];
    }
}
