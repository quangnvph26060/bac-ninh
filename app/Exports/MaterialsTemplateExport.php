<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MaterialsTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MaterialsEmptySheet(), // Sheet nhập liệu
            // new MaterialsDataSheet(),  
        ];
    }
}


