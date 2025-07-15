<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OpeningBalanceController extends Controller
{
    public function create()
    {

        return view('admin.opening_balances.create');
    }

    public function store(Request $request)
    {

        $credentials = $request->validate([
            'transaction_date' => 'required|date_format:Y-m-d',
            'object_type' => 'required|in:customer,supplier,employee',
            'type' => 'required|in:income,expense',
            'object_id' => [
                'required',
                'integer',
                Rule::when(
                    $request->object_type === 'customer',
                    ['exists:customers,id'],
                ),
                Rule::when(
                    $request->object_type === 'supplier',
                    ['exists:suppliers,id'],
                ),
                Rule::when(
                    $request->object_type === 'employee',
                    ['exists:employees,id'],
                ),
            ],
            'amount' => 'required|numeric',
            'note' => 'nullable|max:255'
        ]);

        return transaction(function () use ($credentials) {
            $openingBalance  =  OpeningBalance::create($credentials);

            JournalEntry::create([
                'object_type' => $credentials['object_type'],
                'amount' => $credentials['amount'],
                'debit_account' => $credentials['object_type'] === 'customer' ? 131 : 331,
                'note' => $credentials['note'],
                'related_type' => 'opening_balance',
                'related_id' => $openingBalance->id
            ]);

            $message = "Tạo công nợ đầu kỳ thành công.";

            sessionFlash('success', $message);

            $redirect = $credentials['object_type'] === 'customer' ? '/admin/debts/customer' : '/admin/debts/supplier';

            return successResponse(message: $message, isResponse: true, data: $redirect);
        });
    }
}
