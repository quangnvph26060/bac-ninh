<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permisstion\PermisstionRequest;
use App\Services\PermissionService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    use PaginateTrait;

    public function __construct(public PermissionService $permissionService) {}

    public function index()
    {
        if (request()->ajax()) {

            $employees = $this->permissionService->pagination();

            return DataTables::of($employees)
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                // ->editColumn(
                //     'operations',
                //     fn($row) =>
                //     "
                //     <button data-id='$row->id'
                //         class='btn btn-primary btn-sm table-actions btn-operation-edit'>
                //         <i class='ti ti-edit'></i>
                //     </button>
                //     <button data-id='$row->id'
                //         class='btn btn-danger btn-sm table-actions btn-operation-destroy'>
                //         <i class='ti ti-trash'></i>
                //     </button>
                // "
                // )
                // ->rawColumns(['operations'])
                ->make(true);
        }
        return view('admin.permission.index');
    }

    public function save(PermisstionRequest $request, $id = '')
    {
        $payloads = $request->validated();
        $response = $this->permissionService->store($id, $payloads);

        return response()->json([
            'message' => $response['message'],
            'success' => $response['success']
        ], $response['code']);
    }

    public function destroy(string $id)
    {
        $result = $this->permissionService->destroy($id);

        return response()->json([
            'message' => $result['message'],
            'success' => $result['success']
        ], $result['code']);
    }
}
