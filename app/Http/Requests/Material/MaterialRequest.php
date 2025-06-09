<?php

namespace App\Http\Requests\Material;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        return [
            'name' => 'required|string|max:255',
            'import_code' => 'nullable|string|max:255',

            'data' => 'required|array|min:1',
            'data.*.type_name' => 'required|string|max:255',
            'data.*.supplier_name' => 'required|string|max:255',
            'data.*.price' => 'required|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'data.*.quantity' => 'required|numeric|min:0',
            'data.*.unit' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return __('request.messages');
    }


    public function attributes()
    {
        return [
            'material_id' => 'nguyên vật liệu',
            'data.*.type_name' => 'loại vật liệu',
            'data.*.price' => 'giá',
            'data.*.quantity' => 'số lượng',
            'data.*.unit' => 'đơn vị',
            'data.*.supplier_name' => 'nhà cung cấp',
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     $messages = $validator->errors()->getMessages();

    //     $customMessages = [];

    //     foreach ($messages as $key => $errors) {
    //         if (preg_match('/data\.(\d+)\.(\w+)/', $key, $matches)) {
    //             $index = (int) $matches[1] + 1; // dòng bắt đầu từ 1
    //             $field = $matches[2];
    //             foreach ($errors as $error) {
    //                 // Thay :position = dòng
    //                 $customMessages[] = str_replace(':position', $index, $error);
    //             }
    //         } else {
    //             $customMessages = array_merge($customMessages, $errors);
    //         }
    //     }

    //     throw new HttpResponseException(response()->json([
    //         'message' => $customMessages[0],
    //     ], 422));
    // }
}
