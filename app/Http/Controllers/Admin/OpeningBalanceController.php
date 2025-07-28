<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\MoneyAccount;
use App\Models\OpeningBalance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OpeningBalanceController extends Controller
{
    public function create()
    {

        return view('admin.opening_balances.create');
    }

    // public function store(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'transaction_date' => 'required|date_format:Y-m-d',
    //         'object_type' => 'required|in:customer,supplier',
    //         'type' => 'required|in:income,expense',
    //         'amount' => 'required|numeric|min:0',
    //         'description' => 'nullable|max:255',
    //         'object_id' => [
    //             'required',
    //             'integer',
    //             Rule::when($request->object_type === 'customer', ['exists:customers,id']),
    //             Rule::when($request->object_type === 'supplier', ['exists:suppliers,id']),
    //         ],
    //     ]);

    //     return transaction(function () use ($credentials) {
    //         $transaction = Transaction::create([
    //             'transaction_date' => $credentials['transaction_date'],
    //             'description' => $credentials['description'],
    //             'type' => 'other', // công nợ đầu kỳ
    //             'document_type' => 'opening_balance',
    //             'created_by' => Auth::guard('admin')->id(),
    //         ]);

    //         if ($credentials['object_type'] === 'customer') {
    //             $accountId = MoneyAccount::where('code', 131)->value('id');
    //             $contraAccountId = null;
    //         } else { // supplier
    //             $accountId = null;
    //             $contraAccountId = MoneyAccount::where('code', 331)->value('id');
    //         }

    //         $transaction->entries()->create([
    //             'account_id' => $accountId,
    //             'contra_account_id' => $contraAccountId,
    //             'customer_id' => $credentials['object_type'] === 'customer' ? $credentials['object_id'] : null,
    //             'supplier_id' => $credentials['object_type'] === 'supplier' ? $credentials['object_id'] : null,
    //             'debit_amount' => $credentials['type'] === 'income' ? $credentials['amount'] : 0,
    //             'credit_amount' => $credentials['type'] === 'expense' ? $credentials['amount'] : 0,
    //             'note' => 'Công nợ đầu kỳ',
    //         ]);

    //         $message = "Tạo công nợ đầu kỳ thành công.";
    //         sessionFlash('success', $message);

    //         $redirect = $credentials['object_type'] === 'customer'
    //             ? '/admin/debts/customer'
    //             : '/admin/debts/supplier';

    //         return successResponse(message: $message, isResponse: true, data: $redirect);
    //     });
    // }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'transaction_date' => 'required|date_format:Y-m-d',
            'object_type' => 'required|in:customer,supplier',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|max:255',
            'object_id' => [
                'required',
                'integer',
                Rule::when($request->object_type === 'customer', ['exists:customers,id']),
                Rule::when($request->object_type === 'supplier', ['exists:suppliers,id']),
            ],
        ]);

        return transaction(function () use ($credentials) {
            $transaction = Transaction::create([
                'transaction_date' => $credentials['transaction_date'],
                'description' => $credentials['description'],
                'type' => 'other', // phiếu công nợ đầu kỳ
                'created_by' => Auth::guard('admin')->id(),
            ]);

            // Xác định đối tượng (customer hoặc supplier)
            $tableableType = $credentials['object_type'] === 'customer'
                ? 'App\\Models\\Customer'
                : 'App\\Models\\Supplier';
            $tableableId = $credentials['object_id'];

            // Xác định tài khoản kế toán theo loại phiếu
            if ($credentials['type'] === 'income') {
                // Phiếu thu → công nợ phải thu KH
                $accountId = MoneyAccount::where('code', 131)->value('id');
                $debitAmount = $credentials['amount'];
                $creditAmount = 0;
            } else {
                // Phiếu chi → công nợ phải trả NCC
                $accountId = MoneyAccount::where('code', 331)->value('id');
                $debitAmount = 0;
                $creditAmount = $credentials['amount'];
            }

            $transaction->entries()->create([
                'account_id' => $accountId,
                'debit_amount' => $debitAmount,
                'credit_amount' => $creditAmount,
                'tableable_type' => $tableableType,
                'tableable_id' => $tableableId,
                'note' => 'Công nợ đầu kỳ',
            ]);

            $message = "Tạo công nợ đầu kỳ thành công.";

            $redirect = $credentials['object_type'] === 'customer'
                ? '/admin/debts/customer'
                : '/admin/debts/supplier';

            return successResponse(message: $message, isResponse: true, data: $redirect);
        });
    }
}
