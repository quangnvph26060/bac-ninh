<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BomsRequest;
use App\Models\Bom;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class BomsController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $raw = Bom::with(['productable', 'material'])
                ->get()
                ->groupBy(function ($item) {
                    return $item->productable_type . '_' . $item->productable_id;
                })
                ->values();

            $data = $raw->map(function ($group, $index) {
                $first = $group->first();

                $productableName = '';
                $productable_type = '';
                if ($first->productable_type == '\App\Models\Product') {
                    $productableName = Product::find($first->productable_id)->name;
                    $productable_type = 'Sản phẩm';
                } elseif ($first->productable_type == '\App\Models\ProductVariant') {
                    $productableName = ProductVariant::find($first->productable_id)->sku;
                    $productable_type = 'Biến thể';
                }

                $materialList = $group->map(function ($item) {
                    return ($item->material->name ?? '') . ' (' . number_format($item->quantity_required, 2) . ')';
                })->implode(', ');

                return [
                    'DT_RowIndex'        => $index + 1,
                    'checkbox'           => '<input type="checkbox" class="row-checkbox" value="' . $first->productable_id . '">',
                    'productable_type'   => $productable_type,
                    'productable_name'   => $productableName,
                    'material_name'      => $materialList,
                    'quantity_required'  => '',
                    'operations' => '
                            <a href="' . route('admin.boms.edit', $first->productable_id) . '"
                            class="btn btn-primary btn-sm table-actions btn-operation-edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="javascript:void(0);"
                            data-id="' . $first->productable_id . '"
                            class="btn btn-danger btn-sm table-actions btn-boms-destroy">
                                <i class="ti ti-trash"></i>
                            </a>
                        ',

                ];
            });

            return datatables()->of($data)
                ->rawColumns(['checkbox', 'operations'])
                ->make(true);
        }

        return view('admin.booms.index');
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $title = 'Thêm mới vật liệu cho sản phẩm';
        $materials = Material::get();
        $products = Product::with('variants')->get();
        $boms = collect();

        return view('admin.booms.save', compact('title', 'products', 'materials', 'boms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BomsRequest $request)
    {

        $data = [];

        foreach ($request->input('values') as $value) {
            $data[] = [
                'productable_type'  => $request->input('productable_type'),
                'productable_id'    => $request->input('productable_id'),
                'material_id'       => $value['material_id'],
                'quantity_required' => $value['quantity_required'],
            ];
        }

        Bom::insert($data);
        return redirect()->route('admin.boms.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Cập nhật vật liệu cho sản phẩm';
        $materials = Material::get();
        $products = Product::with('variants')->get();

        $boms = Bom::where('productable_id', $id)->get();
        return view('admin.booms.save', compact('title', 'products', 'materials', 'boms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BomsRequest $request, string $id)
    {
        $productableType = $request->input('productable_type');
        $productableId = $request->input('productable_id');
        $values = $request->input('values', []);


        $existingBoms = Bom::where('productable_type', $productableType)
            ->where('productable_id', $productableId)
            ->get()
            ->keyBy('material_id');


        $newMaterialIds = [];

        foreach ($values as $value) {
            $materialId = $value['material_id'];
            $quantity = $value['quantity_required'];
            $newMaterialIds[] = $materialId;

            if ($existingBoms->has($materialId)) {
                // Nếu đã có thì update
                $existingBoms[$materialId]->update([
                    'quantity_required' => $quantity,
                ]);
            } else {
                Bom::create([
                    'productable_type' => $productableType,
                    'productable_id' => $productableId,
                    'material_id' => $materialId,
                    'quantity_required' => $quantity,
                ]);
            }
        }


        $bomsToDelete = $existingBoms->filter(function ($bom) use ($newMaterialIds) {
            return !in_array($bom->material_id, $newMaterialIds);
        });

        foreach ($bomsToDelete as $bom) {
            $bom->delete();
        }

        // return redirect()->route('admin.boms.index');

    }

    public function delete($id)
    {

        $deleted = Bom::where('productable_id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xoá thành công.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy dữ liệu để xoá.'
        ], 404);
    }


}
