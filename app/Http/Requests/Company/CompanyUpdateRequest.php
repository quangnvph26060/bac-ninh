<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
           'name' => 'required|unique:companies,name,'.$this->id,
           'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
           'email' => 'required|email|unique:companies,email,'.$this->id,
           'address' => 'required',
           'tax_number' => 'required',
           'bank_account' => 'required',
           'bank_id' => 'required|exists:banks,id',
           'note' => 'nullable',
           'city_id' => 'required|exists:city,id',
        ];
    }

    public function messages(): array
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên công ty',
            'phone' => 'Số điện thoại',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'tax_number' => 'Mã số thuế',
            'bank_account' => 'Số tài khoản ngân hàng',
            'bank_id' => 'Ngân hàng',
            'note' => 'Ghi chú',
            'city_id' => 'Thành phố',
        ];
    }
}
