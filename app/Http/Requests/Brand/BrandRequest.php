<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'name' => 'required|max:250|unique:brands,name,' . $id,
            'slug' => 'nullable|max:150|unique:brands,slug,' . $id,
            'description' => 'nullable|string|max:400',
            'seo_title' => 'nullable|string|max:250',
            'website' => 'nullable|string|max:150',
            'seo_description' => 'nullable|string|max:400',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'status' => 'required|nullable|in:1,2'
        ];
    }
}
