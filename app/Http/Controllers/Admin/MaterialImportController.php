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
use Barryvdh\DomPDF\Facade\Pdf;
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
                        fn($row) => view('components.material_import_actions', compact('row'))
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

    public function downloadPdf($id)
    {
        $materialImport = $this->materialImportService->show($id);
        logger($materialImport);
        // Tạo tên file: phieu_nhap_kho_[ma_phieu]_[timestamp].pdf
        $code = $materialImport->code;
        $timestamp = now()->format('Ymd_His');
        $fileName = 'phieu_nhap_kho_' . $code . '_' . $timestamp . '.pdf';

        $pdf = Pdf::loadView('admin.template.imports.print', compact('materialImport'))->setPaper('A4', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
