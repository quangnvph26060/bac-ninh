<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductTemplateExport;
use App\Imports\ProductImport;
use App\Services\CompanyService;
use App\Traits\PaginateTrait;
use Exception;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use PaginateTrait;
    public function __construct(
        public ProductService $productService,
        public CategoryService $categoryService,
        public BrandService $brandService,
        public SupplierService $supplierService,
        public CompanyService $companyService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->supplierService = $supplierService;
        $this->companyService = $companyService;
    }
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->productService->getProductAll();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('brand_id', fn($row) => $row->brand->name ?? 'Unknown')
                    ->editColumn('category_id', fn($row) => $row->category->name ?? 'Unknown')
                    ->editColumn('company_name',  fn($row) => $row->productCompanies->count() > 0 ? $row->productCompanies->pluck('name')->toArray() : 'Đang cập nhật...')
                    ->addColumn('operations', function ($row) {
                        return view('admin.components.operation', compact('row'));
                    }),
                ['operations']
            );
        }

        $categories     = $this->categoryService->getCategoryAll();
        $brands         = $this->brandService->getBrandAll();
        $companies      = $this->companyService->getCompanyAll();

        // dd($companies);
        return view('admin.product.index', compact('categories', 'brands', 'companies'));
    }

    public function productFilter(Request $request)
    {
        // dd($request->all());
        $name = $request->input('name');
        $company_id = $request->input('company_id');
        $category = $this->categoryService->getCategoryAllStaff();
        $brand = $this->brandService->getAllBrand();
        $title = 'Sản phẩm';
        $companies = Company::orderBy('name', 'asc')->get();
        try {
            $product = $this->productService->productFilter($name, $company_id);
            // dd($products);
            return view('admin.product.index', compact('product', 'companies', 'title', 'category', 'brand'));
        } catch (Exception $e) {
            Log::error("Failed to find Product: " . $e->getMessage());
            return redirect()->route('admin.product.store')->with('error', 'Failed to find Product');
        }
    }

    public function create()
    {
        $title = 'thêm sản phẩm';
        $brands = $this->brandService->getBrandAll();
        $categories = $this->categoryService->getCategoryAll();
        return view('admin.product.save', compact('brands', 'categories', 'title'));
    }

    public function addSubmit(ProductStoreRequest $request)
    {
        try {
            $product = $this->productService->createProduct($request);
            return redirect()->route('admin.product.store')->with('success', 'Thêm sản phẩm thành công !');
        } catch (ModelNotFoundException $e) {
            $exception = new ProductNotFoundException();
            return $exception->render(request());
        } catch (Exception $e) {
            dd($e->getMessage());
            Log::error('Failed to fetch add products: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch add products', 500);
        }
    }

    public function edit(Product $product)
    {
        $title = 'Sửa sản phẩm';
        $categories = $this->categoryService->getCategoryAll();
        $brands = $this->brandService->getBrandAll();
        return view('admin.product.save', compact('product', 'brands', 'categories', 'title'));
    }

    public function update($id, ProductUpdateRequest $request)
    {
        $product = $this->productService->updateProduct($id, $request);
        return redirect()->route('admin.product.store')->with('success', 'Cập nhật sản phẩm thành công');
    }


    // ProductController.php
    public function delete($id)
    {
        try {
            $this->productService->deleteProduct($id);

            // Lấy lại danh sách sản phẩm sau khi xóa
            $product = Product::orderByDesc('created_at')->paginate(10); // Điều chỉnh số lượng sản phẩm trên mỗi trang nếu cần thiết
            $view = view('admin.product.table', compact('product'))->render(); // Tạo view cho bảng sản phẩm

            return response()->json(['success' => true, 'message' => 'Xóa thành công!', 'table' => $view]);
        } catch (Exception $e) {
            Log::error('Failed to delete product: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Sản phẩm không thể xóa.']);
        }
    }

    public function formimport()
    {
        return view('admin.product.importexcel');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        $import = new ProductImport();
        Excel::import($import, $request->file('file'));

        if (!empty($import->getErrors())) {

            $message = 'Một số hàng bị lỗi: <br>' . implode('<br>', $import->getErrors());

            session()->flash('message', $message);
        }

        toastr()->success('Hoàn tất quá trình Import.');

        return redirect()->back();
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'product_template.xlsx');
    }

    public function export1(Request $request)
    {
        $selectedCategories = json_decode($request->query('categories', '[]'), true);
        $selectedCompanies = json_decode($request->query('companies', '[]'), true);
        $selectedBrands = json_decode($request->query('brands', '[]'), true);

        // Lọc sản phẩm dựa trên các loại hàng được chọn
        $query = Product::query();

        if ($selectedCategories) {
            $query->whereIn('category_id', $selectedCategories);
        }

        if ($selectedBrands) {
            $query->whereIn('brands_id', $selectedBrands);
        }

        if ($selectedCompanies) {
            $query->company->whereIn('company_id', $selectedCompanies);
        }

        $products = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Đặt tiêu đề cột
        $sheet->setCellValue('A1', 'Mã sản phẩm');
        $sheet->setCellValue('B1', 'tên sản phẩm');
        $sheet->setCellValue('C1', 'Đơn vị');
        $sheet->setCellValue('D1', 'Số lương');
        $sheet->setCellValue('E1', 'Giá nhập');
        $sheet->setCellValue('F1', 'Giá bán');
        $sheet->setCellValue('G1', 'Danh mục');
        $sheet->setCellValue('H1', 'Thương hiệu');
        $sheet->setCellValue('I1', 'Nhà cung cấp');

        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->code);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('C' . $row, $product->product_unit);
            $sheet->setCellValue('D' . $row, $product->quantity);
            $sheet->setCellValue('E' . $row, $product->price);
            $sheet->setCellValue('F' . $row, $product->priceBuy);
            $sheet->setCellValue('G' . $row, $product->categories ? $product->categories->name : ''); // Kiểm tra nếu categories tồn tại
            $sheet->setCellValue('H' . $row, $product->brands ? $product->brands->name : ''); // Kiểm tra nếu brands tồn tại
            $sheet->setCellValue('I' . $row, $product->company->pluck('name')->join(', ')); // Lấy tên các công ty, nối thành chuỗi
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(50);

        $writer = new Xlsx($spreadsheet);

        $response = response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="products.xlsx"',
            ]
        );

        return $response;
    }
}
