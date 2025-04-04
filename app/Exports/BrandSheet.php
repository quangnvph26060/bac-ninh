<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class BrandSheet implements FromCollection, WithTitle
{
    public function collection()
    {
        return Brand::orderBy('name', 'asc')->pluck('name')->map(fn($name) => [$name]);
    }

    public function title(): string
    {
        return 'Thương hiệu';
    }
}
