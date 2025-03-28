<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Brand;
use App\Services\BrandService;
use App\Services\CompanyService;
use App\Services\SupplierService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    //
    protected $brandService;
    protected $supplierService;
    protected $companyService;
    public function __construct(BrandService $brandService, SupplierService $supplierService, CompanyService $companyService)
    {
        $this->brandService = $brandService;
        $this->supplierService = $supplierService;
        $this->companyService = $companyService;
    }
    public function index(Request $request)
    {
        try {
            $supplier = $this->supplierService->GetAllSupplier();
            $title = 'Thương hiệu';

            $brand = Brand::orderByDesc('created_at')->paginate(10);

            if ($request->ajax()) {
                $view = view('admin.brand.table', compact('brand'))->render();
                $pagination = view('vendor.pagination.custom', ['paginator' => $brand])->render();
                return response()->json(['success' => true, 'table' => $view, 'pagination' => $pagination]);
            }

            return view('admin.brand.index', compact('brand', 'title', 'supplier'));
        } catch (Exception $e) {
            Log::error('Failed to fetch brands: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch brands', 500);
        }
    }


    public function findByName(Request $request)
    {
        try {
            $supplier = $this->supplierService->GetAllSupplier();
            $title = 'Thương hiêu ';
            $brands = $this->brandService->brandByName($request->input('name'));
            $brand = new LengthAwarePaginator(
                $brands ? [$brands] : [],
                $brands ? 1 : 0,
                10,
                1,
                ['path' => Paginator::resolveCurrentPath()]
            );
            return view('admin.brand.index', compact('brand', 'title', 'supplier'));
        } catch (Exception $e) {
            Log::error('Failed to find brand: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to find brand'], 500);
        }
    }
    public function addForm()
    {
        $supplier = $this->supplierService->GetAllSupplier();
        $title = 'Thêm thương hiệu ';
        return view('admin.brand.add', compact('title', 'supplier'));
    }

    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'images' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $brand = $this->brandService->createBrand($request);
        return redirect()->route('admin.brand.store')->with('success', 'Thêm thành công');
    }

    public function edit($id)
    {
        $supplier = $this->supplierService->GetAllSupplier();
        $title = 'Sửa thương hiệu';
        $brand = $this->brandService->getBrandById($id);
        return view('admin.brand.edit', compact('brand', 'title', 'supplier'));
    }

    public function update($id, Request $request)
    {
        $brand = $this->brandService->updateBrand($id, $request);
        return redirect()->route('admin.brand.store')->with('success', 'Sửa thành công');
    }

    public function delete($id)
    {
        try {
            $this->brandService->deleteBrand($id);
            $brand = Brand::orderByDesc('created_at')->paginate(10);
            $view = view('admin.brand.table', compact('brand'))->render();
            return response()->json(['success' => true, 'message' => 'Xoá thương hiệu thành công!', 'table' => $view]);
        } catch (Exception $e) {
            Log::error('Failed to delete brand: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể xóa thương hiệu']);
        }
    }

    public function findBySupplier(Request $request)
    {
        $supplier = $this->companyService->getCompany();
        $title = 'Thương hiệu ';
        $brand = $this->brandService->findBrandBySupplier($request->input('supplier_id'));
        return view('admin.brand.index', compact('brand', 'title', 'supplier'));
    }
}
