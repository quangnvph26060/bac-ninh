<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class MaterialsEmptySheet implements FromArray, WithTitle, WithHeadings, WithEvents, ShouldAutoSize
{
    public function headings(): array
    {
        return ['Mã NVL', 'Tên vật tư', 'Đơn vị', 'Số lượng báo động', 'Ghi chú'];
    }

    public function array(): array
    {
        // Trả về 1 dòng trống để user điền vào
        return [];
    }

    public function title(): string
    {
        return 'Nhập dữ liệu';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Chỉ định dạng tiêu đề A1:E1
                $cellRange = 'A1:E1';
                $sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ]
                ]);

            }
        ];
    }

}
