<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class EmployeeRequest extends FormRequest
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
            'full_name' => 'required|max:250',
            "phone" => [
                "required",
                "regex:/^(0|\+84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9])[0-9]{7}$/",
                "unique:employees,phone,{$id}"
            ],
            'email' => "required|email|max:250|unique:employees,email,{$id}",
            'gender' => 'required|in:other,male,female',
            'date_of_birth' => 'nullable|date|date_format:d-m-Y|before:today|after:01-01-1900',
            'address' => 'nullable|max:255',
            'password' => [$id ? 'nullable' : 'required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'confirm_password' => ($id ? 'nullable' : 'required') . '|same:password',
            'identity_card_number' => 'nullable|regex:/^\d{12}$/',
            'contract_type' => 'required|in:full-time,part-time,probation',
            'note' => 'nullable|max:500',
            'status' => 'required|in:1,2',
            'avatar' => 'nullable|image|mimes:png,jpg,webp|max:2048',
            'identity_card_image' => 'nullable|image|mimes:png,jpg,webp|max:2048',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'họ tên',
            'phone' => 'số điện thoại',
            'email' => 'email',
            'gender' => 'giới tính',
            'date_of_birth' => 'ngày sinh',
            'address' => 'địa chỉ',
            'password' => 'mật khẩu',
            'confirm_password' => 'xác nhận mật khẩu',
            'identity_card_number' => 'số căn cước công dân',
            'contract_type' => 'loại hợp đồng',
            'note' => 'ghi chú',
            'status' => 'trạng thái',
            'avatar' => 'ảnh đại diện',
            'identity_card_image' => 'ảnh CCCD',
        ];
    }
}
// "phone" => "required|regex:/^\+?[1-9]\d{1,14}$/|unique:employees,phone,{$id}",
