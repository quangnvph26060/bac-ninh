<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{

    public function customer(Request $request)
    {
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

        $customersQuery = DB::table('customers as c')
            ->select('c.id', 'c.name', 'c.code', 'c.phone');

        if ($nameFilter) {
            $customersQuery->where('c.name', 'like', "%$nameFilter%");
        }

        $debtReports = $customersQuery->get()
            ->map(function ($customer) use ($startDate, $endDate) {
                $so_du_no_dau = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Customer')
                    ->where('te.tableable_id', $customer->id)
                    ->where('t.transaction_date', '<', $startDate)
                    ->sum('te.debit_amount');

                $so_du_co_dau = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Customer')
                    ->where('te.tableable_id', $customer->id)
                    ->where('t.transaction_date', '<', $startDate)
                    ->sum('te.credit_amount');

                $ghi_no = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Customer')
                    ->where('te.tableable_id', $customer->id)
                    ->whereBetween('t.transaction_date', [$startDate, $endDate])
                    ->sum('te.debit_amount');

                $ghi_co = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Customer')
                    ->where('te.tableable_id', $customer->id)
                    ->whereBetween('t.transaction_date', [$startDate, $endDate])
                    ->sum('te.credit_amount');

                $so_du_rong = ($so_du_no_dau + $ghi_no) - ($so_du_co_dau + $ghi_co);

                return (object)[
                    'customer_code' => $customer->code,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->phone,
                    'opening_debit' => $so_du_no_dau,
                    'opening_credit' => $so_du_co_dau,
                    'period_debit' => $ghi_no,
                    'period_credit' => $ghi_co,
                    'ending_debit' => $so_du_rong > 0 ? $so_du_rong : 0,
                    'ending_credit' => $so_du_rong < 0 ? abs($so_du_rong) : 0,
                ];
            })
            ->filter(
                fn($i) =>
                $i->opening_debit || $i->opening_credit || $i->period_debit || $i->period_credit
            )
            ->values();

        if ($request->ajax()) {
            return response()->json($debtReports);
        }

        return view('admin.debt.customer', [
            'customerDebts' => $debtReports,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function supplier(Request $request)
    {
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

        $suppliersQuery = DB::table('suppliers as s')
            ->select('s.id', 's.name', 's.code', 's.phone');

        if ($nameFilter) {
            $suppliersQuery->where('s.name', 'like', "%$nameFilter%");
        }

        $supplierDebts = $suppliersQuery->get()
            ->map(function ($supplier) use ($startDate, $endDate) {
                $openingDebit = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Supplier')
                    ->where('te.tableable_id', $supplier->id)
                    ->where('t.transaction_date', '<', $startDate)
                    ->sum('te.debit_amount');

                $openingCredit = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Supplier')
                    ->where('te.tableable_id', $supplier->id)
                    ->where('t.transaction_date', '<', $startDate)
                    ->sum('te.credit_amount');

                $periodDebit = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Supplier')
                    ->where('te.tableable_id', $supplier->id)
                    ->whereBetween('t.transaction_date', [$startDate, $endDate])
                    ->sum('te.debit_amount');

                $periodCredit = DB::table('transaction_entries as te')
                    ->join('transactions as t', 't.id', '=', 'te.transaction_id')
                    ->where('te.tableable_type', 'App\\Models\\Supplier')
                    ->where('te.tableable_id', $supplier->id)
                    ->whereBetween('t.transaction_date', [$startDate, $endDate])
                    ->sum('te.credit_amount');

                $endingBalance = ($openingDebit + $periodDebit) - ($openingCredit + $periodCredit);

                return (object)[
                    'supplier_code' => $supplier->code,
                    'supplier_name' => $supplier->name,
                    'supplier_phone' => $supplier->phone,
                    'opening_debit' => $openingDebit,
                    'opening_credit' => $openingCredit,
                    'period_debit' => $periodDebit,
                    'period_credit' => $periodCredit,
                    'ending_debit' => $endingBalance > 0 ? $endingBalance : 0,
                    'ending_credit' => $endingBalance < 0 ? abs($endingBalance) : 0,
                ];
            })
            ->filter(
                fn($item) =>
                $item->opening_debit || $item->opening_credit || $item->period_debit || $item->period_credit
            )
            ->values();

        if ($request->ajax()) {
            return response()->json($supplierDebts);
        }

        return view('admin.debt.supplier', compact('supplierDebts', 'startDate', 'endDate'));
    }
}
