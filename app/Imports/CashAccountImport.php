<?php

namespace App\Imports;

use App\Models\CashAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CashAccountImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $latestLevel1Id = null;
            $latestLevel2Id = null;

            foreach ($rows as $row) {
                $level = $row['level'];
                $code = $row['code'];
                $name = $row['ten'] ?? $row['name'];

                // Xác định parent_id theo cấp:
                if ($level == 1) {
                    $parentId = null;
                } elseif ($level == 2) {
                    $parentId = $latestLevel1Id;
                } elseif ($level == 3) {
                    $parentId = $latestLevel2Id;
                } else {
                    $parentId = null; // fallback nếu có sai dữ liệu
                }

                $cashAccount = CashAccount::create([
                    'code' => $code,
                    'name' => trim($name),
                    'level' => $level,
                    'parent_id' => $parentId,
                    'status' => 1,
                    'is_default' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Cập nhật biến lưu id cha gần nhất cho các cấp tiếp theo
                if ($level == 1) {
                    $latestLevel1Id = $cashAccount->id;
                    $latestLevel2Id = null; // reset khi gặp cấp 1 mới
                } elseif ($level == 2) {
                    $latestLevel2Id = $cashAccount->id;
                }
            }
        });
    }
}
