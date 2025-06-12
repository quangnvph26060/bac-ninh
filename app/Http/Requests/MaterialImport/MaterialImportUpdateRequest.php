<?php

namespace App\Http\Requests\MaterialImport;

use Illuminate\Foundation\Http\FormRequest;

class MaterialImportUpdateRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'code' => "nullable|string|max:50|unique:material_imports,code,{$id}",
            'date' => 'required|date_format:d/m/Y|before_or_equal:today',
            'note' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',

            'materials' => 'required|array|min:1',
            'materials.*.unit_price' => 'required|numeric|min:0',
            'materials.*.quantity' => 'required|numeric|min:1',

            'payments' => ['array'],
            'payments.*.amount' => ['required', 'numeric', 'min:0', 'regex:/^\d*(\.\d{1,2})?$/'],
            'payments.*.date' => ['required', 'date_format:d/m/Y', 'before_or_equal:today'],
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã phiếu nhập',
            'date' => 'ngày nhập',
            'supplier_id' => 'nhà cung cấp',
            'summary_paid' => 'số tiền đã thanh toán',

            'materials' => 'danh sách nguyên vật liệu',
            'materials.*.unit_price' => 'đơn giá nguyên vật liệu',
            'materials.*.quantity' => 'số lượng nguyên vật liệu',
        ];
    }
}
