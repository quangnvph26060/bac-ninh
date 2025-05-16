<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Brand;



class ActivityService extends BaseService
{
    public function __construct(ActivityLog $activityLog)
    {
        parent::__construct($activityLog);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'employee_id',
            'action',
            'model_type',
            'model_id',
            'changes',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['employee']
        );
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['employee']);
    }
}
