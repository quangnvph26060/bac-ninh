<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SampleCashTransactionExport;
use App\Http\Controllers\Controller;
use App\Imports\CashTransactionImport;
use App\Models\MoneyAccount;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\User;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CashTransactionController extends Controller
{
    public function index()
    {
        $accounts = MoneyAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();
        $voucherTypes = VoucherType::query()->pluck('name', 'id')->toArray();
        // $orderedAccounts = $this->sortAccountsHierarchically($accounts);

        return view('admin.cash-transaction.index', compact('voucherTypes'));
    }

    public function save($id = null)
    {
        $voucherTypes = VoucherType::pluck('name', 'id')->toArray();
        $accounts = MoneyAccount::query()
            ->with('creator')
            ->orderBy('code') // hoặc sắp theo id nếu muốn
            ->get();
        $orderedAccounts = $this->sortAccountsHierarchically($accounts);

        $cashAccounts = MoneyAccount::query()
            ->whereHas('parent', function ($q) {
                $q->where('code', 111);
            })
            ->where('is_default', false)
            ->where('status', true)
            ->get();


        $cashTransaction = null;
        if ($id) {
            $cashTransaction = Receipt::with(['objectable', 'cashAccount', 'contraMoneyAccount', 'voucherType', 'creator'])->findOrFail($id);
        }

        return view('admin.cash-transaction.save', compact('voucherTypes', 'orderedAccounts', 'cashTransaction', 'cashAccounts'));
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

    public function search(Request $request)
    {
        $type = $request->input('type');
        $keyword = $request->input('keyword');

        if (!$type || strlen($keyword) < 3) {
            return response()->json([]);
        }

        $query = null;

        switch ($type) {
            case 'customer':
                $query = Customer::query()->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%");
                });
                break;

            case 'supplier':
                $query = Supplier::query()->where(function ($q) use ($keyword) {
                    $q->where('company_name', 'like', "%$keyword%");
                });
                break;

            case 'employee':
                $query = Employee::query()->where(function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%$keyword%");
                });
                break;
        }

        if (!$query) {
            return response()->json([]);
        }

        $results = $query->limit(10)->get()->map(function ($item) use ($type) {
            return [
                'id' => $item->id,
                'code' => $item->code ?? '',
                'name' => match ($type) {
                    'customer' => $item->name ?? '',
                    'supplier' => $item->company_name ?? '',
                    'employee' => $item->full_name ?? '',
                    default => '',
                },
                'phone' => $item->phone ?? '',
            ];
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'transaction_date' => ['required', 'date'],
            'object_type' => ['required', Rule::in(['customer', 'supplier', 'employee', 'other'])],
            'money_account_id' => ['required', 'exists:money_accounts,id'],
            'objectable_id' => [
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
            'type' => ['required', Rule::in(['income', 'expense'])],
            'voucher_type_id' => ['required', 'exists:voucher_types,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'file_path' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,webp'],
            'contra_money_account_id' => 'nullable|exists:money_accounts,id'
        ], __('request.messages'), [
            'transaction_date' => 'ngày giao dịch',
            'object_type' => 'loại đối tượng',
            'money_account_id' => 'tài khoản tiền',
            'objectable_id' => 'đối tượng',
            'type' => 'loại giao dịch',
            'voucher_type_id' => 'loại phiếu',
            'amount' => 'số tiền',
            'note' => 'ghi chú',
            'file_path' => 'tệp đính kèm',
            'contra_money_account_id' => 'tài khoản đối ứng'
        ]);

        // Map object_type => Model
        $modelMap = [
            'customer' => Customer::class,
            'supplier' => Supplier::class,
            'employee' => Employee::class,
        ];

        // Gán objectable_type
        if (isset($credentials['object_type']) && isset($modelMap[$credentials['object_type']])) {
            $credentials['objectable_type'] = $modelMap[$credentials['object_type']];
        } else {
            $credentials['objectable_type'] = null;
        }

        $credentials['created_by'] = auth('admin')->id();

        // Tự động gán tài khoản đối ứng gọn hơn
        $contraAccountId = match ($credentials['object_type']) {
            'customer' => MoneyAccount::where('code', '131')->value('id'),
            'supplier' => MoneyAccount::where('code', '331')->value('id'),
            default => null,
        };

        $credentials['contra_money_account_id'] ??= $contraAccountId;

        // Xử lý upload file nếu có
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('attachments/cash_transactions', $filename, 'public');
            $credentials['file_path'] = "attachments/cash_transactions/$filename";
        }

        Receipt::create($credentials);

        return response()->json([
            'success' => true,
            'message' => 'Tạo phiếu thu/chi thành công.',
            'redirect' => '/admin/cash-transactions'
        ]);
    }

    public function update(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);

        $credentials = $request->validate([
            'transaction_date' => ['required', 'date'],
            'object_type' => ['required', Rule::in(['customer', 'supplier', 'employee', 'other'])],
            'money_account_id' => ['required', 'exists:money_accounts,id'],
            'objectable_id' => [
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
            'type' => ['required', Rule::in(['income', 'expense'])],
            'voucher_type_id' => ['required', 'exists:voucher_types,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'file_path' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,webp'],
            'contra_money_account_id' => 'nullable|exists:money_accounts,id'
        ], __('request.messages'), [
            'transaction_date' => 'ngày giao dịch',
            'object_type' => 'loại đối tượng',
            'money_account_id' => 'tài khoản tiền',
            'objectable_id' => 'đối tượng',
            'type' => 'loại giao dịch',
            'voucher_type_id' => 'loại phiếu',
            'amount' => 'số tiền',
            'note' => 'ghi chú',
            'file_path' => 'tệp đính kèm',
            'contra_money_account_id' => 'tài khoản đối ứng',
        ]);

        // Map object_type => Model
        $modelMap = [
            'customer' => Customer::class,
            'supplier' => Supplier::class,
            'employee' => Employee::class,
        ];

        if (isset($credentials['object_type']) && isset($modelMap[$credentials['object_type']])) {
            $credentials['objectable_type'] = $modelMap[$credentials['object_type']];
        } else {
            $credentials['objectable_type'] = null;
        }

        // Tự động gán tài khoản đối ứng nếu chưa chọn
        $contraAccountId = match ($credentials['object_type']) {
            'customer' => MoneyAccount::where('code', '131')->value('id'),
            'supplier' => MoneyAccount::where('code', '331')->value('id'),
            default => null,
        };

        $credentials['contra_money_account_id'] ??= $contraAccountId;

        // Xử lý file đính kèm nếu có
        if ($request->hasFile('file_path')) {
            // Xóa file cũ nếu có
            if ($receipt->file_path) {
                deleteImage($receipt->file_path);
            }

            $file = $request->file('file_path');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('attachments/cash_transactions', $filename, 'public');
            $credentials['file_path'] = "attachments/cash_transactions/$filename";
        }

        // Xóa file khi user chọn xóa
        if ($request->input('remove_attachment') == '1' && $receipt->file_path) {
            deleteImage($receipt->file_path);
            $credentials['file_path'] = null;
        }

        $receipt->update($credentials);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật phiếu thu/chi thành công.',
            'redirect' => '/admin/cash-transactions'
        ]);
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:receipts,id'
        ]);

        Receipt::whereIn('id', $request->ids)->delete();

        return successResponse(message: "Xóa phiếu thành công.", isResponse: true);
    }

    public function printMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        $transactions = CashTransaction::with(['voucherType', 'cashAccount', 'creator'])
            ->whereIn('id', $ids)
            ->get();

        return view('admin.cash-transaction.print', compact('transactions'));
    }

    public function list(Request $request)
    {
        $query = Receipt::with(['objectable', 'cashAccount', 'contraMoneyAccount', 'voucherType', 'creator']);

        // Chỉ lấy các bản ghi là con của tài khoản code = 111 và is_default = 0
        $query->whereHas('cashAccount.parent', function ($q) {
            $q->where('code', '111');
        });

        // Xử lý date_range
        if ($request->filled('date_range')) {
            $dates = preg_split('/\s*[-–đến]+\s*/', $request->input('date_range'));
            if (count($dates) === 2) {
                try {
                    $start = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                    $end = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                    $query->whereBetween('transaction_date', [$start, $end]);
                } catch (\Exception $e) {
                    // Không lọc nếu lỗi format
                }
            }
        } else {
            $start = now()->startOfDay();
            $end = now()->addMonthNoOverflow()->startOfDay()->endOfDay();
            $query->whereBetween('transaction_date', [$start, $end]);
        }

        // Lọc theo account (cash / bank)
        if ($request->filled('account')) {
            $accountType = $request->input('account');
            $query->where('money_account_id', $accountType);
        }

        // Lọc theo voucher (yes / no)
        if ($request->filled('voucher')) {
            $voucherFilter = $request->input('voucher');
            $query->where('voucher_type_id', $voucherFilter);
        }

        // Lọc theo amount chính xác
        if ($request->filled('amount')) {
            $query->where('amount', $request->input('amount'));
        }

        $transactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'html' => view('admin.cash-transaction.table_rows', compact('transactions'))->render()
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

    public function downloadSample()
    {
        return Excel::download(new SampleCashTransactionExport, 'mau_import_phieu_thu_chi_' . date('d_m_Y') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new CashTransactionImport, $request->file('file'));

        return back()->with('success', 'Import thành công!');
    }
}
