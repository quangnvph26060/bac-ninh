<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\TransferHistoryService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    use PaginateTrait;
    public function __construct(public TransferHistoryService $transferHistoryService) {}
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->transferHistoryService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->addColumn('method', fn($row) => "<img src='" . showImage($row->configPayment->image) . "' alt='" . $row->configPayment->title . "' width='50'>")
                    ->addColumn('proof', fn($row) => showImage($row->proof))
                    ->addColumn('user', fn($row) => $row->wallet->user->name)
                    ->editColumn('created_at', fn($row) => $row->created_at->format('F j, Y \a\t g:i a')),
                ['method']
            );
        }

        return view('admin.transfer-history.index');
    }

    public function reject(Request $request)
    {
        $credentials = $request->validate([
            'id' => 'required',
            'reason' => 'required',
        ]);

        $response = $this->transferHistoryService->reject($credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], [], false);
    }

    public function confirm(Request $request)
    {
        $credentials = $request->validate([
            'id' => 'required',
        ]);

        $response = $this->transferHistoryService->confirm($credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], [], false);
    }
}
