<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialVariant;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseDetail;
use App\Services\AttributeService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    //
    use PaginateTrait;
    public function __construct(public AttributeService $attributeService)
    {
    }
    public function index()
    {
        if (request()->ajax()) {

            $productListQuery = Warehouse::query();

            return $this->processDataTable(
                $productListQuery,
                function ($dataTable) {
                    $dataTable->editColumn('price_usd', function ($row) {
                        return '$ ' . number_format($row->price_usd, 2);
                    });

                    $dataTable->editColumn('price_vnd', function ($row) {
                        return number_format($row->price_vnd, 0, ',', '.') . ' đ';
                    });

                    $dataTable->editColumn('purchase_date', function ($row) {
                        return \Carbon\Carbon::parse($row->purchase_date)->format('d/m/Y');
                    });

                    $dataTable->filterColumn('purchase_date', function ($query, $keyword) {
                        $query->whereRaw("DATE_FORMAT(purchase_date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
                    });

                    $dataTable->editColumn('operations', function ($row) {
                        $showUrl = route('admin.warehouse.show', $row->id);

                        return "
                            <a href=\"{$showUrl}\" style='padding: 7px 12px'  class=\"btn btn-primary btn-sm\">
                                <i class=\"ti ti-eye\"></i>
                            </a>

                            <a href=\"javascript:void(0)\" data-id=\"{$row->id}\"
                                class=\"btn btn-danger btn-sm table-actions btn-operation-destroy\">
                                <i class=\"ti ti-trash\"></i>
                            </a>
                        ";
                    });



                    return $dataTable;
                },
                ['operations']
            );
        }

        return view('admin.warehouse.index');
    }

    public function create()
    {
        $materials = Material::with('variants')->get();
        $listdata = [];

        foreach ($materials as $material) {
            if ($material->type === 'normal') {
                $listdata[] = [
                    'type' => $material->type,
                    'id' => $material->id,
                    'name' => $material->name,
                    'stock' => $material->stock,
                    'name_parent' => '',
                    'id_parent' => ''
                ];
            } else {
                foreach ($material->variants as $variant) {
                    $listdata[] = [
                        'type' => $material->type,
                        'id' => $variant->id,
                        'name' => $variant->sku,
                        'stock' => $variant->stock,
                        'name_parent' => $material->name,
                        'id_parent' => $material->id
                    ];
                }
            }
        }
        // dd($listdata);

        $suppliers = Supplier::get();
        return view('admin.warehouse.warehouse', compact('listdata', 'suppliers'));
    }

    public function store(Request $request)
    {
        $items = $request->input('data');

        $usd = 0;
        $vnd = 0;

        foreach ($items as $item) {
            if ($item['currency'] == 'vnd') {
                $rawPrice = str_replace('.', '', $item['price']);
                $price = (float)$rawPrice;
                $vnd += $price * $item['quantity'];
            } else {
                $rawPrice = str_replace(',', '', $item['price']);
                $price = (float)$rawPrice;
                $usd += $price * $item['quantity'];
            }
        }

        $warehouse = Warehouse::create([
            'purchase_date' => now(),
            'price_usd' => $usd,
            'price_vnd' => $vnd,
        ]);

        $name1 = '';
        $name2 = '';
        foreach ($items as $item) {
            if ($item['type'] == 'normal') {
                $material = Material::find($item['id']);
                $name1 = $material->name;
                if ($material) {
                    $material->update([
                        'stock' => $material->stock + $item['quantity']
                    ]);
                }
            } else {
                $materialVariant = MaterialVariant::find($item['id']);
                $name2 = $materialVariant->sku;
                if ($materialVariant) {
                    $materialVariant->update([
                        'stock' => $materialVariant->stock + $item['quantity']
                    ]);
                }
            }

            $supplier = Supplier::find($item['supplier']);

            if ($item['currency'] == 'vnd') {
                $rawPrice = str_replace('.', '', $item['price']);
                $price = (float)$rawPrice;
            } else {
                $rawPrice = str_replace(',', '', $item['price']);
                $price = (float)$rawPrice;
            }


            WarehouseDetail::create([
                'name' => $item['type'] != 'normal' ? $name2 : $name1,
                'type' => $item['type'],
                'name_parent' => $item['type'] != 'normal' ? Material::find(MaterialVariant::find($item['id'])->material_id)->name : '',
                'quantity' => $item['quantity'],
                'price' => $price,
                'price_type' => $item['currency'],
                'distributor' => $supplier ? $supplier->company_name : '',
                'warehouse_id' => $warehouse->id,
                'note' =>  $item['note']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lưu kho thành công!',
            'data' => $warehouse
        ]);
    }

    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if (request()->ajax()) {
            $productListQuery = WarehouseDetail::query()->where('warehouse_id', $id);

            return $this->processDataTable(
                $productListQuery,
                function ($dataTable) {
                    $dataTable->editColumn('name', function ($row) {
                        $name_parent = $row->type == 'normal' ? '' : '( ' . $row->name_parent . ' )';
                        return "$row->name $name_parent";
                    });

                    $dataTable->filterColumn('name', function ($query, $keyword) {
                        $query->where('name', 'like', "%{$keyword}%")
                            ->orWhere('name_parent', 'like', "%{$keyword}%");
                    });


                    $dataTable->editColumn('price', function ($row) {
                        if ($row->price_type === 'usd') {
                            return '$ ' . number_format($row->price, 2, '.', ',');
                        } elseif ($row->price_type === 'vnd') {
                            return number_format($row->price, 0, ',', '.') . ' đ';
                        } else {
                            return number_format($row->price, 2, '.', ',');
                        }
                    });



                    $dataTable->addColumn('operations', function ($row) {
                        $showUrl = route('admin.warehouse.show', $row->id);
                        return "
                        <a href=\"{$showUrl}\" class=\"btn btn-primary btn-sm\">
                            <i class=\"ti ti-eye\"></i>
                        </a>
                        <a href=\"javascript:void(0)\" data-id=\"{$row->id}\" class=\"btn btn-danger btn-sm btn-operation-destroy\">
                            <i class=\"ti ti-trash\"></i>
                        </a>
                    ";
                    });

                    return $dataTable;
                },
                ['operations']
            );
        }

        return view('admin.warehouse.show', compact('warehouse', 'id'));
    }
}
