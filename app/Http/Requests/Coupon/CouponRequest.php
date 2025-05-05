<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'code' => 'required|unique:coupons,code,' . $id,
            'value' => 'required|numeric|max:100',
            'type' => 'required|in:order,product',
            'max_discount' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'min_order_value' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'start_date' => 'nullable|date|date_format:d-m-Y H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|in:1,2',
            'product_id' => 'nullable'
        ];
    }

    public function messages(): array
    {
        return __('request.messages');
    }


    public function attributes(): array
    {
        return [
            'code' => 'mã giảm giá',
            'value' => 'giá trị giảm',
            'type' => 'kiểu giảm giá',
            'max_discount' => 'giảm tối đa',
            'min_order_value' => 'giá trị đơn tối thiểu',
            'start_date' => 'ngày bắt đầu',
            'end_date' => 'ngày kết thúc',
            'usage_limit' => 'giới hạn sử dụng',
            'usage_per_user' => 'giới hạn mỗi người dùng',
            'description' => 'mô tả',
            'status' => 'trạng thái',
        ];
    }
}
