<?php

namespace App\Imports;

use App\Models\CashTransaction;
use App\Models\VoucherType;
use App\Models\CashAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CashTransactionImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $rows = $rows->filter(function ($row) {
            return $row->filter()->isNotEmpty();
        });

        foreach ($rows as $row) {
            // Parse Loại chứng từ
            $voucherTypeId = null;
            if (!empty($row['loai_chung_tu'])) {
                $voucherTypeId = explode(' - ', $row['loai_chung_tu'])[0];
                if (!VoucherType::find($voucherTypeId)) {
                    continue;
                }
            }

            // Parse Tài khoản
            $cashAccount = null;
            if (!empty($row['tai_khoan'])) {
                $cashAccountCode = trim(explode(' - ', $row['tai_khoan'])[0]);
                $cashAccount = CashAccount::where('code', $cashAccountCode)->first();
                if (!$cashAccount) {
                    continue;
                }
            }

            // Parse ngày
            try {
                $dateValue = $row['ngay'];
                if (is_numeric($dateValue)) {
                    $date = ExcelDate::excelToDateTimeObject($dateValue)->format('Y/m/d');
                } else {
                    $date = Carbon::createFromFormat('d-m-Y', $dateValue)->format('Y/m/d');
                }
            } catch (\Exception $e) {
                continue;
            }

            // Xác định loại phiếu
            $type = null;
            if (!empty($row['loai_phieu'])) {
                $loaiPhieu = trim($row['loai_phieu']);
                if ($loaiPhieu === 'Phiếu thu') {
                    $type = 'income';
                } elseif ($loaiPhieu === 'Phiếu chi') {
                    $type = 'expense';
                } else {
                    continue;
                }
            } else {
                continue;
            }

            // Parse số tiền
            $amount = 0;
            if (!empty($row['so_tien'])) {
                $amount = floatval($row['so_tien']);
                if ($amount <= 0) {
                    continue;
                }
            } else {
                continue;
            }

            // Insert DB
            CashTransaction::create([
                'code' => $row['ma_phieu'] ?? generateUniqueCode('cash_transactions'),
                'date' => $date,
                'voucher_type_id' => $voucherTypeId,
                'cash_account_id' => $cashAccount->id ?? null,
                'amount' => $amount,
                'type' => $type,
                'description' => '',
                'attachment' => $row['file_chung_tu'] ?? '',
                'created_by' => auth('admin')->id(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '*.ma_phieu' => 'nullable|string|max:255',
            '*.ngay' => 'required',
            '*.loai_chung_tu' => 'nullable|string',
            '*.tai_khoan' => 'nullable|string',
            '*.loai_phieu' => 'required|in:Phiếu thu,Phiếu chi',
            '*.so_tien' => 'required|numeric|min:0.01',
            '*.file_chung_tu' => 'nullable|string',
        ];
    }
}
