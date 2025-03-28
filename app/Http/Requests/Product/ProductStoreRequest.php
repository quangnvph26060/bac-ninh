<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
        return [
            'name' => 'required|unique:products',
            'price' => 'required|integer|min:0',
            'product_unit' => 'required',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'is_featured' => 'nullable',
            'is_new_arrival' => 'nullable',
            'brand_id' => 'required|exists:brands,id',
        ];
    }

    public function messages(): array
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên sản phẩm',
            'price' => 'Giá',
            'quantity' => 'Số lượng',
            'product_unit' => 'Đơn vị',
            'category_id' => 'Danh mục',
            'description' => 'Mô tả',
            'is_featured' => 'Đặc biệt',
            'is_new_arrival' => 'Mới',
            'discount_id' => 'Giảm giá',
            'brand_id' => 'Nhãn hiệu',
        ];
    }
}
