<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashBookController extends Controller
{
    public function index()
    {
        $accounts = CashAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();
        $orderedAccounts = $this->sortAccountsHierarchically($accounts);

        return view('admin.cashbook.index', compact('orderedAccounts'));
    }

    public function save($id = null)
    {
        $voucherTypes = VoucherType::pluck('name', 'id')->toArray();
        $accounts = CashAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();
        $orderedAccounts = $this->sortAccountsHierarchically($accounts);

        $cashTransaction = null;
        if ($id) {
            $cashTransaction = CashTransaction::with(['cashAccount', 'voucherType', 'creator'])->findOrFail($id);
        }

        return view('admin.cashbook.save', compact('voucherTypes', 'orderedAccounts', 'cashTransaction'));
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'voucher_type_id' => 'nullable|exists:voucher_types,id',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'type' => 'required|string|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:20480',
        ]);

        // Xử lý lưu file nếu có đính kèm
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('attachments/cash_transactions', $filename, 'public');
            $data['attachment'] = "attachments/cash_transactions/$filename";
        }

        $data['created_by'] = Auth::id();
        $data['code'] = generateUniqueCode('cash_transactions'); // nếu cần sinh code tự động

        $cashTransaction = CashTransaction::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tạo phiếu thu/chi thành công.',
            'data' => $cashTransaction,
            'redirect' => '/admin/cashbook'
        ]);
    }

    public function update(Request $request, $id)
    {
        $cashTransaction = CashTransaction::findOrFail($id);

        $data = $request->validate([
            'date' => 'required|date',
            'voucher_type_id' => 'nullable|exists:voucher_types,id',
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'type' => 'required|string|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:20480',
        ]);

        // Xử lý lưu file nếu có đính kèm
        if ($request->hasFile('attachment')) {
            // Xóa file cũ nếu có
            if ($cashTransaction->attachment) {
                deleteImage($cashTransaction->attachment);
            }

            $file = $request->file('attachment');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('attachments/cash_transactions', $filename, 'public');
            $data['attachment'] = "attachments/cash_transactions/$filename";
        }

        if ($request->input('remove_attachment') == '1' && $cashTransaction->attachment) {
            deleteImage($cashTransaction->attachment);
            $cashTransaction->attachment = null;
        }

        $cashTransaction->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật phiếu thu/chi thành công.',
            'data' => $cashTransaction,
            'redirect' => '/admin/cashbook'
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:cash_transactions,id'
        ]);

        CashTransaction::whereIn('id', $request->ids)->delete();

        return successResponse(message: "Xóa phiếu thành công.", isResponse: true);
    }

    public function printMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        $transactions = CashTransaction::with(['voucherType', 'cashAccount', 'creator'])
            ->whereIn('id', $ids)
            ->get();

        return view('admin.cashbook.print', compact('transactions'));
    }

    public function list(Request $request)
    {
        $query = CashTransaction::with(['cashAccount', 'voucherType', 'creator']);

        // Xử lý date_range
        if ($request->filled('date_range')) {
            $dates = preg_split('/\s*[-–đến]+\s*/', $request->input('date_range'));
            if (count($dates) === 2) {
                try {
                    $start = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                    $end = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                    $query->whereBetween('date', [$start, $end]);
                } catch (\Exception $e) {
                    // Không lọc nếu lỗi format
                }
            }
        } else {
            $start = now()->startOfDay();
            $end = now()->addMonthNoOverflow()->startOfDay()->endOfDay();
            $query->whereBetween('date', [$start, $end]);
        }

        // Lọc theo account (cash / bank)
        if ($request->filled('account')) {
            $accountType = $request->input('account');
             $query->where('cash_account_id', $accountType);
            // if ($accountType === 'cash') {
            //     $query->where('cash_account_id', 9);
            // } elseif ($accountType === 'bank') {
            //     $query->where('cash_account_id', 10);
            // }
        }

        // Lọc theo voucher (yes / no)
        if ($request->filled('voucher')) {
            $voucherFilter = $request->input('voucher');
            if ($voucherFilter === 'yes') {
                $query->whereNotNull('voucher_type_id');
            } elseif ($voucherFilter === 'no') {
                $query->whereNull('voucher_type_id');
            }
        }

        // Lọc theo amount chính xác
        if ($request->filled('amount')) {
            $query->where('amount', $request->input('amount'));
        }

        $transactions = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.cashbook.table_rows', compact('transactions'))->render()
        ]);
    }


    public function voucherType(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|unique:voucher_types,name',
            'description' => 'nullable|string|max:255'
        ]);

        $voucherType = VoucherType::create($credentials);

        return response()->json([
            'data' => $voucherType,
            'success' => true
        ]);
    }
}
