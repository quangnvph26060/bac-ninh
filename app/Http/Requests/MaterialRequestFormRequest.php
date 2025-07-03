<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequestFormRequest extends FormRequest
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
    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'item_id' => ['required'], // format: product_id-variant_id
            'materials' => 'required|array|min:1',
            'materials.*.quantity' => 'required|numeric|min:0.01',
            'materials.*.note' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ];
    }
}
