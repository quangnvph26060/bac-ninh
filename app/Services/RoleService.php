<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService extends BaseService
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['permissions']
        );
    }

    public function store(array $payloads)
    {
        return transaction(function () use ($payloads) {

            $role = $this->create(['name' => $payloads['name']]);

            if (!empty($payloads['permissions'])) {
                $role->syncPermissions($payloads['permissions']);
            }

            return successResponse('Thêm mới vai trò thành công.', $role, 201);
        });
    }

    public function pluckRole()
    {
        return $this->pluck(['id', 'name'], [], [], ['name', 'asc']);
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['permissions']);
    }

    public function update(string $id, array $payloads)
    {
        return transaction(function () use ($payloads, $id) {
            $role = $this->findById($id);

            if (!$role) {
                return errorResponse('Không tìm thấy dữ liệu.', 404);
            }

            $role->update(['name' => $payloads['name']]);

            if (!empty($payloads['permissions'])) {
                $role->syncPermissions($payloads['permissions']);
            }

            return successResponse('Cập nhật vai trò thành công.');
        });
    }

    public function destroy($id)
    {
        try {
            $this->deleteById($id);
            return successResponse('Xóa vai trò thành công.');
        } catch (\Exception $e) {
            logger('RoleService:' . $e->getMessage());
            return errorResponse('Xóa vai trò thất bại!');
        }
    }
}
