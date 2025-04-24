<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Mail\EmployeeCreated;
use App\Services\EmployeeService;
use App\Services\RoleService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    use PaginateTrait;

    public function __construct(public EmployeeService $employeeService, public RoleService $roleService)
    {
        // $this->categoryService = $categoryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $title = 'Thêm mới nhân viên';
        return view('admin.employee.save', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
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
        $title = 'Cập nhật nhân viên';
        $employee = $this->employeeService->show($id);
        $roles =  $this->roleService->pluckRole();
        return view('admin.employee.save', compact('employee', 'title', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        $payload = $request->validated();
        $response = $this->employeeService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
