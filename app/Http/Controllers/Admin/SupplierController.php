<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierRequest;
use App\Models\Supplier;
use App\Services\BrandService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    use PaginateTrait;
    public function __construct(public SupplierService $supplierService, public BrandService $brandService) {}

    public function index()
    {
        $this->authorize('view', Supplier::class);

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
        $this->authorize('create', Supplier::class);
        $banks = DB::table('banks')->pluck('name', 'id')->toArray();
        $title = 'Thêm mới nhà cung cấp.';
        $supplier = null;
        $brands = $this->brandService->getBrandAll(false);
        return view('admin.supplier.save', compact('title', 'supplier', 'banks', 'brands'));
    }

    public function store(SupplierRequest $request)
    {
        $this->authorize('create', Supplier::class);

        $response = $this->supplierService->create($request->all());
        return handleResponse($response['message'], $response['success'], $response['code'], $response['data'], false);
    }

    public function edit(string $id)
    {
        $this->authorize('edit', Supplier::class);

        $title = 'Cập nhật nhà cung cấp.';
        $supplier = $this->supplierService->show($id);
        $getSelectdBrands = $supplier->brands->pluck('id')->toArray();
        $banks = DB::table('banks')->pluck('name', 'id')->toArray();
        $brands = $this->brandService->getBrandAll(false);
        return view('admin.supplier.save', compact('title', 'supplier', 'banks', 'brands', 'getSelectdBrands'));
    }

    public function update(string $id, SupplierRequest $request)
    {
        $this->authorize('edit', Supplier::class);

        $payload = $request->validated();

        $response = $this->supplierService->update($id, $payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
