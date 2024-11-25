<?php

namespace App\Http\Requests\Comment;

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
            'blog_id' => ['required', 'integer', 'exists:blogs,id'],
            'content' => ['required'],
        ];
    }

    //customize err
    public function messages()
    {
        return [
            // Messages for category_id
            'blog_id.required' => 'The blog field is required',
            'blog_id.integer' => 'The blog must be a number',
            'blog_id.exists' => 'The selected blog does not exist',

            // Messages for content
            'content.required' => 'The content field is required',
        ];
    }
}
