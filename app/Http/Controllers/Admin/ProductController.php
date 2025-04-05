<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductTemplateExport;
use App\Imports\ProductImport;
use App\Services\AttributeService;
use App\Services\CompanyService;
use App\Traits\PaginateTrait;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\BrandService;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\ProductExport;
use App\Http\Requests\Product\ProductRequest;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use PaginateTrait;
    public function __construct(
        public ProductService $productService,
        public CategoryService $categoryService,
        public BrandService $brandService,
        public SupplierService $supplierService,
        public CompanyService $companyService,
        public AttributeService $attributeService
    ) {}
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->productService->getProductAll();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('brand_id', fn($row) => $row->brand->name ?? '-----')
                    ->editColumn('category_id', fn($row) => $row->category->name ?? '-----')
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }

        $categories     = $this->categoryService->getCategoryAll();
        $brands         = $this->brandService->getBrandAll();
        $companies      = $this->companyService->getCompanyAll();

        return view('admin.product.index', compact('categories', 'brands', 'companies'));
    }

    public function create()
    {
        $title = 'thêm sản phẩm';
        $brands = $this->brandService->getBrandAll();
        $categories = $this->categoryService->getCategoryAll();
        $attributes = $this->attributeService->getPluck();
        return view('admin.product.save', compact('brands', 'categories', 'title', 'attributes'));
    }

    public function store(ProductRequest $request)
    {
        $payload = $request->validated();
        $response = $this->productService->store($payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    public function edit(string $id)
    {
        $title = 'Cập nhật sản phẩm';

        $brands = $this->brandService->getBrandAll();
        $categories = $this->categoryService->getCategoryAll();
        $attributes = $this->attributeService->getPluck();
        $product = $this->productService->show($id);
        $variants = $this->productService->getVariants($product);
        $selectedAttributes =   $product->attributes->pluck('id')->toArray();
        $attributesWithValues = $this->productService->attributesWithValues($product);
        $productCrossSell = $this->productService->getProductCrossSell($product);
        $preloadedImages = $this->productService->getProductImages($product);
        // dd($productImages);

        return view('admin.product.save', compact('product', 'brands', 'categories', 'attributesWithValues', 'title', 'selectedAttributes', 'attributes', 'variants', 'productCrossSell', 'preloadedImages'));
    }

    public function update(string $id, ProductRequest $request)
    {
        $payload = $request->validated();
        $response = $this->productService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
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

    /**
     * Tìm kiếm sản phẩm dựa trên query.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // Lấy query từ request
        $query = $request->get('query');
        $page = $request->get('page', 1); // Mặc định trang là 1
        $perPage = $request->get('per_page', 10); // Mặc định 10 sản phẩm mỗi trang

        // Tìm kiếm sản phẩm theo tên
        $products = Product::where('name', 'like', '%' . $query . '%')
            ->paginate($perPage); // Phân trang kết quả

        // Trả về kết quả tìm kiếm dạng JSON, bao gồm dữ liệu sản phẩm và phân trang
        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'prev_page_url' => $products->previousPageUrl(),
                'next_page_url' => $products->nextPageUrl(),
            ],
        ]);
    }

    public function getValueByAttributeId($attributeId)
    {
        return $this->attributeService->getValueByAttributeId($attributeId);
    }
}
