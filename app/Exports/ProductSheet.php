<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProductSheet implements WithHeadings, WithColumnFormatting, WithEvents
{
    public function headings(): array
    {
        return ['ID', 'Mã sản phẩm', 'Tên sản phẩm', 'Số lượng', 'Giá nhập', 'Giá bán', 'Danh mục', 'Thương hiệu', 'Đơn vị'];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // Số lượng
            'E' => '#,##0 [$₫-vi-VN]', // Giá nhập
            'F' => '#,##0 [$₫-vi-VN]', // Giá bán
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $maxRows = 100;

                // Ghi dữ liệu mẫu vào dòng thứ 2
                $sampleData = [
                    '1',
                    'SP001',
                    'Áo Thun Nam',
                    '50',
                    '100000',
                    '150000',
                    'Thời trang',
                    'Nike',
                    'Cái'
                ];
                $sheet->fromArray($sampleData, null, 'A2');

                // Định dạng dòng dữ liệu mẫu
                $sheet->getStyle('A2:I2')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF99']
                    ],
                ]);

                // Cột danh mục (G) - Sử dụng sheet 'Danh mục'
                $validationCategory = $sheet->getCell('G3')->getDataValidation();
                $validationCategory->setType(DataValidation::TYPE_LIST);
                $validationCategory->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validationCategory->setAllowBlank(false);
                $validationCategory->setShowDropDown(true);
                $validationCategory->setFormula1("='Danh mục'!A1:A100");

                // Cột thương hiệu (H) - Sử dụng sheet 'Thương hiệu'
                $validationBrand = $sheet->getCell('H3')->getDataValidation();
                $validationBrand->setType(DataValidation::TYPE_LIST);
                $validationBrand->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validationBrand->setAllowBlank(false);
                $validationBrand->setShowDropDown(true);
                $validationBrand->setFormula1("='Thương hiệu'!A1:A100");

                // Áp dụng dropdown cho nhiều dòng
                for ($i = 4; $i <= $maxRows; $i++) {
                    $sheet->getCell("G$i")->setDataValidation(clone $validationCategory);
                    $sheet->getCell("H$i")->setDataValidation(clone $validationBrand);
                }
            },
        ];
    }
}
