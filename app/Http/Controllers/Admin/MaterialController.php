<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\MaterialRequest;
use App\Models\Material;
use App\Models\MaterialAttribute;
use App\Services\MaterialService;
use App\Services\SupplierService;
use App\Services\TypeService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    use PaginateTrait;
    public function __construct(public MaterialService $materialService, public TypeService $typeService, public SupplierService $supplierService)
    {
    }

    public function index()
    {
        // dd($this->materialService->pagination()->get());

        if (request()->ajax()) {

            $buider = $this->materialService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
                    ->addColumn('items', fn($row) => $row->types ? $row->types->pluck('name')->implode(', ') : 'N/A')
                    ->addColumn('total_stock', fn($row) => $row->import_details_sum_quantity ?? 0)
                    ->addColumn('total_stock', fn($row) => $row->import_details_sum_quantity ?? 0)
                    ->addColumn('operations', fn($row) => "
                        <a href='" . route('admin.materials.show', $row->id) . "'
                            class='btn btn-primary btn-sm table-actions btn-operation-show'>
                            <i class='ti ti-eye'></i>
                        </a>
                    "),
                ['operations']
            );
        }

        return view('admin.material.index');
    }

    public function create()
    {
        $suppliers = $this->supplierService->getAllSupplier();
        $types = $this->typeService->getTypeNames();
        $names = $this->materialService->getMaterialNames();
        return view('admin.material.save', compact('names', 'types', 'suppliers'));
    }

    public function show(string $id)
    {
        $material = $this->materialService->show($id);

        $groupedByDate = $material->importDetails
            ->sortByDesc(fn($detail) => $detail->import->created_at)
            ->groupBy(fn($detail) => \Carbon\Carbon::parse($detail->import->import_date)->format('Y-m-d'));

        return view('admin.material.show', compact('material', 'groupedByDate'));
    }


    public function store(MaterialRequest $request)
    {
        $credentials = $request->validated();

        $response = $this->materialService->store($credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }



    public function edit(string $id)
    {
        // $title = 'Cập nhật vật tư';
        // $material = Material::find($id);

        // $variants = $material->variants ?? [];
        // // dd($variants);
        // $attributes = $this->attributeService->getPluck();
        // $materialAttributes =  MaterialAttribute::where('material_id', $id)->get();
        // $selectedAttributes = $materialAttributes->pluck('attribute_id')->toArray();

        // return view('admin.material.save', [
        //     'title' => 'Cập nhật vật tư',
        //     'material' => $material,
        //     'variants' => $variants,  // Truyền dữ liệu variants,
        //     'attributes' => $attributes,
        //     'selectedAttributes' => $selectedAttributes,
        //     'materialAttributes' => $materialAttributes
        // ]);
    }
}
