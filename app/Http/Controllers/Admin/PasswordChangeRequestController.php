<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PasswordChangeRequestService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class PasswordChangeRequestController extends Controller
{
    use PaginateTrait;

    public function __construct(public PasswordChangeRequestService $passwordChangeRequestService) {}

    public function index()
    {
        if (request()->ajax()) {
            $query = $this->passwordChangeRequestService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                    ->editColumn('updated_at', fn($row) => $row->created_at == $row->updated_at ? 'Chưa cập nhật...' : $row->updated_at->format('d-m-Y H:i'))
            );
        }

        return view('admin.employee.password-change-request');
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:password_change_requests,id'
        ]);

        $response = $this->passwordChangeRequestService->confirm($request->id);

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }
}
