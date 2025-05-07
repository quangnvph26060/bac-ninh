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
        $columns = [
            'id',
            'wallet_id',
            'code',
            'amount',
            'note',
            'balance_before',
            'balance_after',
            'created_at',
            'type'
        ];

        return $this->queryBuilder(
            $columns,
            ['wallet.user:id,name']
        );
    }
}
