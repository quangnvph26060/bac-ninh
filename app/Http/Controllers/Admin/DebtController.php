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

        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
        } else {
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMonth()->startOfDay();
        }

        $query = DB::table('customers')
            ->select(
                'customers.id as customer_id',
                'customers.code as customer_code',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
            )
            // Tính opening_debit
            ->selectSub(function ($query) use ($startDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'customers.id')
                    ->where('object_type', 'customer')
                    ->where('type', 'income')
                    ->where('transaction_date', '<', $startDate->toDateString());
            }, 'opening_debit')
            // Tính opening_credit
            ->selectSub(function ($query) use ($startDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'customers.id')
                    ->where('object_type', 'customer')
                    ->where('type', 'expense')
                    ->where('transaction_date', '<', $startDate->toDateString());
            }, 'opening_credit')
            // Tính period_debit
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'customers.id')
                    ->where('object_type', 'customer')
                    ->where('type', 'income')
                    ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }, 'period_debit')
            // Tính period_credit
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'customers.id')
                    ->where('object_type', 'customer')
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }, 'period_credit');

        // Lọc theo customer_name nếu có (tìm tương đối)
        if ($request->filled('name')) {
            $query->where('customers.name', 'LIKE', '%' . $request->input('name') . '%');
        }

        $customerDebts = $query
            ->havingRaw('
            opening_debit != 0 OR
            opening_credit != 0 OR
            period_debit != 0 OR
            period_credit != 0
        ')
            ->get()
            ->map(function ($item) {
                $ending = $item->opening_debit + $item->period_debit - $item->opening_credit - $item->period_credit;

                if ($ending > 0) {
                    $item->ending_debit = $ending;
                    $item->ending_credit = 0;
                } elseif ($ending < 0) {
                    $item->ending_debit = 0;
                    $item->ending_credit = abs($ending);
                } else {
                    $item->ending_debit = 0;
                    $item->ending_credit = 0;
                }

                return $item;
            });

        if ($request->ajax()) {
            return response()->json($customerDebts);
        }

        return view('admin.debt.customer', compact('customerDebts', 'startDate', 'endDate'));
    }

    public function supplier(Request $request)
    {
        $dateRange = $request->input('date_range');

        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();
        } else {
            $endDate = Carbon::now();
            $startDate = $endDate->copy()->subMonth()->startOfDay();
        }

        $query = DB::table('suppliers')
            ->select(
                'suppliers.id as supplier_id',
                'suppliers.code as supplier_code',
                'suppliers.company_name as supplier_name',
                'suppliers.phone as supplier_phone',
            )
            // Tính opening_debit
            ->selectSub(function ($query) use ($startDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'suppliers.id')
                    ->where('object_type', 'supplier')
                    ->where('type', 'income')
                    ->where('transaction_date', '<', $startDate->toDateString());
            }, 'opening_debit')
            // Tính opening_credit
            ->selectSub(function ($query) use ($startDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'suppliers.id')
                    ->where('object_type', 'supplier')
                    ->where('type', 'expense')
                    ->where('transaction_date', '<', $startDate->toDateString());
            }, 'opening_credit')
            // Tính period_debit
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'suppliers.id')
                    ->where('object_type', 'supplier')
                    ->where('type', 'income')
                    ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }, 'period_debit')
            // Tính period_credit
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('opening_balances')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('object_id', 'suppliers.id')
                    ->where('object_type', 'supplier')
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }, 'period_credit');

        // Lọc theo supplier_name nếu có (tìm tương đối)
        if ($request->filled('name')) {
            $query->where('suppliers.name', 'LIKE', '%' . $request->input('name') . '%');
        }

        $supplierDebts = $query
            ->havingRaw('
            opening_debit != 0 OR
            opening_credit != 0 OR
            period_debit != 0 OR
            period_credit != 0
        ')
            ->get()
            ->map(function ($item) {
                $ending = $item->opening_debit + $item->period_debit - $item->opening_credit - $item->period_credit;

                if ($ending > 0) {
                    $item->ending_debit = $ending;
                    $item->ending_credit = 0;
                } elseif ($ending < 0) {
                    $item->ending_debit = 0;
                    $item->ending_credit = abs($ending);
                } else {
                    $item->ending_debit = 0;
                    $item->ending_credit = 0;
                }

                return $item;
            });

        if ($request->ajax()) {
            return response()->json($supplierDebts);
        }

        return view('admin.debt.supplier', compact('supplierDebts', 'startDate', 'endDate'));
    }
}
