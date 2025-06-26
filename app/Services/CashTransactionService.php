<?php

namespace App\Services;

use App\Models\CashTransaction;

class CashTransactionService extends BaseService
{
    public function __construct(CashTransaction $cashTransaction)
    {
        parent::__construct($cashTransaction);
    }

    public function pagination()
    {

        return $this->queryBuilder(
            ['*'],
        );
    }
}
