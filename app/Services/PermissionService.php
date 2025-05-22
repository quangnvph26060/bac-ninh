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
                'vi_name',
                'group_name',
                'created_at'
            ]
        );
    }

    public function store(string $id, array $payloads)
    {
        return transaction(function () use ($id, $payloads) {

            if (isset($payloads['name'])) {
                $payloads['name'] = ucwords(mb_strtolower($payloads['name']));
            }


            if (isset($payloads['vi_name'])) {
                $payloads['vi_name'] = ucfirst(mb_strtolower($payloads['vi_name']));
            }

            $this->updateOrCreate(['id' => $id], $payloads);

            return successResponse('Thao tác thành công.');
        });
    }


    public function groupPermissionsByNamespace()
    {
        return $this->permission->query()
            ->select('id', 'name', 'group_name', 'vi_name')
            ->orderBy('group_name', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('group_name');
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

    public function getUniqueGroupNames()
    {
        return $this->permission->query()
            ->select('group_name')
            ->whereNotNull('group_name')
            ->distinct()
            ->orderBy('group_name', 'asc')
            ->pluck('group_name')
            ->values(); // Đảm bảo trả về collection chỉ gồm giá trị
    }

}
