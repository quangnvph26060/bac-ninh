<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CashAccountImport;
use App\Models\CashAccount;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.account.index');
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

    public function store(Request $request)
    {
        
    }
}
