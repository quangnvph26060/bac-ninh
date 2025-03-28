<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:categories',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.*
     * @return array
     */
    public function messages()
    {
        return __('request.messages');
    }
}
