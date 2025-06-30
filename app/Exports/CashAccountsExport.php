<?php

namespace App\Exports;

use App\Models\CashAccount;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CashAccountsExport implements FromArray, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function array(): array
    {
        $data = [];
        $accounts = CashAccount::orderBy('parent_id')->orderBy('id')->get();

        $ordered = $this->buildTreeAndFlatten($accounts);

        foreach ($ordered as $account) {
            $indent = str_repeat('    ', $account->level_display ?? 0);

            $data[] = [
                $account->id,
                $indent . (string) $account->code,
                $indent . $account->name,
                $account->status ? 'kích hoạt' : 'Ngừng',
                $account->creator?->full_name ?? '',
                $account->created_at ? $account->created_at->format('d/m/Y H:i') : '',
            ];
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // Cột Code luôn dạng text
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Tên',
            'Tình trạng',
            'Người tạo',
            'Ngày tạo',
        ];
    }

    protected function buildTreeAndFlatten($accounts, $parentId = null, $level = 0)
    {
        $result = [];

        foreach ($accounts->where('parent_id', $parentId) as $account) {
            $account->level_display = $level;
            $result[] = $account;

            $children = $this->buildTreeAndFlatten($accounts, $account->id, $level + 1);
            $result = array_merge($result, $children);
        }

        return $result;
    }
}
