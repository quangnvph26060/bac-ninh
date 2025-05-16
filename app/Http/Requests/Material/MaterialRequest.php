<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
        $rules = [
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:normal,variant',
            'price_usd'    => 'required_if:type,normal|min:0',
            'price_vnd'    => 'required_if:type,normal|min:0',
            'stock'        => 'required_if:type,normal|min:0',
            'distributor'  => 'nullable|string|max:255',
            'sku'          => 'nullable|string|max:100|unique:materials,sku',
            'status'       => 'nullable'
        ];

        if ($this->input('type') != 'normal') {
            $rules['attributes'] = 'required|array|min:1';
            $rules['variants']   = 'required|array|min:1';

            $rules['variants.*.attribute_value_ids'] = 'required';
            $rules['variants.*.sku']                 = 'required';
            $rules['variants.*.price']               = 'required';
            $rules['variants.*.product_unit']        = 'required';
            $rules['variants.*.stock']               = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name'                          => 'Tên vật liệu',
            'type'                          => 'Loại vật liệu',
            'price_usd'                     => 'Giá vật liệu (USD)',
            'price_vnd'                     => 'Giá vật liệu (VND)',
            'distributor'                  => 'Nhà phân phối',
            'stock'                         => 'Số lượng tồn',
            'sku'                           => 'Mã vật liệu',

            'attributes'                    => 'Thuộc tính',
            'attributes.*'                  => 'Thuộc tính',

            'variants'                      => 'Biến thể',
            'variants.*.attribute_value_ids'     => 'Giá trị thuộc tính',
            'variants.*.attribute_value_ids.*'   => 'Giá trị thuộc tính chi tiết',
            'variants.*.sku'                => 'Mã biến thể',
            'variants.*.price'              => 'Giá biến thể',
            'variants.*.product_unit'       => 'Đơn vị biến thể',
            'variants.*.stock'              => 'Số lượng tồn kho biến thể',
        ];
    }
}
