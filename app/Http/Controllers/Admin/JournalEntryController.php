<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\DB;


class JournalEntryController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dateRange = $request->input('date_range');
            $nameFilter = $request->input('name');

            if ($dateRange) {
                [$start, $end] = explode(' - ', $dateRange);
                $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
            } else {
                $endDate = Carbon::now();
                $startDate = $endDate->copy()->subMonth()->startOfDay();
            }

            $transactions = DB::table('transactions as t')
                ->join('transaction_entries as te', 't.id', '=', 'te.transaction_id')
                ->join('money_accounts as acc', 'te.account_id', '=', 'acc.id')
                ->leftJoin('customers as c', function ($join) {
                    $join->on('te.tableable_id', '=', 'c.id')
                        ->where('te.tableable_type', '=', 'App\\Models\\Customer');
                })
                ->leftJoin('suppliers as s', function ($join) {
                    $join->on('te.tableable_id', '=', 's.id')
                        ->where('te.tableable_type', '=', 'App\\Models\\Supplier');
                })
                ->whereNotNull('te.id')
                ->whereBetween('t.transaction_date', [$startDate, $endDate])
                ->when($nameFilter, function ($query, $nameFilter) {
                    $query->where(function ($q) use ($nameFilter) {
                        $q->where('c.name', 'like', "%$nameFilter%")
                            ->orWhere('s.name', 'like', "%$nameFilter%")
                            ->orWhere('c.phone', 'like', "%$nameFilter%")
                            ->orWhere('s.phone', 'like', "%$nameFilter%");
                    });
                })
                ->select([
                    't.id as transaction_id',
                    't.transaction_date',
                    't.type as transaction_type',
                    't.document_type',
                    't.attachment',
                    DB::raw("MAX(CASE WHEN te.debit_amount > 0 THEN CONCAT(acc.code) END) as debit_account"),
                    DB::raw("MAX(CASE WHEN te.credit_amount > 0 THEN CONCAT(acc.code) END) as credit_account"),
                    DB::raw("MAX(te.debit_amount + te.credit_amount) as amount"),
                    DB::raw("MAX(CASE WHEN te.debit_amount > 0 THEN te.note ELSE '' END) as note"),
                    DB::raw("COALESCE(MAX(c.name), MAX(s.name)) as object_name"),
                    DB::raw("COALESCE(MAX(c.phone), MAX(s.phone)) as object_phone"),
                ])
                ->groupBy('t.id', 't.transaction_date', 't.type', 't.document_type', 't.attachment')
                ->orderByDesc('t.transaction_date')
                ->get();

            return response()->json([
                'success' => true,
                'html' => view('admin.journal-entries._table', compact('transactions'))->render()
            ]);
        }

        return view('admin.journal-entries.index');
    }

    public function destroy($id)
    {

        $query = JournalEntry::find($id);
        $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá thành công!'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có bản ghi nào được chọn.'
            ]);
        }

        JournalEntry::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá thành công ' . count($ids) . ' bản ghi.'
        ]);
    }

    public function ledgerTransactions(Request $request)
    {
        if ($request->ajax()) {
            $account = $request->input('account');

            if (!$account) {
                return response()->json([
                    'message' => 'Vui lòng chọn tài khoản để xem phát sinh đối ứng.'
                ], 422);
            }

            if ($request->filled('date_range')) {
                [$start, $end] = explode(' - ', $request->input('date_range'));
                $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
            } else {
                // Mặc định: từ ngày này tháng trước đến hôm nay
                $startDate = now()->subMonth()->startOfDay();
                $endDate = now()->endOfDay();
            }

            $entries = DB::table('journal_entries')
                ->select(
                    DB::raw("IF(debit_account = '$account', credit_account, debit_account) as doi_ung"),
                    DB::raw("SUM(CASE WHEN debit_account = '$account' THEN amount ELSE 0 END) as phat_sinh_no"),
                    DB::raw("SUM(CASE WHEN credit_account = '$account' THEN amount ELSE 0 END) as phat_sinh_co")
                )
                ->where(function ($q) use ($account) {
                    $q->where('debit_account', $account)
                        ->orWhere('credit_account', $account);
                })
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('doi_ung')
                ->get();

            $tong_phat_sinh_no = $entries->sum('phat_sinh_no');
            $tong_phat_sinh_co = $entries->sum('phat_sinh_co');

            // Tính dư đầu kỳ
            $du_dau_ky = DB::table('journal_entries')
                ->where(function ($q) use ($account) {
                    $q->where('debit_account', $account)
                        ->orWhere('credit_account', $account);
                })
                ->where('transaction_date', '<', $startDate)
                ->selectRaw("
            SUM(CASE WHEN debit_account = '$account' THEN amount ELSE 0 END)
            - SUM(CASE WHEN credit_account = '$account' THEN amount ELSE 0 END)
            as du_dau_ky
        ")
                ->value('du_dau_ky') ?? 0;

            $du_cuoi_ky = $du_dau_ky + $tong_phat_sinh_no - $tong_phat_sinh_co;

            return response()->json([
                'entries' => $entries,
                'tong_phat_sinh_no' => $tong_phat_sinh_no,
                'tong_phat_sinh_co' => $tong_phat_sinh_co,
                'du_dau_ky' => $du_dau_ky,
                'du_cuoi_ky' => $du_cuoi_ky,
            ]);
        }

        return view('admin.journal-entries.account_ledger');
    }
}
