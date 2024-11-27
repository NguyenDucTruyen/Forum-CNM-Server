<?php

namespace App\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;

class RefreshRequest extends FormRequest
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
            'access_token' => ['required'],
        ];
    }

    //customize err
    public function messages()
    {
        return [

            // Messages for content
            'access_token.required' => 'The access_token is required',
        ];
    }
}
