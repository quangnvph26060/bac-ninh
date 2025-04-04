<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategorySheet implements FromCollection, WithTitle
{
    public function collection()
    {
        return Category::orderBy('name', 'asc')->pluck('name')->map(fn($name) => [$name]);
    }

    public function title(): string
    {
        return 'Danh mục';
    }
}
