<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id', null);

        return [
            'name' => 'required|unique:categories,name,' . $id,
            'slug' => 'nullable|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:1,2',
            'parent_id' => 'nullable|exists:categories,id',
            'seo_title' => 'nullable|string|max:250',
            'seo_description' => 'nullable|string|max:250',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048'
        ];
    }
}
