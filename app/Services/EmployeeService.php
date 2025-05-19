<?php

namespace App\Services;

use App\Models\Employee;

class EmployeeService extends BaseService
{
    public function __construct(public Employee $employee)
    {
        parent::__construct($employee);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'employee_code',
            'full_name',
            'gender',
            'date_of_birth',
            'phone',
            'email',
            'status',
            'contract_type',
        ];

        return $this->queryBuilder(
            $columns,
            [],
            false,
            [],
            [['is_admin', '<>', true]]
        );
    }

    public function store(array $payload)
    {
        $uploadedAvatar = null;
        $uploadedIdentityCard = null;

        return transaction(function () use ($payload, &$uploadedAvatar, &$uploadedIdentityCard) {

            if (hasFile('avatar')) {
                $uploadedAvatar = uploadImages('avatar', 'employees');
                $payload['avatar'] = $uploadedAvatar;
            }

            if (hasFile('identity_card_image')) {
                $uploadedIdentityCard = uploadImages('identity_card_image', 'employees');
                $payload['identity_card_image'] = $uploadedIdentityCard;
            }

            if (!$employee = $this->create($payload)) {
                errorResponse('Thêm nhân viên thất bại!');
            }

            $employee->syncRoles($payload['roles'] ?? []);

            return successResponse('Thêm nhân viên thành công.', $employee, 201);
        }, function () use ($uploadedAvatar, $uploadedIdentityCard) {
            if (!empty($uploadedAvatar)) {
                deleteImage($uploadedAvatar);
            }
            if (!empty($uploadedIdentityCard)) {
                deleteImage($uploadedIdentityCard);
            }
        });
    }

    public function show(string $id)
    {
        return $this->findById($id);
    }

    public function update(string $id, array $payload)
    {
        $uploadedAvatar         = null;
        $uploadedIdentityCard   = null;
        $oldAvatar              = null;
        $oldIdentityCard        = null;

        return transaction(function () use ($id, $payload, &$uploadedAvatar, &$uploadedIdentityCard, $oldAvatar, $oldIdentityCard) {

            $employee = $this->findById($id);

            if (hasFile('avatar')) {
                $uploadedAvatar = uploadImages('avatar', 'employees');
                $payload['avatar'] = $uploadedAvatar;
                $oldAvatar = $employee->avatar;
            }

            if (hasFile('identity_card_image')) {
                $uploadedIdentityCard = uploadImages('identity_card_image', 'employees');
                $payload['identity_card_image'] = $uploadedIdentityCard;
                $oldIdentityCard = $employee->identity_card_image;
            }

            if (!$employee = $this->updateData($id, $payload)) {
                errorResponse('Cập nhật viên thất bại!');
            }

            $employee->syncRoles($payload['roles'] ?? []);

            if (!empty($uploadedAvatar)) {
                deleteImage($oldAvatar);
            }

            if (!empty($uploadedIdentityCard)) {
                deleteImage($oldIdentityCard);
            }

            return successResponse('Cập nhật viên thành công.', $employee, 201);
        }, function () use ($uploadedAvatar, $uploadedIdentityCard) {
            if (!empty($uploadedAvatar)) {
                deleteImage($uploadedAvatar);
            }
            if (!empty($uploadedIdentityCard)) {
                deleteImage($uploadedIdentityCard);
            }
        });
    }

    public function changePasswordByEmail($email, $newPassword)
    {
        if (!$data = $this->firstdByWhere(['*'], [['email', $email]])) {
            return errorResponse("Không tìm thấy tài khoản trên hệ thống!", false, 404);
        }

        $data->password = $newPassword;
        $data->save();

        return true;
    }

    public function getEmployeeByEmail($email)
    {
        return $this->firstdByWhere(['*'], [['email', $email]]);
    }
}
