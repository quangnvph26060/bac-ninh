<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'name' => 'required|max:250|unique:suppliers,name,' . $id,
            'representative_name' => 'required|max:250',
            'position' => 'nullable|max:100',
            'phone' => ['required', 'regex:/^(0|\+84)[3-9][0-9]{8}$/', 'max:12'],
            'email' => 'nullable|max:250|email|unique:suppliers,email,' . $id,
            'address' => 'nullable|max:250',
            'tax_code' => 'nullable',
            'bank_account_number' => 'nullable',
            'bank_id' => 'nullable|exists:banks,id',
            'notes' => 'nullable|max:400',
            'status' => 'nullable|numeric',
            'brand_id' => 'nullable|array',
            'brand_id.*' => 'exists:brands,id'
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }
}
