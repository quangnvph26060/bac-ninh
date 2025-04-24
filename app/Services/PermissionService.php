<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService extends BaseService
{
    public function __construct(public Permission $permission)
    {
        parent::__construct($permission);
    }

    public function pagination()
    {

        return $this->queryBuilder(
            [
                'id',
                'name',
                'created_at'
            ]
        );
    }

    public function store(string $id, array $payloads)
    {
        return transaction(function () use ($id, $payloads) {

            $this->updateOrCreate(['id' => $id],  $payloads);

            return successResponse('Thao tác thành công.');
        });
    }

    public function groupPermissionsByNamespace()
    {
        return $this->permission->query()
            ->select('name', 'id')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy(function ($permission) {
                $parts = explode(' ', $permission->name);
                return $parts[0];
            });
    }

    public function destroy($id)
    {
        try {
            $this->deleteById($id);
            return successResponse('Xóa quyền thành công.');
        } catch (\Exception $e) {
            logger('RoleService:' . $e->getMessage());
            return errorResponse('Xóa quyền thất bại!');
        }
    }
}
