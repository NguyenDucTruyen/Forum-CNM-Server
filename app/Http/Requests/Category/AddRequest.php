<?php

namespace App\Http\Requests\Category;

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
            'categoryName' => ['required', 'string', 'unique:categories,categoryName']
        ];
    }

    //customize err
    public function messages()
    {
        return [
            'categoryName.required' => 'Please enter Category Name',
            'categoryName.string' => 'Please enter the correct format (String)',
            'categoryName.unique' => 'This Category Name already had'
        ];
    }
}
