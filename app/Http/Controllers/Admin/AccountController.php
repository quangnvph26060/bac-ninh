<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CashAccountsExport;
use App\Http\Controllers\Controller;
use App\Imports\CashAccountImport;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AccountController extends Controller
{
    public function index()
    {
        $cashAccounts = CashAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();

        $orderedAccounts = $this->sortAccountsHierarchically($cashAccounts);

        return view('admin.account.index', compact('orderedAccounts'));
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
            $parent  = CashAccount::query()->findOrFail($credentials['parent_id']);
            $credentials['level'] = $parent->level + 1;
        }

        CashAccount::create($credentials);

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

        $cashAccount = CashAccount::query()->findOrFail($request->id);

        if (!empty($credentials['parent_id'])) {
            $parent = CashAccount::query()->findOrFail($credentials['parent_id']);
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

        CashAccount::whereIn('id', $request->ids)->delete();

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

    public function list()
    {
        $cashAccounts = CashAccount::query()
            ->with('creator')
            ->get();

        $orderedAccounts = $this->sortAccountsHierarchically($cashAccounts);

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
        $accounts = CashAccount::where('code', 'like', "%$q%")
            ->orWhere('name', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'code', 'name']);

        return response()->json($accounts);
    }
}
