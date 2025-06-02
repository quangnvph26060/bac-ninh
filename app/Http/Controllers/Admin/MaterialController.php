<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\MaterialRequest;
use App\Models\AttributeValue;
use App\Models\Material;
use App\Models\MaterialAttribute;
use App\Services\AttributeService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\CompanyService;
use App\Services\SupplierService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    use PaginateTrait;
    public function __construct(
        public AttributeService $attributeService
    ) {
    }

    public function index()
    {
        if (request()->ajax()) {

            $productListQuery = Material::query();

            return $this->processDataTable(
                $productListQuery,
                function ($dataTable) {
                    $dataTable->editColumn('price_usd', function ($row) {
                        return number_format($row->price_usd, 2);
                    });

                    $dataTable->editColumn('price_vnd', function ($row) {
                        return number_format($row->price_vnd, 0, ',', '.');
                    });

                    $dataTable->addColumn('operations', function ($row) {
                        return view('admin.components.operation', ['row' => $row]);
                    });

                    return $dataTable;
                },
                ['operations']
            );
        }

        return view('admin.material.index');
    }


    public function create()
    {
        $title = 'Thêm vật liệu';

        $attributes = $this->attributeService->getPluck();
        return view('admin.material.save', compact('title', 'attributes'));
    }

    public function store(MaterialRequest $request)
    {
        $material = Material::create([
            'name'         => $request->name,
            'type'         => $request->type,
            'price_usd' => $request->type == 'normal' ? (float) str_replace([','], '', $request->price_usd): null,
            'price_vnd' => $request->type == 'normal' ? (int) str_replace(['.'], '', $request->price_vnd) : null,
            'distributor'  => $request->distributor,
            'stock'        => $request->type == 'normal' ? $request->stock : null,
            'status'        => $request->status,
        ]);

        // Nếu là variant, xử lý thêm
        if ($request->type === 'variant') {
            // Lưu các thuộc tính
            foreach ($request->input('attributes') as $key => $attributeId) {
                MaterialAttribute::create([
                    'material_id'          => $material->id,
                    'attribute_id'         => $key,
                    'attribute_values_ids' => array_map('intval', $attributeId)
                ]);
            }

            // Lưu các biến thể
            if ($request->has('variants') && is_array($request->variants)) {
                $stock = 0;
                foreach ($request->variants as $variantData) {
                    $valueNames  = AttributeValue::whereIn('id', $variantData['attribute_value_ids'])->pluck('value')->toArray();
                    $variantName = implode(' - ', $valueNames);
                    $idString    = implode('-', $variantData['attribute_value_ids']);

                    $variant = $material->variants()->create([
                        'sku'                    => $variantData['sku'] ?? $variantName,
                        'price'                  => (int) str_replace(['.'], '', $variantData['price'] ),
                        'stock'                  => $variantData['stock'],
                        'product_unit'           => $variantData['product_unit'],
                        'attribute_value_combine' => $idString
                    ]);
                    $stock += $variantData['stock'];

                    $variant->attributeValues()->attach(array_map('intval', $variantData['attribute_value_ids']));
                }
                $material->update([
                    'stock' => $stock
                ]);
            }
        }
    }



    public function edit(string $id)
    {
        $title = 'Cập nhật vật tư';
        $material = Material::find($id);

        $variants = $material->variants ?? [];
        // dd($variants);
        $attributes = $this->attributeService->getPluck();
        $materialAttributes =  MaterialAttribute::where('material_id', $id)->get();
        $selectedAttributes = $materialAttributes->pluck('attribute_id')->toArray();

        return view('admin.material.save', [
            'title' => 'Cập nhật vật tư',
            'material' => $material,
            'variants' => $variants,  // Truyền dữ liệu variants,
            'attributes' => $attributes,
            'selectedAttributes' => $selectedAttributes,
            'materialAttributes' => $materialAttributes
        ]);
    }


    public function update(MaterialRequest $request, $id)
    {
        // 1️⃣ Tìm material theo ID
        $material = Material::findOrFail($id);

        // 2️⃣ Cập nhật thông tin chung
        $material->update([
            'name'         => $request->name,
            'type'         => $request->type,
            'price_usd' => $request->type == 'normal' ? (float) str_replace([','], '', $request->price_usd): null,
            'price_vnd' => $request->type == 'normal' ? (int) str_replace(['.'], '', $request->price_vnd) : null,
            'distributor'  => $request->distributor,
            'stock'        => $request->type === 'normal' ? $request->stock : null,
            'status'       => $request->status,
        ]);

        // 3️⃣ Nếu là 'variant', xử lý attributes và variants
        if ($request->type === 'variant') {
            // Xóa toàn bộ attributes cũ
            $material->attributes()->delete();

            // Lưu các thuộc tính mới
            if ($request->has('attributes')) {
                foreach ($request->input('attributes') as $key => $attributeId) {
                    MaterialAttribute::create([
                        'material_id'          => $material->id,
                        'attribute_id'         => $key,
                        'attribute_values_ids' => array_map('intval', $attributeId)
                    ]);
                }
            }

            // Xóa toàn bộ variants cũ
            $material->variants()->delete();

            // Lưu các variants mới
            if ($request->has('variants') && is_array($request->variants)) {
                $stock = 0;
                foreach ($request->variants as $variantData) {
                    $valueNames  = AttributeValue::whereIn('id', $variantData['attribute_value_ids'])->pluck('value')->toArray();
                    $variantName = implode(' - ', $valueNames);
                    $idString    = implode('-', $variantData['attribute_value_ids']);

                    $variant = $material->variants()->create([
                        'sku'                    => $variantData['sku'] ?? $variantName,
                        'price'                  => (int) str_replace(['.'], '', $variantData['price'] ),
                        'stock'                  => $variantData['stock'],
                        'product_unit'           => $variantData['product_unit'],
                        'attribute_value_combine' => $idString
                    ]);

                    $stock += $variantData['stock'];

                    $variant->attributeValues()->attach(array_map('intval', $variantData['attribute_value_ids']));
                }

                // Update tổng stock
                $material->update([
                    'stock' => $stock
                ]);
            }
        } else {
            // Nếu không phải variant, xóa toàn bộ attributes và variants
            // $material->attributes()->delete();
            // $material->variants()->delete();
        }

        return response()->json([
            'message' => 'Material updated successfully!',
            'data'    => $material
        ], 200);
    }



    public function list()
    {
        $material = Material::get();
        return view('admin.material.index');
    }
}
