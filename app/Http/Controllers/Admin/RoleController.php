<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use PaginateTrait;

    public function __construct(public RoleService $roleService, public PermissionService $permissionService) {}

    public function index()
    {
        $this->authorize('view', Role::class);

        if (request()->ajax()) {

            $employees = $this->roleService->pagination();

            return $this->processDataTable(
                $employees,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                    ->editColumn(
                        'permissions',
                        fn($row) =>
                        $row->permissions->isNotEmpty()
                            ? $row->permissions->pluck('name')->implode(', ')
                            : 'Không có quyền nào'
                    )
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'logo', 'website']
            );
        }
        return view('admin.role.index');
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions  = $this->permissionService->groupPermissionsByNamespace();
        $title = "Thêm mới vai trò";
        return view('admin.role.save', compact('title', 'permissions'));
    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $payload = $request->validated();
        $response = $this->roleService->store($payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    public function edit(string $id)
    {
        $this->authorize('edit', Role::class);

        $title = "Cập nhật vai trò";
        $role = $this->roleService->show($id);
        $permissions  = $this->permissionService->groupPermissionsByNamespace();
        return view('admin.role.save', compact('title', 'permissions', 'role'));
    }

    public function update(RoleRequest $request, string $id)
    {
        $this->authorize('edit', Role::class);

        $payload = $request->validated();
        $response = $this->roleService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
