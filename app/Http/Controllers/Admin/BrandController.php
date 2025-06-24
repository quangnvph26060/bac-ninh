<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\BrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use App\Services\CompanyService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    use PaginateTrait;
    protected $brandService;
    protected $supplierService;
    public function __construct(BrandService $brandService, SupplierService $supplierService, CompanyService $companyService)
    {
        $this->brandService = $brandService;
        $this->supplierService = $supplierService;
    }
    public function index()
    {
        $this->authorize('view', Brand::class);

        if (request()->ajax()) {
            $query = $this->brandService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('website', fn($row) => "<a target='_blank' href='$row->website'>$row->website</a>" ?? '-----')
                    ->editColumn('logo', fn($row) => '<img class="img-fluid" src="' . showImage($row->logo) . '" alt="' . $row->name . '" />')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'logo', 'website']
            );
        }
        
        return view('admin.brand.index');
    }

    public function create()
    {
        $this->authorize('create', Brand::class);

        $title = 'Thêm thương hiệu';
        $brand = null;
        return view('admin.brand.save', compact('title', 'brand'));
    }

    public function store(BrandRequest $request)
    {
        $this->authorize('create', Brand::class);

        $payload = $request->validated();
        $response = $this->brandService->store($payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    public function edit(string $id)
    {
        $this->authorize('edit', Brand::class);

        $title = 'Cập nhật thương hiệu';
        $brand = $this->brandService->show($id);
        return view('admin.brand.save', compact('brand', 'title'));
    }

    public function update(string $id, BrandRequest $request)
    {
        $this->authorize('edit', Brand::class);

        $payload = $request->validated();
        $response = $this->brandService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
