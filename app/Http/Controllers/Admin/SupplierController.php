<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Supplier;
use App\Services\BrandService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    use PaginateTrait;
    public function __construct(public SupplierService $supplierService, public BrandService $brandService) {}

    public function index()
    {

        if (request()->ajax()) {
            $query = $this->supplierService->pagination();

            return $this->processDataTable(
                $query,
                fn($datatable) => $datatable
                    ->editColumn('company_name', function ($row) {
                        return '<strong>' . e($row->company_name) . '</strong><br>' .
                            '<a href="mailto:' . e($row->email) . '">' . e($row->email) . '</a><br>' .
                            '<a href="tel:' . e($row->phone) . '">' . e($row->phone) . '</a>';
                    })
                    ->addColumn('bank_account_number', fn($row) => $row->bank_account_number . ' - ' . ($row->bank?->shortName ?? 'Chưa cập nhật...'))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['company_name', 'operations']
            );
        }

        return view('admin.supplier.index');
    }


    public function create()
    {
        $title = 'Thêm mới nhà cung cấp.';
        $supplier = null;
        return view('admin.supplier.save', compact('title', 'supplier'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $supplier = $this->supplierService->addSupplier($request->all());
            session()->flash('success', 'Thêm người đại diện thành công');
            return redirect()->route('admin.supplier.index', ['company_id' => $request->company_id]);
        } catch (Exception $e) {
            Log::error('Failed to create supplier: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to create supplier']);
        }
    }


    public function edit(string $id)
    {
        $title = 'Cập nhật nhà cung cấp.';
        $supplier = $this->supplierService->show($id);
        $getSelectdBrands = $supplier->brands->pluck('brand_id')->toArray();
        $banks = DB::table('banks')->pluck('name', 'id')->toArray();
        $brands = $this->brandService->getBrandAll(false);
        return view('admin.supplier.save', compact('title', 'supplier', 'banks', 'brands', 'getSelectdBrands'));
    }

    public function update(string $id, SupplierRequest $request)
    {

        $payload = $request->validated();

        $response = $this->supplierService->update($id, $payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }

}
