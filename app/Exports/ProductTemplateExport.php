<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductSheet(),
            new CategorySheet(),
            new BrandSheet()
        ];
    }
}
