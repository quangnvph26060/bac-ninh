<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\ConfigPayment;
use App\Models\TransactionHistory;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\PaypalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BillController extends Controller
{
    public $paypalService;
    public function __construct(PaypalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function bill()
    {
        $configPayments = ConfigPayment::query()->with('bank')->latest()->get();
        return view('frontend.app.bill.index', compact('configPayments'));
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

    public function processTransaction(Request $request)
    {
        $request->validate(
            [
                'amount' => 'required|numeric',
                'note' => 'nullable|string|max:255'
            ],
            __('request.messages'),
            [
                'amount' => 'Số tiền',
                'note' => 'Ghi chú'
            ]
        );

        try {
            $response = $this->paypalService->createOrder(
                $request->amount,
                'USD',
                route('bills.paypal.success'),
                route('bills.paypal.cancel')
            );

            if (isset($response['id']) && $response['status'] == 'CREATED') {
                session([
                    'paypal_wallet' => [
                        'token' => $response['id'],
                        'amount' => $request->amount,
                        'note' => $request->note,
                    ]
                ]);
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return response()->json([
                            'approval_url' => $link['href'],
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            logger("error:" . $e->getMessage());
            return errorResponse('Đã có lỗi xảy ra trong quá trình thành toán!', true);
        }
    }

    public function successTransaction(Request $request)
    {
        try {
            $response = $this->paypalService->captureOrder($request->token);

            $paypalWallet = session('paypal_wallet');

            // Nếu không có session hoặc token không trùng khớp
            if (!$paypalWallet || $paypalWallet['token'] !== $request->token) {
                return redirect()->route('bills.index')->with('error', 'Lệnh nạp không hợp lệ!');
            }

            $amount = $paypalWallet['amount'];
            $note = $paypalWallet['note'];
            $status = $response['status'] ?? 'FAILED';

            // Lấy ví (nếu chưa có thì tạo)
            $wallet = Wallet::firstOrCreate(
                ['user_id' => auth()->id()],
                ['balance' => 0]
            );

            $balanceBefore = $wallet->balance;

            if ($status === 'COMPLETED') {
                $wallet->increment('balance', $amount);
            }

            $balanceAfter = $wallet->fresh()->balance;

            // Ghi lại lịch sử giao dịch, dù thành công hay thất bại
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'code' => generateTransactionCode(),
                'amount' => $amount,
                'note' => $note,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'extra' => json_encode($response), // Có thể lưu toàn bộ response
            ]);

            session()->forget('paypal_wallet');

            if ($status === 'COMPLETED') {
                return redirect()->route('bills.index')->with('success', 'Nạp tiền thành công!');
            } else {
                return redirect()->route('bills.index')->with('error', 'Giao dịch không hoàn tất.');
            }
        } catch (\Exception $e) {
            logger("PayPal error: " . $e->getMessage());

            // Ghi lại nếu có session
            if (session()->has('paypal_wallet')) {
                $paypalWallet = session('paypal_wallet');
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => auth()->id()],
                    ['balance' => 0]
                );

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'code' => generateTransactionCode(),
                    'amount' => $paypalWallet['amount'],
                    'note' => $paypalWallet['note'] . ' (Lỗi khi thanh toán)',
                    'balance_before' => $wallet->balance,
                    'balance_after' => $wallet->balance,
                    'status' => 'failure',
                    'extra' => json_encode(['exception' => $e->getMessage()]),
                ]);

                session()->forget('paypal_wallet');
            }

            return redirect()
                ->route('bills.index')
                ->with('error', 'Đã xảy ra lỗi khi xử lý thanh toán.');
        }
    }


    public function cancelTransaction()
    {
        return back()
            ->with('error', 'You have canceled the transaction.');
    }
}
