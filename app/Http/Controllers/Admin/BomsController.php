<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BomsRequest;
use App\Models\Bom;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BomService;
use App\Services\ProductService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BomsController extends Controller
{
    use PaginateTrait;

    public function __construct(public ProductService $productService, public BomService $bomService) {}

    public function index()
    {

        if (request()->ajax()) {
            // $raw = Bom::with(['productable', 'material'])
            //     ->get()
            //     ->groupBy(function ($item) {
            //         return $item->productable_type . '_' . $item->productable_id;
            //     })
            //     ->values();

            // $data = $raw->map(function ($group, $index) {
            //     $first = $group->first();

            //     $productableName = '';
            //     $productable_type = '';
            //     if ($first->productable_type == '\App\Models\Product') {
            //         $productableName = Product::find($first->productable_id)->name;
            //         $productable_type = 'Sản phẩm';
            //     } elseif ($first->productable_type == '\App\Models\ProductVariant') {
            //         $productableName = ProductVariant::find($first->productable_id)->sku;
            //         $productable_type = 'Biến thể';
            //     }

            //     $materialList = $group->map(function ($item) {
            //         return ($item->material->name ?? '') . ' (' . number_format($item->quantity_required, 2) . ')';
            //     })->implode(', ');

            //     return [
            //         'DT_RowIndex'        => $index + 1,
            //         'checkbox'           => '<input type="checkbox" class="row-checkbox" value="' . $first->productable_id . '">',
            //         'productable_type'   => $productable_type,
            //         'productable_name'   => $productableName,
            //         'material_name'      => $materialList,
            //         'quantity_required'  => '',
            //         'operations' => '
            //                 <a href="' . route('admin.boms.edit', $first->productable_id) . '"
            //                 class="btn btn-primary btn-sm table-actions btn-operation-edit">
            //                     <i class="ti ti-edit"></i>
            //                 </a>
            //                 <a href="javascript:void(0);"
            //                 data-id="' . $first->productable_id . '"
            //                 class="btn btn-danger btn-sm table-actions btn-boms-destroy">
            //                     <i class="ti ti-trash"></i>
            //                 </a>
            //             ',

            //     ];
            // });

            // return datatables()->of($data)
            //     ->rawColumns(['checkbox', 'operations'])
            //     ->make(true);

            if (request()->ajax()) {
                $query = $this->bomService->pagination();

                return $this->processDataTable(
                    $query,
                    fn($dataTable) =>
                    $dataTable
                        ->addColumn('material_items', function ($row) {
                            return $row->bomItems
                                ->map(fn($item) =>  $item->material->name . ' (SL: ' . number_format($item->quantity_required, 0) . ')')
                                ->implode(', ');
                        })->addColumn('productable_name', function ($row) {
                            return $row->productable_name;
                        })
                        ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                        ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                    ['operations']
                );
            }
        }

        return view('admin.booms.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $title = 'Thiết lập booms';
        $materials = Material::get();
        $products = Product::with('variants')->get();
        $bom = null;

        return view('admin.booms.save', compact('title', 'products', 'materials', 'bom'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BomsRequest $request)
    {
        $productableType = $request->productable_type;
        $productableId = $request->productable_id;

        // Kiểm tra nếu BOM đã tồn tại
        $exists = Bom::where('productable_type', $productableType)
            ->where('productable_id', $productableId)
            ->exists();

        if ($exists) {
            return errorResponse('Sản phẩm hoặc biến thể này đã có BOM. Vui lòng chỉnh sửa BOM thay vì tạo mới.', true);
        }

        try {
            DB::beginTransaction();

            $bom = Bom::create($request->only(['productable_type', 'productable_id']));

            $data = [];

            foreach ($request->values as $value) {
                $data[] = [
                    'material_id' => $value['material_id'],
                    'quantity_required' => $value['quantity_required'],
                ];
            }

            $bom->bomItems()->createMany($data);

            DB::commit();
            return successResponse("Tạo BOM thành công", ['redirect' => '/admin/boms'], 201, true);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return errorResponse("Đã có lỗi xảy ra, vui lòng thử lại sau", true);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Cập nhật boms';
        $materials = Material::get();
        $products = Product::with('variants')->get();

        $bom = Bom::query()->with('bomItems')->findOrFail($id);

        return view('admin.booms.save', compact('title', 'products', 'materials', 'bom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BomsRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            // Tìm bom cần cập nhật
            $bom = Bom::findOrFail($id);

            // Xoá các bom_item cũ
            $bom->bomItems()->delete();

            // Tạo lại danh sách bom_item mới
            $data = [];
            foreach ($request->values as $value) {
                $data[] = [
                    'material_id' => $value['material_id'],
                    'quantity_required' => $value['quantity_required'],
                ];
            }

            $bom->bomItems()->createMany($data);

            DB::commit();
            return handleResponse("Cập nhật BOM thành công", true, 200, ['redirect' => '/admin/boms']);
        } catch (\Exception $e) {
            logger($e->getMessage());
            DB::rollBack();
            return errorResponse("Đã có lỗi xảy ra, vui lòng thử lại sau", true);
        }
    }


    public function productSelect(Request $request)
    {
        $products = $this->productService->productSelect($request);

        return response()->json($products);
    }

    public function checkVariants(Product $product)
    {
        $product->load('variants');

        $variants = $product->variants()->select('id', 'sku')->get();

        return response()->json([
            'has_variant' => $variants->count() > 1,
            'variants' => $variants
        ]);
    }
}
