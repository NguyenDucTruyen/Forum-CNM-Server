<?php

namespace App\Http\Requests\Blog;

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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required'],
            'content' => ['required'],
            'blogImage' => ['sometimes'],
        ];
    }

    //customize err
    public function messages()
    {
        return [
            // Messages for category_id
            'category_id.required' => 'The category field is required',
            'category_id.integer' => 'The category must be a number',
            'category_id.exists' => 'The selected category does not exist',

            // Messages for title
            'title.required' => 'The title field is required',

            // Messages for content
            'content.required' => 'The content field is required',
        ];
    }
}
