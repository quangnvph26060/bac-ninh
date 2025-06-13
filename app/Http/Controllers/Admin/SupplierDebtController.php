<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupplierDebtService;
use App\Services\SupplierPaymentService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierDebtController extends Controller
{

    use PaginateTrait;
    public function __construct(
        public SupplierDebtService $supplierDebtService,
        public SupplierPaymentService $supplierPaymentService,
        public SupplierService $supplierService,
    ) {}

    public function index()
    {

        if (request()->ajax()) {

            $buider = $this->supplierDebtService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) => $dataTable
                    ->addIndexColumn()
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
                    ->addColumn('supplier_name', fn($row) => $row->supplier->company_name ?? 'N/A')
                    ->addColumn('debt_amount', fn($row) => number_format($row->total_amount - $row->paid_amount))
                    ->editColumn('total_amount', fn($row) => number_format($row->total_amount))
                    ->editColumn('paid_amount', fn($row) => number_format($row->paid_amount))
                    ->addColumn('operations', fn($row) => view('components.debt_actions', compact('row'))->render()),
                ['operations', 'supplier_name', 'debt_amount']
            );
        }

        $totalDebt = $this->supplierDebtService->getTotalDebt();
        $totalPaid = $this->supplierPaymentService->getTotalPaid();
        $totalRemain = $totalDebt - $totalPaid;
        $suppliers      = $this->supplierService->getAllSupplier();

        return view('admin.suplier-debt.index', compact('totalDebt', 'totalPaid', 'totalRemain', 'suppliers'));
    }

    public function show(string $id)
    {
        $supplierDebt = $this->supplierDebtService->show($id);

        if (request()->has('print')) {
            return view('admin.template.print', compact('supplierDebt'));
        }

        if (request()->ajax()) {
            return response()->json(['data' => $supplierDebt]);
        }

        return view('admin.template.print', compact('supplierDebt'));
    }

    public function downloadPdf($id)
    {
        $supplierDebt = $this->supplierDebtService->show($id);

        $code = $supplierDebt->code;
        $timestamp = now()->format('Ymd_His');
        $fileName = 'phieu_cong_no_' . $code . '_' . $timestamp . '.pdf';

        $pdf = Pdf::loadView('admin.template.print', compact('supplierDebt'))->setPaper('A4', 'portrait');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }


    public function pay(Request $request)
    {
        $request->validate([
            'debt_id' => 'required|exists:supplier_debts,id',
            'amount' => 'required|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
        ]);

        $response = $this->supplierDebtService->pay($request->toArray());

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }

    public function getDebtSummary($from = null, $to = null)
    {
        $totalDebt = $this->supplierDebtService->getTotalDebt($from, $to);
        $totalPaid = $this->supplierPaymentService->getTotalPaid($from, $to);
        $totalRemain = $totalDebt - $totalPaid;

        return response()->json([
            'totalDebt' => $totalDebt,
            'totalPaid' => $totalDebt,
            'totalRemain' => $totalDebt
        ]);

        // return compact('totalDebt', 'totalPaid', 'totalRemain');
    }
}
