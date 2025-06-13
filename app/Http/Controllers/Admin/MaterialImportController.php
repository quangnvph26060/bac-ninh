<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialImport\{
    MaterialImportStoreRequest,
    MaterialImportUpdateRequest
};
use App\Services\MaterialImportService;
use App\Services\MaterialService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialImportController extends Controller
{
    use PaginateTrait;
    public function __construct(
        public MaterialImportService $materialImportService,
        public SupplierService $supplierService,
        public MaterialService $materialService
    ) {}
    public function index()
    {
        if (request()->ajax()) {

            $buider = $this->materialImportService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) =>
                $dataTable
                    ->addColumn('payment_status', fn($row) => $row->payment_status)
                    ->addColumn('supplier', fn($row) => $row->supplier->company_name)
                    ->addColumn('total', fn($row) => formatPrice($row->total))
                    ->addColumn('paid', fn($row) => formatPrice($row->paid))
                    ->editColumn('date', fn($row) => $row->date->format('d/m/Y'))
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i'))
                    ->addColumn(
                        'operations',
                        fn($row) =>
                        "
                            <button data-id='$row->id' type='button'
                                class='btn btn-primary btn-sm table-actions btn-operation-show'>
                                <i class='ti ti-eye text-white'></i>
                            </button>

                            <a href='" . route('admin.material-imports.edit', $row->id) . "'
                                class='btn btn-warning btn-sm table-actions btn-operation-edit'>
                                <i class='ti ti-edit text-dark'></i>
                            </a>
                    "
                    ),
                ['operations', 'payment_status']
            );
        }


        return view('admin.material-import.index');
    }

    public function create()
    {
        $suppliers = $this->supplierService->getAllSupplier();
        $units = $this->materialService->getUnits();
        $banks = DB::table('banks')->pluck('name', 'id')->toArray();
        return view('admin.material-import.create', compact('suppliers', 'banks', 'units'));
    }

    public function store(MaterialImportStoreRequest $request)
    {
        $credentials = $request->validated();

        $response = $this->materialImportService->store($credentials);

        logger($response);

        return handleResponse($response['message'], $response['success'], $response['code'], $response['data']);
    }

    public function show(string $id)
    {
        $materialImport = $this->materialImportService->show($id);

        return response()->json(['data' => $materialImport]);
    }

    public function edit(string $id)
    {
        $suppliers = $this->supplierService->getAllSupplier();
        $units = $this->materialService->getUnits();
        $banks = DB::table('banks')->pluck('name', 'id')->toArray();
        $materialImport = $this->materialImportService->show($id);

        return view('admin.material-import.edit', compact('materialImport', 'suppliers', 'banks', 'units'));
    }

    public function update(MaterialImportUpdateRequest $request, string $id)
    {

        $credentials = $request->validated();

        $response = $this->materialImportService->update($id, $credentials);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
