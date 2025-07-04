<?php

namespace App\Exports;

use App\Models\VoucherType;
use App\Models\CashAccount;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SampleCashTransactionExport implements WithHeadings, WithEvents
{
    protected $voucherTypesList;
    protected $cashAccountsList;
    protected $receiptTypesList;

    public function __construct()
    {
        // Lấy voucher types
        $this->voucherTypesList = VoucherType::orderBy('id')
            ->get()
            ->map(function ($item) {
                return "{$item->id} - {$item->name}";
            })
            ->values()
            ->toArray();

        // Lấy toàn bộ cash accounts
        $accounts = CashAccount::select('id', 'code', 'name', 'parent_id')
            ->orderBy('code')
            ->get();

        $sortedAccounts = $this->sortAccountsHierarchically($accounts);

        $this->cashAccountsList = $sortedAccounts->map(function ($account) {
            return str_repeat(' ', $account->level_display * 4) . "{$account->code} - {$account->name}";
        })->values()->toArray();

        // Danh sách loại phiếu
        $this->receiptTypesList = ['Phiếu thu', 'Phiếu chi'];
    }

    private function sortAccountsHierarchically($accounts, $parentId = null, $level = 0)
    {
        $sorted = collect();

        foreach ($accounts->where('parent_id', $parentId) as $account) {
            $account->level_display = $level;
            $sorted->push($account);
            $children = $this->sortAccountsHierarchically($accounts, $account->id, $level + 1);
            $sorted = $sorted->merge($children);
        }

        return $sorted;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã phiếu',
            'Ngày',
            'Loại chứng từ',
            'Tài khoản',
            'Loại phiếu', // cột mới thay cho 2 cột Thu/Chi
            'Số tiền',
            'File chứng từ',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Dropdown cột D: Loại chứng từ
                $voucherTypesString = '"' . implode(',', $this->voucherTypesList) . '"';
                for ($row = 2; $row <= 100; $row++) {
                    $validation = $sheet->getCell("D{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($voucherTypesString);
                }

                // Dropdown cột E: Tài khoản
                $cashAccountsString = '"' . implode(',', $this->cashAccountsList) . '"';
                for ($row = 2; $row <= 100; $row++) {
                    $validation = $sheet->getCell("E{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($cashAccountsString);
                }

                // Dropdown cột F: Loại phiếu
                $receiptTypesString = '"' . implode(',', $this->receiptTypesList) . '"';
                for ($row = 2; $row <= 100; $row++) {
                    $validation = $sheet->getCell("F{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowDropDown(true);
                    $validation->setFormula1($receiptTypesString);
                }

                // Định dạng cột C (Ngày)
                $sheet->getStyle('C2:C100')
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');

                // Thêm ghi chú
                $sheet->setCellValue('I2', "Ghi chú:\n- Chọn đúng 'Loại phiếu' là Phiếu thu hoặc Phiếu chi.\n- Ngày định dạng yyyy-mm-dd.");
                $sheet->getStyle('I2')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
