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
                ->addColumn('user', fn($row)=> $row->wallet->user->name)
                    ->editColumn('created_at', fn($row) => $row->created_at->format('F j, Y \a\t g:i a'))
            );
        }

        return view('admin.transfer-history.index');
    }
}
