<?php

namespace App\Traits;

// use Illuminate\Database\Eloquent\Builder;

trait PaginateTrait
{

    public function processDataTable($query, callable $customizeColumns, array $rawColumn = [], $method = 'eloquent')
    {
        $dataTable = DataTables()->$method($query);

        // Gọi callback để xử lý phần tùy chỉnh
        $dataTable = $customizeColumns($dataTable);

        $rawColumn[] = 'checkbox';

        // Kiểm tra nếu có cột cần xử lý raw
        if (!empty($rawColumn)) {
            // Đảm bảo rằng rawColumn là một mảng hợp lệ, không chứa các giá trị trống
            $validRawColumns = array_filter($rawColumn, fn($column) => !empty($column));
            $dataTable->rawColumns($validRawColumns);
        }

        return $dataTable
            ->addColumn('checkbox', fn($row) => "<input type='checkbox' class='row-checkbox form-check-input' value='{$row->id}'>")
            ->make(true);
    }
}
