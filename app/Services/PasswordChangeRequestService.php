<?php

namespace App\Services;

use App\Models\PasswordChangeRequest;

class PasswordChangeRequestService extends BaseService
{
    public function __construct(PasswordChangeRequest $password, public EmployeeService $employeeService)
    {
        parent::__construct($password);
    }

    public function pagination()
    {
        return $this->queryBuilder(
            ['*'],
        );
    }

    public function confirm($id)
    {
        return transaction(function () use ($id) {
            $data = $this->findById($id);

            if ($data->status !== "pending") return errorResponse("Yêu cầu không hợp lệ!", false, 400);

            if ($this->updateData($id, ['status' => 'approved'])) {
                $result = $this->employeeService->changePasswordByEmail($data->email, $data->new_password);

                if (isset($result['success']) && $result['success'] === false) {
                    return errorResponse($result['message'], $result['success'], $result['code']);
                }
                return successResponse("Xác nhận đổi mật khẩu thành công.");
            }
        });
    }
}

