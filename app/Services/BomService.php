<?php

namespace App\Services;

use App\Models\Bom;

class BomService extends BaseService
{
    public function __construct(Bom $bom)
    {
        parent::__construct($bom);
    }

    public function pagination()
    {
        return $this->queryBuilder(['*'], ['productable', 'bomItems.material']);
    }
}
