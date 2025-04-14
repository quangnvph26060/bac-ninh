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
        return [
            'ID',
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Số lượng',
            'Giá nhập',
            'Giá bán',
            'Danh mục',
            'Thương hiệu',
            'Đơn vị',
            'Slug',
            'Ảnh',
            'Giá khuyến mãi',
            'Ngày bắt đầu giảm',
            'Ngày kết thúc giảm',
            'Trạng thái kho',
            'Mô tả',
            'Nổi bật',
            'Hiển thị trang chủ',
            'Trạng thái',
            'Tiêu đề SEO',
            'Mô tả SEO',
            'Tags',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER, // Số lượng
            'E' => '#,##0 [$₫-vi-VN]', // Giá nhập
            'F' => '#,##0 [$₫-vi-VN]', // Giá bán
            'M' => 'yyyy-mm-dd', // Ngày bắt đầu giảm
            'N' => 'yyyy-mm-dd', // Ngày kết thúc giảm
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
                    '1',                    // ID
                    'SP001',                // Mã sản phẩm
                    'Áo Thun Nam',          // Tên sản phẩm
                    '50',                   // Số lượng
                    '100000',               // Giá nhập
                    '150000',               // Giá bán
                    'Thời trang',           // Danh mục
                    'Nike',                 // Thương hiệu
                    'Cái',                  // Đơn vị
                    'ao-thun-nam',          // Slug
                    'https://example.com/image.jpg', // Ảnh
                    '120000',               // Giá khuyến mãi
                    now()->format('Y-m-d'), // Ngày bắt đầu giảm
                    now()->addDays(5)->format('Y-m-d'), // Ngày kết thúc giảm
                    'in_stock',             // Trạng thái kho
                    'Áo thun chất cotton mịn mát, thoải mái', // Mô tả
                    '1',                    // Nổi bật
                    '1',                    // Hiển thị trang chủ
                    '1',                    // Trạng thái
                    'Áo thun nam cao cấp',  // Tiêu đề SEO
                    'Mô tả sản phẩm SEO',   // Mô tả SEO
                    'áo thun, nam, cotton', // Tags
                ];

                $sheet->fromArray($sampleData, null, 'A2');

                // Định dạng dòng dữ liệu mẫu
                $sheet->getStyle('A2:V2')->applyFromArray([
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

                // Dropdown Trạng thái kho (O - cột 15)
                $validationStockStatus = $sheet->getCell('O3')->getDataValidation();
                $validationStockStatus->setType(DataValidation::TYPE_LIST);
                $validationStockStatus->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validationStockStatus->setAllowBlank(false);
                $validationStockStatus->setShowDropDown(true);
                $validationStockStatus->setFormula1('"in_stock,out_of_stock,waiting_for_goods"');

                // Dropdown Trạng thái (S - cột 19)
                $validationStatus = $sheet->getCell('S3')->getDataValidation();
                $validationStatus->setType(DataValidation::TYPE_LIST);
                $validationStatus->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validationStatus->setAllowBlank(false);
                $validationStatus->setShowDropDown(true);
                $validationStatus->setFormula1('"1,2"');

                // Áp dụng dropdown cho nhiều dòng
                for ($i = 3; $i <= $maxRows; $i++) {
                    $sheet->getCell("G$i")->setDataValidation(clone $validationCategory);
                    $sheet->getCell("H$i")->setDataValidation(clone $validationBrand);
                    $sheet->getCell("O$i")->setDataValidation(clone $validationStockStatus);
                    $sheet->getCell("S$i")->setDataValidation(clone $validationStatus);
                }

                // Ghi chú cột "Nổi bật" (Q - cột 17)
                $sheet->getComment('Q1')->getText()->createTextRun("Nổi bật: 1 or 0");
                // Ghi chú cột "Hiển thị trang chủ" (R - cột 18)
                $sheet->getComment('R1')->getText()->createTextRun("Hiển thị trang chủ: 1 or 0");
                // Ghi chú cột "Trạng thái" (S - cột 19)
                $sheet->getComment('S1')->getText()->createTextRun("Trạng thái: 1 or 2");
            },
        ];
    }
}
