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
        return handleResponse($response['message'], $response['success'], $response['code']);
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

        return view('admin.product.save', compact('product', 'brands', 'categories', 'attributesWithValues', 'title', 'selectedAttributes', 'attributes', 'variants', 'productCrossSell', 'preloadedImages'));
    }

    public function update(string $id, ProductRequest $request)
    {
        $this->authorize('edit', Product::class);

        $payload = $request->validated();
        $response = $this->productService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
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
}
