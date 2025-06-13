<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MaterialRequestService;
use App\Services\OrderService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    use PaginateTrait;

    public function __construct(
        public MaterialRequestService $materialRequestService,
        public OrderService $orderService
    ) {}

    public function index()
    {
        if (request()->ajax()) {

            $buider = $this->materialRequestService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
            );
        }
        return view('admin.material-request.index');
    }

    public function create()
    {

        return view('admin.material-request.create');
    }

    public function orderSelect(Request $request)
    {
        $orders = $this->orderService->orderSelect($request);

        return response()->json($orders);
    }
}
