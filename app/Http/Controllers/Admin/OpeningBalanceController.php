<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            OpeningBalance::create($credentials);

            $message = "Tạo công nợ đầu kỳ thành công.";

            return successResponse(message: $message, isResponse: true);
        });
    }
}
