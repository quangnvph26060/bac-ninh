<?php

namespace App\Services;

use App\Mail\PasswordResetApproved;
use App\Models\PasswordChangeRequest;
use Illuminate\Support\Facades\Mail;

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

            if ($data->status !== "pending")
                return errorResponse("Yêu cầu không hợp lệ!", false, 400);

            if ( $this->updateData($id, ['status' => 'approved'])) {
                $result = $this->employeeService->changePasswordByEmail($data->email, $data->new_password);

                if (isset($result['success']) && $result['success'] === false) {
                    return errorResponse($result['message'], $result['success'], $result['code']);
                }

                $employee = $this->employeeService->getEmployeeByEmail($data->email);

                Mail::to($data->email)->send(new PasswordResetApproved($employee, 'approved', ''));
                return successResponse("Xác nhận đổi mật khẩu thành công.");
            }
        });
    }

    public function reject($id, $reason)
    {
        return transaction(function () use ($id, $reason) {
            $data = $this->findById($id);

            if ($data->status !== "pending")
                return errorResponse("Yêu cầu không hợp lệ!", false, 400);

            if ($this->updateData($id, ['status' => 'rejected'])) {
                $employee = $this->employeeService->getEmployeeByEmail($data->email);

                Mail::to($data->email)->send(new PasswordResetApproved($employee, 'rejected', $reason));
                return successResponse("Từ chối đổi mật khẩu thành công.");
            }

            return errorResponse("Từ chối đổi mật khẩu thất bại.", false, 400);
        });
    }
}

