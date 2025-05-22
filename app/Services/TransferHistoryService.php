<?php

namespace App\Services;

use App\Models\WalletTransaction;

class TransferHistoryService extends BaseService
{
    public function __construct(WalletTransaction $walletTransaction)
    {
        parent::__construct($walletTransaction);
    }

    public function pagination()
    {

        return $this->queryBuilder(
            ['*'],
            ['wallet.user:id,name,email', 'configPayment'],
            false,
            [],
            [['is_topup_request', true]]
        );
    }

    public function reject($credentials)
    {
        try {

            $walletTransaction = $this->findById($credentials['id']);

            if (!$walletTransaction) {
                return errorResponse("Yêu cầu không tồn tại!");
            }

            if ($walletTransaction->status === 'failure') {
                return errorResponse("Yêu cầu đã bị từ chối trước đó rồi!");
            }

            if ($walletTransaction->status === 'complete') {
                return errorResponse("Yêu cầu đã được xác nhận không thể từ chối!");
            }

            if ($this->updateData($credentials['id'], ['status' => 'failure', 'reason' => $credentials['reason']])) {
                return successResponse("Yêu cầu đã bị từ chối.", null, 200);
            }

            return errorResponse("Yêu cầu không tồn tại.");
        } catch (\Exception $e) {
            return errorResponse("Yêu cầu không tồn tại.");
        }
    }

    public function confirm($credentials)
    {
        try {

            $walletTransaction = $this->findById($credentials['id']);

            if (!$walletTransaction) {
                return errorResponse("Yêu cầu không tồn tại!");
            }

            if ($walletTransaction->status === 'complete') {
                return errorResponse("Yêu cầu đã được xác nhận trước đó rồi!");
            }

            if ($walletTransaction->status === 'failure') {
                return errorResponse("Yêu cầu đã bị từ chối không thể xác nhận!");
            }

            if ($this->updateData($credentials['id'], ['status' => 'complete'])) {
                $walletTransaction->wallet->increment('balance', $walletTransaction->amount);

                return successResponse("Yêu cầu đã được xác nhận.", null, 200);
            }

            return errorResponse("Yêu cầu không tồn tại.");
        } catch (\Exception $e) {
            return errorResponse("Yêu cầu không tồn tại.");
        }
    }
}
