<?php

namespace App\Http\Requests\Reply;

use Illuminate\Foundation\Http\FormRequest;

class Updaterequest extends FormRequest
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
            'content' => ['required'],
        ];
    }

    //customize err
    public function messages()
    {
        return [

            // Messages for content
            'content.required' => 'The content field is required',
        ];
    }
}
