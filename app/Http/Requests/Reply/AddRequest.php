<?php

namespace App\Http\Requests\Reply;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'comment_id' => ['required', 'integer', 'exists:comments,id'],
            'content' => ['required'],
        ];
    }

    //customize err
    public function messages()
    {
        return [
            // Messages for category_id
            'comment_id.required' => 'The comment field is required',
            'comment_id.integer' => 'The comment must be a number',
            'comment_id.exists' => 'The selected comment does not exist',

            // Messages for content
            'content.required' => 'The content field is required',
        ];
    }
}
