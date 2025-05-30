<?php

namespace App\Services;

use App\Models\Subject;

class SubjectService extends BaseService
{
    public function __construct(Subject $subject)
    {
        parent::__construct($subject);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'title',
            'status',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
        );
    }

    public function store($credentials)
    {
        return transaction(function () use ($credentials) {

            $credentials['status'] ??= 2;

            if (! $this->create($credentials)) {
                errorResponse('Thêm chủ thể thất bại!');
            }

            return successResponse('Thêm chủ thể thành công.', '', 201);
        });
    }

    public function update($credentials)
    {
        return transaction(function () use ($credentials) {

            $credentials['status'] ??= 2;

            if (! $this->updateData($credentials['id'], $credentials)) {
                errorResponse('Cập nhật chủ thể thất bại!');
            }

            return successResponse('Cập nhật chủ thể thành công.', '', 201);
        });
    }
}
