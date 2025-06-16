<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderImportTemplateExport implements FromArray, WithHeadings, WithEvents
{
    public function headings(): array
    {
        return [
            'Order Code',
            'Order Name',
            'Product Name',
            'Product Variant SKU',
            'Quantity',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Nation',
            'State',
            'City',
            'Street Address',
            'Zip Code',
            'Note',
            'Delivery Method',
            'Tracking Number',
            'Mockup Image',
            'Design Image'
        ];
    }

    public function array(): array
    {
        return [
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Xác định cột cần dropdown - cột "Delivery Method" là cột P (16)
                $column = 'P'; // 1-based index: 16th column => P
                $rowStart = 2;
                $rowEnd = 1000; // Tùy ý: hỗ trợ đến dòng 1000

                $validation = $event->sheet->getDelegate()->getCell("{$column}{$rowStart}")->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setFormula1('"standard shipping,express shipping,international shipping"');

                // Áp dụng cho nhiều dòng
                for ($i = $rowStart; $i <= $rowEnd; $i++) {
                    $event->sheet->getDelegate()->getCell("{$column}{$i}")->setDataValidation(clone $validation);
                }
            },
        ];
    }
}
