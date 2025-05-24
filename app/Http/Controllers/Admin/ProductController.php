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
        $this->authorize('view', Product::class);

        if (request()->ajax()) {
            $query = $this->productService->getProductAll();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('brand_id', fn($row) => $row->brand->name ?? '-----')
                    ->editColumn('brand_id', fn($row) => $row->brand->name ?? '-----')
                    ->editColumn('category_id', fn($row) => $row->category->name ?? '-----')
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }

        $categories     = $this->categoryService->getCategoryAll();
        $brands         = $this->brandService->getBrandAll();
        // $companies      = $this->companyService->getCompanyAll();

        return view('admin.product.index', compact('categories', 'brands'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $title = 'thêm sản phẩm';
        $brands = $this->brandService->getBrandAll();
        $categories = $this->categoryService->getCategoryAll();
        $attributes = $this->attributeService->getPluck();
        return view('admin.product.save', compact('brands', 'categories', 'title', 'attributes'));
    }

    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $payload = $request->validated();
        $response = $this->productService->store($payload);
        return handleResponse($response['message'], $response['success'], $response['code'], [], false);
    }

    public function edit(string $id)
    {
        $this->authorize('edit', Product::class);

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

        $values = collect($attributesWithValues)
            ->map(function ($value) {
                return $value['values']; // mỗi phần tử là 1 mảng con
            })
            ->flatten(1) // gộp các mảng con lại thành 1 mảng
            ->all();     // chuyển về mảng PHP thông thường nếu cần

        return view('admin.product.save', compact('product', 'brands', 'categories', 'attributesWithValues', 'title', 'selectedAttributes', 'attributes', 'variants', 'productCrossSell', 'preloadedImages', 'values'));
    }

    public function update(string $id, ProductRequest $request)
    {
        $this->authorize('edit', Product::class);

        $payload = $request->validated();
        $response = $this->productService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code'], [], false);
    }

    public function import(Request $request)
    {
        $this->authorize('import', Product::class);

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
        $this->authorize('export', Product::class);

        return Excel::download(new ProductExport, 'products.xlsx');
    }

    public function downloadTemplate()
    {
        $this->authorize('downloadTemplate', Product::class);

        return Excel::download(new ProductTemplateExport, 'product_template.xlsx');
    }

    public function getValueByAttributeId($attributeId)
    {
        return $this->attributeService->getValueByAttributeId($attributeId);
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
        $products = Product::query()->select('id', 'name', 'image')->where('name', 'like', '%' . $query . '%')
            ->paginate($perPage); // Phân trang kết quả

        $formattedProducts = collect($products->items())->map(function ($product) {
            $product->image = showImage($product->image);
            return $product;
        });

        // Trả về kết quả tìm kiếm dạng JSON, bao gồm dữ liệu sản phẩm và phân trang
        return response()->json([
            'data' => $formattedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'prev_page_url' => $products->previousPageUrl(),
                'next_page_url' => $products->nextPageUrl(),
            ],
        ]);
    }
}
