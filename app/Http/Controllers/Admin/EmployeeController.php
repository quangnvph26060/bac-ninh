<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Mail\EmployeeCreated;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    use PaginateTrait;

    public function __construct(public EmployeeService $employeeService, public PermissionService $permissionService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Employee::class);

        if (request()->ajax()) {

            return $this->processDataTable(
                $this->employeeService->pagination(),
                fn($dataTable) =>
                $dataTable
                    ->editColumn('date_of_birth', fn($row) => $row->date_of_birth ? $row->date_of_birth->format('d-m-Y') : 'Chưa cập nhật...')
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'image']
            );
        }
        return view('admin.employee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Employee::class);

        $title = 'Thêm mới nhân viên';
        $permissions = $this->permissionService->groupPermissionsByNamespace();
        return view('admin.employee.save', compact('title', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        $this->authorize('create', Employee::class);

        $payload = $request->validated();
        $response = $this->employeeService->store($payload);
        if ($response['success']) {
            Mail::to($payload['email'])->send(new EmployeeCreated($response['data'], $payload['password']));
        }
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $this->authorize('edit', Employee::class);

        $title = 'Cập nhật nhân viên';
        $permissions = $this->permissionService->groupPermissionsByNamespace();
        $employee = $this->employeeService->show($id);
        $assignedPermissions = $employee->permissions->pluck('name')->toArray();
        return view('admin.employee.save', compact('employee', 'title', 'permissions', 'assignedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        $this->authorize('edit', Employee::class);

        $payload = $request->validated();
        $response = $this->employeeService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
