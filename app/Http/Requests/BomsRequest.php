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
        $id = $this->route('id'); // 'id' ở route (nullable nếu đang thêm mới)
        $productableType = $this->input('productable_type');
       

        return [
            'productable_id' => [
                'required',
                function ( $value, $fail) use ($id, $productableType) {
                    $query = Bom::where('productable_type', $productableType)
                        ->where('productable_id', $value);

                    if ($id) {
                        // Đang sửa, cho phép nếu ID giống ID cũ
                        $bom = Bom::find($id);

                        if ($bom && ($bom->productable_id != $value || $bom->productable_type != $productableType)) {
                            // Nếu đổi sang product khác đã tồn tại -> không cho
                            if ($query->exists()) {
                                $fail('Sản phẩm này đã có vật liệu, không thể gán lại.');
                            }
                        }
                    } else {
                        // Đang thêm mới -> không được trùng
                        if ($query->exists()) {
                            $fail('Sản phẩm này đã có danh sách vật liệu.');
                        }
                    }
                }
            ],
            'productable_type' => 'required|string',
            'values' => 'required|array|min:1',
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
