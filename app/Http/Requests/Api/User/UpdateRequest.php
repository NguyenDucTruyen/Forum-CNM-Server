<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'firstName' => ['sometimes'],
            'lastName' => ['sometimes'],
            'gender' => ['sometimes'],
            'phone' => ['sometimes'],
            'dayOfBirth'=>['sometimes'],
            'profileImage'=>['sometimes'],
        ];
    }
    //customize err
    public function messages()
    {
        return [
            'firstName.required' => 'Please enter your name',
            'lastName.required' => 'Please enter your name',
            'gender.required' => 'Please enter your gender',
            'dayOfBirth.required' => 'Please enter your dayOfBirth',
            'profileImage.required' => 'Please enter your profileImage',
        ];
    }
}
