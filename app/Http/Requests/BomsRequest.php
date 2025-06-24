<?php

namespace App\Http\Requests;

use App\Models\Bom;
use Illuminate\Foundation\Http\FormRequest;

class BomsRequest extends FormRequest
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
        $productable_type = $this->input('variant_id') ? \App\Models\ProductVariant::class : \App\Models\Product::class;

        $productable_id = $this->input('variant_id');
        if (!$productable_id) {
            $productable_id = $this->input('product_id');
        }

        $this->merge([
            'productable_type' => $productable_type,
            'productable_id' => $productable_id
        ]);

        return [
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'values' => 'required|array',
            'values.*.material_id' => 'required|distinct',
            'values.*.quantity_required' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'productable_id' => 'Sản phẩm',
            'values' => 'Danh sách vật liệu',
            'values.*.quantity_required' => 'Số lượng cần thiết',
            'values.*.material_id' => 'Nguyên vật liệu',
        ];
    }
}
