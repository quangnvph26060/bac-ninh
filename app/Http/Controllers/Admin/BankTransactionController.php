<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\MoneyAccount;
use App\Models\Receipt;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BankTransactionController extends Controller
{
    public function index()
    {
        return view('admin.cash-bank.bank');
    }

    public function list(Request $request)
    {
        $dateRange = $request->query('date_range');
        $amounts = $request->query('amounts');

        $minAmount = null;
        $maxAmount = null;

        if ($amounts) {
            [$minRaw, $maxRaw] = array_pad(explode('-', $amounts), 2, null);
            $minAmount = is_numeric(trim($minRaw)) ? floatval(trim($minRaw)) : null;
            $maxAmount = is_numeric(trim($maxRaw)) ? floatval(trim($maxRaw)) : null;
        }

        if ($dateRange) {
            [$from, $to] = explode(' - ', $dateRange);
            $from = Carbon::createFromFormat('d/m/Y', trim($from))->toDateString();
            $to = Carbon::createFromFormat('d/m/Y', trim($to))->toDateString();
        } else {
            $from = now()->subMonth()->toDateString();
            $to = now()->toDateString();
        }

        // 👉 Lấy danh sách account_id của 112 và các con (ngân hàng)
        $bankAccountIds = DB::table('money_accounts')
            ->where(function ($q) {
                $q->where('code', '112')
                    ->orWhere('parent_id', function ($sub) {
                        $sub->select('id')->from('money_accounts')->where('code', '112')->limit(1);
                    });
            })
            ->pluck('id');

        $entries = DB::table('transactions as t')
            ->join('transaction_entries as te', 'te.transaction_id', '=', 't.id')
            ->join('money_accounts as ma', 'ma.id', '=', 'te.account_id')

            // Join lấy dòng đối ứng
            ->join('transaction_entries as te_contra', function ($q) {
                $q->on('te_contra.transaction_id', '=', 't.id')
                    ->whereColumn('te_contra.id', '!=', 'te.id');
            })
            ->join('money_accounts as contra_acc', 'contra_acc.id', '=', 'te_contra.account_id')

            // Lấy thông tin KH/NCC từ dòng đối ứng
            ->leftJoin('customers as c', function ($q) {
                $q->on('c.id', '=', 'te_contra.tableable_id')
                    ->where('te_contra.tableable_type', 'App\\Models\\Customer');
            })
            ->leftJoin('suppliers as s', function ($q) {
                $q->on('s.id', '=', 'te_contra.tableable_id')
                    ->where('te_contra.tableable_type', 'App\\Models\\Supplier');
            })
            ->join('users as u', 'u.id', '=', 't.created_by')

            ->where('t.type', '!=', 'other')
            ->whereIn('te.account_id', $bankAccountIds)
            ->whereBetween('t.transaction_date', [$from, $to])
            ->when(!is_null($minAmount), fn($q) => $q->havingRaw('SUM(te.debit_amount) >= ?', [$minAmount]))
            ->when(!is_null($maxAmount), fn($q) => $q->havingRaw('SUM(te.debit_amount) <= ?', [$maxAmount]))

            ->groupBy(
                't.id',
                't.transaction_date',
                't.reference_number',
                't.description',
                't.document_type',
                't.attachment',
                'u.name',
                'u.code'
            )
            ->select(
                't.id',
                't.transaction_date',
                't.reference_number',
                't.description',
                't.document_type',
                't.attachment',
                'u.name as creator_name',
                'u.code as creator_code',
                DB::raw("MAX(te.id) as entry_id"),
                DB::raw("COALESCE(MAX(c.name), MAX(s.name)) as related_party"),
                DB::raw("COALESCE(MAX(c.phone), MAX(s.phone)) as related_party_phone"),
                DB::raw("MAX(ma.code) as account_code"),
                DB::raw("MAX(ma.name) as account_name"),
                DB::raw("MAX(contra_acc.code) as contra_code"),
                DB::raw("MAX(contra_acc.name) as contra_name"),
                DB::raw("SUM(te.debit_amount) as debit_amount"),
                DB::raw("SUM(te.credit_amount) as credit_amount")
            )
            ->orderByDesc('t.transaction_date')
            ->orderByDesc('t.id')
            ->get();

        $type = 'bank';

        return response()->json([
            'success' => true,
            'html' => view('admin.cash-bank._table', compact('entries', 'type'))->render()
        ]);
    }

    public function save(Request $request)
    {
        $type = 'bank';
        $transaction = null;
        $mainEntry = null;
        $contraEntry = null;

        $transactionId = $request->input('transactionId', null);

        // Lấy danh sách tài khoản ngân hàng (con của 112)
        $moneyAccounts = MoneyAccount::query()
            ->whereHas('parent', function ($q) {
                $q->where('code', 112);
            })
            ->where('is_default', false)
            ->where('status', true)
            ->orderBy('code')
            ->get();

        $moneyAccountIds = $moneyAccounts->pluck('id')->toArray();

        if (!empty($transactionId)) {
            $transaction = Transaction::with('entries')->findOrFail($transactionId);

            // Kiểm tra xem transaction này có entry nào thuộc tài khoản ngân hàng không
            $hasBankAccount = $transaction->entries->contains(function ($entry) use ($moneyAccountIds) {
                return in_array($entry->account_id, $moneyAccountIds);
            });

            if (!$hasBankAccount) {
                // Không hợp lệ: transaction này không thuộc loại phiếu ngân hàng
                return redirect()->back()->with('error', 'Phiếu này không phải phiếu ngân hàng.');
            }

            // Lấy mainEntry: entry thuộc tài khoản ngân hàng
            $mainEntry = $transaction->entries->firstWhere(function ($entry) use ($moneyAccountIds) {
                return in_array($entry->account_id, $moneyAccountIds);
            });

            // Lấy contraEntry: entry còn lại
            $contraEntry = $transaction->entries->firstWhere(function ($entry) use ($moneyAccountIds) {
                return !in_array($entry->account_id, $moneyAccountIds);
            });
        }

        return view('admin.cash-bank.form', compact(
            'type',
            'moneyAccounts',
            'transaction',
            'mainEntry',
            'contraEntry'
        ));
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
        $credentials = $request->validate([
            'transaction_date'   => 'required|date_format:Y-m-d',
            'obj_type'           => ['required', Rule::in(['customer', 'supplier'])],
            'account_id'         => ['required', 'exists:money_accounts,id'], // tài khoản tiền
            'obj_id'             => [
                'required',
                'integer',
                Rule::when($request->obj_type === 'customer', ['exists:customers,id']),
                Rule::when($request->obj_type === 'supplier', ['exists:suppliers,id']),
            ],
            'type'               => ['required', Rule::in(['debit_notice', 'credit_notice'])],
            'amount'             => 'required|numeric|min:0',
            'description'        => 'nullable|string|max:255',
            'document_type'      => 'nullable|string|max:255',
            'attachment'         => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,webp'],
            'reference_number'   => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($credentials, $request) {
            $credentials['created_by'] = Auth::guard('admin')->id();

            // Nếu là phiếu chi, kiểm tra số dư tài khoản tiền
            if ($credentials['type'] === 'debit_notice') {
                $balance = $this->getClosingBalanceByCode($credentials['account_id']);

                if (!$balance['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $balance['message']
                    ], 400);
                }

                $availableAmount = $balance['closing_balance_debit'] - $balance['closing_balance_credit'];

                if ($availableAmount < $credentials['amount']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản không đủ số dư để chi số tiền này.'
                    ], 400);
                }
            }

            // Xử lý file đính kèm nếu có
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('attachments/cash_transactions', $filename, 'public');
                $credentials['attachment'] = "attachments/cash_transactions/$filename";
            }

            // Tạo phiếu (transaction)
            $transaction = Transaction::create([
                'transaction_date'   => $credentials['transaction_date'],
                'description'        => $credentials['description'] ?? null,
                'reference_number'   => $credentials['reference_number'] ?? null,
                'type'               => $credentials['type'],
                'document_type'      => $credentials['document_type'] ?? null,
                'attachment'         => $credentials['attachment'] ?? null,
                'created_by'         => $credentials['created_by'],
            ]);

            // Xác định đối tượng
            $tableableType = $credentials['obj_type'] === 'customer'
                ? 'App\\Models\\Customer'
                : 'App\\Models\\Supplier';
            $tableableId = $credentials['obj_id'];

            // Tự xác định tài khoản đối ứng theo type + obj_type
            $contraCode = match ([$credentials['type'], $credentials['obj_type']]) {
                ['credit_notice', 'customer']  => '131',
                ['credit_notice', 'supplier']  => '331',
                ['debit_notice', 'customer'] => '131',
                ['debit_notice', 'supplier'] => '331',
            };

            $contraAccountId = MoneyAccount::where('code', $contraCode)->value('id');

            if (!$contraAccountId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản đối ứng phù hợp.'
                ], 400);
            }

            $amount = $credentials['amount'];

            // Tạo 2 bản ghi entries
            if ($credentials['type'] === 'credit_notice') {
                // Báo Nợ: tiền tăng Nợ, công nợ giảm Có
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $credentials['account_id'],
                    'debit_amount'     => $amount,
                    'credit_amount'    => 0,
                ]);
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $contraAccountId,
                    'debit_amount'     => 0,
                    'credit_amount'    => $amount,
                    'tableable_type'   => $tableableType,
                    'tableable_id'     => $tableableId,
                ]);
            } else {
                // Báo Có: tiền giảm Có, công nợ tăng Nợ
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $credentials['account_id'],
                    'debit_amount'     => 0,
                    'credit_amount'    => $amount,
                ]);
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $contraAccountId,
                    'debit_amount'     => $amount,
                    'credit_amount'    => 0,
                    'tableable_type'   => $tableableType,
                    'tableable_id'     => $tableableId,
                ]);
            }

            return response()->json([
                'success'  => true,
                'message'  => 'Tạo phiếu thu/chi thành công.',
                'redirect' => '/admin/bank-transactions'
            ]);
        });
    }

    private function getClosingBalanceByCode($accountId)
    {
        if (!$accountId) {
            return [
                'success' => false,
                'message' => 'Vui lòng cung cấp tài khoản.'
            ];
        }

        $query = "
        SELECT
            ma.id,
            ma.code,
            ma.name,
            GREATEST(SUM(COALESCE(te.debit_amount, 0)) - SUM(COALESCE(te.credit_amount, 0)), 0) AS closing_balance_debit,
            GREATEST(SUM(COALESCE(te.credit_amount, 0)) - SUM(COALESCE(te.debit_amount, 0)), 0) AS closing_balance_credit

        FROM money_accounts ma
        LEFT JOIN transaction_entries te
            ON te.account_id = ma.id
            AND te.tableable_type IS NULL
            AND te.tableable_id IS NULL
        LEFT JOIN transactions t
            ON t.id = te.transaction_id
            AND t.type != 'other'

        WHERE ma.id = ?
        GROUP BY ma.id, ma.code, ma.name
        LIMIT 1
    ";

        $result = DB::selectOne($query, [$accountId]);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.'
            ];
        }

        return [
            'success' => true,
            'account_code' => $result->code,
            'account_name' => $result->name,
            'closing_balance_debit' => $result->closing_balance_debit,
            'closing_balance_credit' => $result->closing_balance_credit,
        ];
    }

    public function update(Request $request)
    {
        $transactionId = $request->input('transaction_id');

        $credentials = $request->validate([
            'transaction_id'     => 'required|integer|exists:transactions,id',
            'transaction_date'   => 'required|date_format:Y-m-d',
            'obj_type'           => ['required', Rule::in(['customer', 'supplier'])],
            'account_id'         => ['required', 'exists:money_accounts,id'], // tài khoản tiền
            'obj_id'             => [
                'required',
                'integer',
                Rule::when($request->obj_type === 'customer', ['exists:customers,id']),
                Rule::when($request->obj_type === 'supplier', ['exists:suppliers,id']),
            ],
            'type'               => ['required', Rule::in(['debit_notice', 'credit_notice'])],
            'amount'             => 'required|numeric|min:0',
            'description'        => 'nullable|string|max:255',
            'document_type'      => 'nullable|string|max:255',
            'attachment'         => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,webp'],
            'reference_number'   => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($credentials, $request, $transactionId) {
            $transaction = Transaction::findOrFail($transactionId);

            // Nếu là phiếu chi, kiểm tra số dư tài khoản tiền
            if ($credentials['type'] === 'debit_notice') {
                $balance = $this->getClosingBalanceByCode($credentials['account_id']);

                if (!$balance['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => $balance['message']
                    ], 400);
                }

                $availableAmount = $balance['closing_balance_debit'] - $balance['closing_balance_credit'];

                if ($availableAmount < $credentials['amount']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tài khoản không đủ số dư để chi số tiền này.'
                    ], 400);
                }
            }

            // Xử lý file đính kèm
            if ($request->hasFile('attachment')) {
                if ($transaction->attachment) {
                    deleteImage($transaction->attachment);
                }
                $file = $request->file('attachment');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('attachments/cash_transactions', $filename, 'public');
                $credentials['attachment'] = "attachments/cash_transactions/$filename";
            }
            if ($request->input('remove_attachment') == '1' && $transaction->attachment) {
                deleteImage($transaction->attachment);
                $credentials['attachment'] = null;
            }

            // Update transaction
            $transaction->update([
                'transaction_date'   => $credentials['transaction_date'],
                'description'        => $credentials['description'] ?? null,
                'reference_number'   => $credentials['reference_number'] ?? null,
                'type'               => $credentials['type'],
                'document_type'      => $credentials['document_type'] ?? null,
                'attachment'         => $credentials['attachment'] ?? null,
            ]);

            // Tự xác định tài khoản đối ứng theo type + obj_type
            $contraCode = match ([$credentials['type'], $credentials['obj_type']]) {
                ['credit_notice', 'customer']  => '131',
                ['credit_notice', 'supplier']  => '331',
                ['debit_notice', 'customer'] => '131',
                ['debit_notice', 'supplier'] => '331',
            };

            $contraAccountId = MoneyAccount::where('code', $contraCode)->value('id');

            if (!$contraAccountId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản đối ứng phù hợp.'
                ], 400);
            }

            $tableableType = $credentials['obj_type'] === 'customer'
                ? 'App\\Models\\Customer'
                : 'App\\Models\\Supplier';
            $tableableId = $credentials['obj_id'];

            $amount = $credentials['amount'];

            // Xóa entries cũ
            $transaction->entries()->delete();

            // Tạo lại entries mới
            if ($credentials['type'] === 'credit_notice') {
                // Báo Nợ: tiền tăng Nợ, công nợ giảm Có
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $credentials['account_id'],
                    'debit_amount'     => $amount,
                    'credit_amount'    => 0,
                ]);
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $contraAccountId,
                    'debit_amount'     => 0,
                    'credit_amount'    => $amount,
                    'tableable_type'   => $tableableType,
                    'tableable_id'     => $tableableId,
                ]);
            } else {
                // Báo Có: tiền giảm Có, công nợ tăng Nợ
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $credentials['account_id'],
                    'debit_amount'     => 0,
                    'credit_amount'    => $amount,
                ]);
                TransactionEntry::create([
                    'transaction_id'   => $transaction->id,
                    'account_id'       => $contraAccountId,
                    'debit_amount'     => $amount,
                    'credit_amount'    => 0,
                    'tableable_type'   => $tableableType,
                    'tableable_id'     => $tableableId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phiếu thu/chi thành công.',
                'redirect' => '/admin/bank-transactions'
            ]);
        });
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id',
        ]);

        return DB::transaction(function () use ($request) {
            $transactionIds = $request->input('ids');

            foreach ($transactionIds as $transactionId) {
                $transaction = Transaction::find($transactionId);
                if ($transaction) {
                    // Xóa file nếu có
                    if ($transaction->attachment) {
                        deleteImage($transaction->attachment);
                    }
                    // Xóa transaction
                    $transaction->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu thành công.'
            ]);
        });
    }
}
