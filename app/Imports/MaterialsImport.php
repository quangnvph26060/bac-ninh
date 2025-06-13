<?php

namespace App\Imports;

use App\Models\Material;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class MaterialsImport implements ToModel, WithHeadingRow, WithChunkReading, WithEvents
{
    public int $success = 0;
    public int $error = 0;
    public array $errors = [];

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function () {
                // Reset biến đếm nếu cần import nhiều lần
                $this->success = 0;
                $this->error = 0;
                $this->errors = [];
            },
        ];
    }

    public function model(array $row)
    {
        $validator = Validator::make(
            $row,
            [
                'ma_nvl' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('materials', 'code'),
                ],
                'ten_vat_tu' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('materials', 'name'),
                ],
                'don_vi' => 'nullable|string|max:255',
                'so_luong_bao_dong' => 'required|numeric|min:0',
                'ghi_chu' => 'nullable|string|max:255',
            ],
            [ // ✅ custom message đúng chỗ
                'ma_nvl.required' => 'Mã nguyên vật liệu là bắt buộc.',
                'ma_nvl.string' => 'Mã nguyên vật liệu phải là chuỗi.',
                'ma_nvl.max' => 'Mã nguyên vật liệu không được vượt quá 255 ký tự.',
                'ma_nvl.unique' => 'Mã nguyên vật liệu đã tồn tại.',

                'ten_vat_tu.required' => 'Tên vật tư là bắt buộc.',
                'ten_vat_tu.string' => 'Tên vật tư phải là chuỗi.',
                'ten_vat_tu.max' => 'Tên vật tư không được vượt quá 255 ký tự.',
                'ten_vat_tu.unique' => 'Tên vật tư đã tồn tại.',

                'don_vi.string' => 'Đơn vị phải là chuỗi.',
                'don_vi.max' => 'Đơn vị không được vượt quá 255 ký tự.',

                'so_luong_bao_dong.required' => 'Số lượng báo động là bắt buộc.',
                'so_luong_bao_dong.numeric' => 'Số lượng báo động phải là số.',
                'so_luong_bao_dong.min' => 'Số lượng báo động phải lớn hơn hoặc bằng 0.',

                'ghi_chu.string' => 'Ghi chú phải là chuỗi.',
                'ghi_chu.max' => 'Ghi chú không được vượt quá 255 ký tự.',
            ]
        );


        if ($validator->fails()) {
            $this->error++;

            // Gom các lỗi lại, loại trùng
            $messages = $validator->errors()->all();
            foreach ($messages as $message) {
                if (!in_array($message, $this->errors)) {
                    $this->errors[] = $message;
                }
            }

            return null;
        }


        try {
            Material::updateOrCreate(
                ['code' => $row['ma_nvl']],
                [
                    'name' => $row['ten_vat_tu'],
                    'unit' => $row['don_vi'],
                    'min_stock' => $row['so_luong_bao_dong'],
                    'note' => $row['ghi_chu'],
                ]
            );

            $this->success++;
        } catch (\Throwable $e) {
            $this->error++;
            $this->errors[] = [
                'row' => $row,
                'message' => $e->getMessage(),
            ];
        }

        return null; // Không return model để tránh auto insert thêm
    }

    public function chunkSize(): int
    {
        return 1000; // hoặc 500, tùy dung lượng server
    }
}
