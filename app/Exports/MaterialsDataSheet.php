<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MaterialsDataSheet implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    public function collection()
    {
        $materials = Material::select('code', 'name', 'unit', 'min_stock', 'note')->latest()->get();

        $data = [];

        foreach ($materials as $index => $material) {
            $data[] = [
                'STT' => $index + 1,
                'Mã NVL' => $material->code,
                'Tên' => $material->name,
                'Đơn vị' => $material->unit,
                'Số lượng báo động' => $material->min_stock,
                'Ghi chú' => $material->note,
            ];
        }
        return collect($data);
    }

    public function headings(): array
    {
        return ['STT', 'Mã NVL', 'Tên', 'Đơn vị', 'Số lượng báo động', 'Ghi chú'];
    }

    public function title(): string
    {
        return 'Dữ liệu hiện có';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Header style
                $sheet->getStyle("A1:F$highestRow")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);


                // Highlight rows where quantity_alert <= 100
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell("E$row")->getValue();
                    if ((int) $cellValue <= 100) {
                        $sheet->getStyle("A$row:F$row")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFFFC7CE'] // đỏ nhạt
                            ],
                            'font' => [
                                'color' => ['argb' => 'FF9C0006'] // đỏ đậm
                            ]
                        ]);
                    }
                }

                // Auto-size columns
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}

