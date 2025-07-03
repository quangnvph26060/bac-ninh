<?php

namespace App\Imports;

use App\Models\CashAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CashAccountImport implements ToCollection, WithHeadingRow, WithStartRow
{

    public function startRow(): int
    {
        return 10; // Bắt đầu từ hàng 6
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $code = trim($row['code']);
            $name = trim($row['name']);

            if (!$code || !$name) {
                continue; // bỏ qua dòng thiếu dữ liệu
            }

            // Xác định cha dựa theo code
            $parentCode = strlen($code) > 3 ? substr($code, 0, 3) : null;
            $parent = CashAccount::where('code', $parentCode)->first();

            // Xác định level
            $level = $parent ? $parent->level + 1 : 1;

            $node = new CashAccount([
                'code' => $code,
                'name' => $name,
                'status' => 1,
                'created_by' => null,
                'level' => $level,
            ]);

            if ($parent) {
                $node->appendToNode($parent)->save();
            } else {
                $node->saveAsRoot();
            }
        }
    }
}
