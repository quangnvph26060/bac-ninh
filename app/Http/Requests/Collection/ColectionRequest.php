<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;

class ColectionRequest extends FormRequest
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
            'name' => 'required|max:250|unique:collections,name,' . $id,
            'slug' => 'nullable|unique:collections,slug,' . $id,
            'description' => 'nullable|string|max:400',
            'status' => 'required|in:1,2',
            'category_id' => 'nullable|array',
            'category_id.*' => 'exists:categories,id'
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return  [
            'name' =>  'Tên bộ sưu tập',
            'slug' => 'Đường dẫn',
            'description' => 'Mô tả ngắn',
            'status' => 'Trạng thái'
        ];
    }
}
