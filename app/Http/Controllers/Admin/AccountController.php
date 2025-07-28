<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CashAccountsExport;
use App\Http\Controllers\Controller;
use App\Imports\CashAccountImport;
use App\Models\MoneyAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AccountController extends Controller
{
    public function index()
    {
        $moneyAccounts = MoneyAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();

        $orderedAccounts = $this->sortAccountsHierarchically($moneyAccounts);

        return view('admin.account.index', compact('orderedAccounts'));
    }

    public function accountBalance(Request $request)
    {
        if ($request->ajax()) {
            $dateRange = $request->input('dateRange');
            $searchInput = trim($request->input('searchInput'));

            if ($dateRange) {
                [$start, $end] = explode(' - ', $dateRange);
                $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
            } else {
                $endDate = Carbon::now()->endOfDay();
                $startDate = $endDate->copy()->subMonth()->startOfDay();
            }

            $query = "
            WITH RECURSIVE account_tree AS (
                SELECT ma.id, ma.code, ma.name, ma.parent_id, ma.level, CAST(ma.code AS CHAR(255)) AS path
                FROM money_accounts ma
                WHERE ma.parent_id IS NULL

                UNION ALL

                SELECT child.id, child.code, child.name, child.parent_id, child.level, CONCAT(parent.path, '-', child.code) AS path
                FROM money_accounts child
                JOIN account_tree parent ON child.parent_id = parent.id
            )
            SELECT
                at.id AS account_id,
                at.code AS account_code,
                at.name AS account_name,
                at.level,
                at.path,

                GREATEST(COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.debit_amount ELSE 0 END),0),0) AS opening_debit,
                GREATEST(COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.credit_amount ELSE 0 END),0),0) AS opening_credit,

                GREATEST(COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.debit_amount ELSE 0 END),0),0) AS period_debit,
                GREATEST(COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.credit_amount ELSE 0 END),0),0) AS period_credit,

                GREATEST((
                    COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.debit_amount ELSE 0 END),0)
                    + COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.debit_amount ELSE 0 END),0)
                    - COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.credit_amount ELSE 0 END),0)
                    - COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.credit_amount ELSE 0 END),0)
                ),0) AS closing_balance_debit,

                GREATEST((
                    COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.credit_amount ELSE 0 END),0)
                    + COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.credit_amount ELSE 0 END),0)
                    - COALESCE(SUM(CASE WHEN t.transaction_date < ? THEN te.debit_amount ELSE 0 END),0)
                    - COALESCE(SUM(CASE WHEN t.transaction_date BETWEEN ? AND ? THEN te.debit_amount ELSE 0 END),0)
                ),0) AS closing_balance_credit

            FROM account_tree at
                LEFT JOIN transaction_entries te
                ON te.account_id = at.id
                AND te.tableable_type IS  NULL
                AND te.tableable_id IS  NULL
            LEFT JOIN transactions t
                ON t.id = te.transaction_id
                AND t.type != 'other'

            WHERE (at.code LIKE ? OR at.name LIKE ?)
            GROUP BY at.id, at.code, at.name, at.level, at.path
            ORDER BY at.path
            ";

            $bindings = [
                $startDate,
                $startDate,
                $startDate,
                $endDate,
                $startDate,
                $endDate,
                $startDate,
                $startDate,
                $endDate,
                $startDate,
                $startDate,
                $endDate,
                $startDate,
                $startDate,
                $endDate,
                $startDate,
                $startDate,
                $endDate,
                "%{$searchInput}%",
                "%{$searchInput}%"
            ];

            $accounts = DB::select($query, $bindings);

            return response()->json([
                'success' => true,
                'html' => view('admin.account.balance_table', compact('accounts'))->render()
            ]);
        }

        return view('admin.account.balance');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|string|max:255|unique:cash_accounts,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cash_accounts,id',
            'status' => 'nullable|in:1',
        ]);

        $credentials['created_by'] = Auth::id();

        if (!empty($credentials['parent_id'])) {
            $parent  = MoneyAccount::query()->findOrFail($credentials['parent_id']);
            $credentials['level'] = $parent->level + 1;
        }

        MoneyAccount::create($credentials);

        return handleResponse('Tạo tài khoản kế toán thành công', true, 201, $credentials, false);
    }

    public function update(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|string|max:255|unique:cash_accounts,code,' . $request->id,
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cash_accounts,id',
            'status' => 'nullable|in:1',
        ]);

        $credentials['status'] ??= 0;

        $cashAccount = MoneyAccount::query()->findOrFail($request->id);

        if (!empty($credentials['parent_id'])) {
            $parent = MoneyAccount::query()->findOrFail($credentials['parent_id']);
            $credentials['level'] = $parent->level + 1;
        } else {
            $credentials['level'] = 0;
        }

        $cashAccount->update($credentials);

        return handleResponse('Cập nhật tài khoản kế toán thành công', true, 200, $credentials, false);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:cash_accounts,id',
        ]);

        MoneyAccount::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá các tài khoản kế toán đã chọn thành công.'
        ]);
    }

    public function export()
    {
        $filename = 'cash_accounts_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new CashAccountsExport, $filename);
    }

    private function sortAccountsHierarchically($accounts, $parentId = null, $level = 0)
    {
        $sorted = collect();

        foreach ($accounts->where('parent_id', $parentId) as $account) {
            $account->level_display = $level; // nếu cần thụt lề
            $sorted->push($account);
            $children = $this->sortAccountsHierarchically($accounts, $account->id, $level + 1);
            $sorted = $sorted->merge($children);
        }

        return $sorted;
    }

    public function list(Request $request)
    {
        $moneyAccounts = MoneyAccount::query()
            ->with('creator')
            ->get();

        $orderedAccounts = $this->sortAccountsHierarchically($moneyAccounts);

        // Filter sau khi đã sort để giữ cấu trúc cây
        if ($request->filled('keyword')) {
            $keyword = mb_strtolower($request->input('keyword'));
            $orderedAccounts = $orderedAccounts->filter(function ($account) use ($keyword) {
                return str_contains(mb_strtolower($account->code), $keyword)
                    || str_contains(mb_strtolower($account->name), $keyword);
            });
        }

        return response()->json([
            'success' => true,
            'html' => view('admin.account.table_rows', compact('orderedAccounts'))->render(),
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new CashAccountImport, $request->file('file'));

        return back()->with('success', 'Import tài khoản kế toán thành công.');
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        $accounts = MoneyAccount::where('code', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'code', 'name']);

        return response()->json($accounts);
    }
}
