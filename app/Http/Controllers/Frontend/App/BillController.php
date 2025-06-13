<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\ConfigPayment;
use App\Models\TopupRequest;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\PaypalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillController extends Controller
{
    // public $paypalService;
    // public function __construct(PaypalService $paypalService)
    // {
    //     $this->paypalService = $paypalService;
    // }

    public function bill(Request $request)
    {
        $search = $request->search;
        $perPage = $request->input('per_page', 10);
        $dateRange = $request->date_range;
        $isTopupRequest = filter_var($request->is_topup_request, FILTER_VALIDATE_BOOLEAN);

        $configPayments = ConfigPayment::query()->with('bank')->latest()->get();
        $wallet = Wallet::query()->where('user_id', auth()->id())->first();

        $walletTransactions = WalletTransaction::query()
            ->with('configPayment')
            ->where('wallet_id', $wallet->id)
            // Nếu là yêu cầu nạp tiền, thêm điều kiện is_topup_request
            ->when($isTopupRequest, function ($query) {
                $query->where('is_topup_request', true);
            })
            // Nếu không phải yêu cầu nạp tiền, thay thế bằng status là "complete"
            ->when(!$isTopupRequest, function ($query) {
                $query->where('status', 'complete');
            })
            ->latest()
            ->when(!empty($search), fn($q) => $q->where('code', 'like', "%{$search}%"))
            ->when(!empty($dateRange), function ($q) use ($dateRange) {
                [$start, $end] = explode(' - ', $dateRange);
                $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                $q->whereBetween('created_at', [$start, $end]);
            })->paginate($perPage);

        if ($request->ajax()) {
            $view = $isTopupRequest ? 'frontend.app.bill.request-deposit-table' : 'frontend.app.bill.transaction-history-table';
            $html = view($view, compact('walletTransactions'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        return view('frontend.app.bill.index', compact('configPayments'));
    }


    public function process(Request $request)
    {
        $credentials = $request->validate([
            'amount' => 'required|numeric',
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'transaction_code' => 'required|string|max:255|unique:wallet_transactions,transaction_code',
            'note' => 'nullable|string|max:255',
            'config_payment_id' => 'required|exists:config_payments,id'
        ], [], [
            'amount' => 'Amount',
            'proof' => 'Proof',
            'transaction_code' => 'Transaction Code',
            'note' => 'Note',
            'config_payment_id' => 'Config Payment'
        ]);

        $wallet = Wallet::query()->where('user_id', auth()->id())->first();

        $balance_after = $wallet->balance + $credentials['amount'];

        $credentials['proof'] = uploadImages('proof', 'proof');

        try {
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'config_payment_id' => $credentials['config_payment_id'],
                'code' => generateTransactionCode(),
                'amount' => $credentials['amount'],
                'note' => $credentials['note'],
                'type' => 'deposit',
                'balance_before' => $wallet->balance,
                'balance_after' => $balance_after,
                'status' => 'pending',
                'proof' => $credentials['proof'],
                'transaction_code' => $credentials['transaction_code'],
                'is_topup_request' => true
            ]);
            return successResponse('The topup request has been sent, please wait for the browser.', [], 200, true);
        } catch (\Throwable $th) {
            deleteImage($credentials['proof']);
            logger($th->getMessage());
            return errorResponse('An error occurred, please try again later!', true);
        }
    }

    public function generateQr(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'note' => 'nullable|max:255',
            'accountNumber' => 'required|numeric',
            'bin' => 'required|exists:banks,bin',
            'contentBank' => 'required|string'
        ]);

        try {
            // Tạo chuỗi khóa cache duy nhất (bao gồm cả ID người dùng để đảm bảo cache là riêng biệt cho từng người)
            $cacheKey = 'qr_' . md5($request->bin . $request->accountNumber . $request->amount . $request->contentBank . auth()->id());
            logger($cacheKey);

            // Kiểm tra nếu cache đã tồn tại, xoá cache cũ trước khi lưu cache mới
            if (Cache::has($cacheKey)) {
                // Xóa cache cũ để reset thời gian
                Cache::forget($cacheKey);
            }

            // Tạo URL QR code
            $qrUrl = "https://img.vietqr.io/image/{$request->bin}-{$request->accountNumber}-compact.png?amount={$request->amount}&addInfo={$request->contentBank}";

            // Lưu URL QR vào cache trong 5 phút
            Cache::put($cacheKey, [
                'amount' => $request->amount,
                'accountNumber' => $request->accountNumber,
                'contentBank' => $request->contentBank
            ], now()->addMinutes(5));

            // Trả về URL mã QR
            return successResponse('Tạo mã QR thành công.', ['qrUrl' => $qrUrl], 200, true);
        } catch (\Exception $e) {
            // Log lỗi và trả về thông báo lỗi cho người dùng
            logger($e->getMessage());
            return errorResponse('Đã có lỗi xảy ra, vui lòng thử lại sau!', true);
        }
    }


    public function confirmTransfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'note' => 'nullable|max:255',
            'bin' => 'required|exists:banks,bin',
            'accountNumber' => 'required|numeric',
            'contentBank' => 'required|string'
        ]);

        try {
            // Tạo chuỗi khóa cache tương ứng
            $cacheKey = 'qr_' . md5($request->bin . $request->accountNumber . $request->amount . $request->contentBank . auth()->id());
            logger($cacheKey);

            // Lấy thông tin đã lưu trong cache
            $cachedData = Cache::get($cacheKey);

            if (!$cachedData) {
                return errorResponse('Lỗi: Phiên giao dịch đã hết hạn hoặc bị thay đổi.', true);
            }

            // So sánh thông tin gửi lên với thông tin trong cache
            if ($cachedData['amount'] !== $request->amount || $cachedData['accountNumber'] !== $request->accountNumber || $cachedData['contentBank'] !== $request->contentBank) {
                return errorResponse('Thông tin giao dịch không khớp. Vui lòng kiểm tra lại.', true);
            }

            $wallet = Wallet::firstOrCreate(
                ['user_id' => auth()->id()],
                ['balance' => 0]
            );

            // Ghi nhận balance hiện tại
            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $request->amount; // Dự kiến sau khi admin duyệt

            WalletTransaction::create(
                [
                    'wallet_id' => $wallet->id,
                    'code' => $request->contentBank,
                    'amount' => $request->amount,
                    'bank_account' => $request->accountNumber,
                    'note' => $request->note,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                ]
            );

            Cache::forget($cacheKey);

            return successResponse('Xác nhận giao dịch thành công.', [], 200, true);
        } catch (\Exception $e) {
            logger($e->getMessage());
            return errorResponse('Đã có lỗi xảy ra, vui lòng thử lại sau!', true);
        }
    }

    public function cancelTransaction()
    {
        return back()
            ->with('error', 'You have canceled the transaction.');
    }
}
