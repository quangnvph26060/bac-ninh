<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Material\MaterialRequest;
use App\Imports\MaterialsImport;
use App\Models\MaterialImport;
use App\Services\MaterialService;
use App\Services\SupplierService;
use App\Services\TypeService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MaterialController extends Controller
{
    use PaginateTrait;
    public function __construct(public MaterialService $materialService)
    {
    }

    public function index()
    {
        if (request()->ajax()) {

            $buider = $this->materialService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('min_stock', fn($row) => $row->min_stock ? number_format($row->min_stock, 0, ',', '') : 0)
                    ->addColumn('stock', fn($row) => $row->inventory ? number_format($row->inventory->quantity, 0, ',', '') : 0)
                    ->addColumn('final_update', fn($row) => $row->inventory ? $row->inventory->updated_at->format('d/m/Y H:i') : 'N/A')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }

        $units = $this->materialService->getUnits();

        return view('admin.material.index', compact('units'));
    }

    public function create()
    {
        $material = null;
        $units = $this->materialService->getUnits();

        return view('admin.material.save', compact('units', 'material'));
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

        return handleResponse($response['message'], $response['success'], $response['code'], $response['data'], false);
    }

    public function edit(string $id)
    {
        $units = $this->materialService->getUnits();

        $material = $this->materialService->show($id);

        return view('admin.material.save', compact('units', 'material'));
    }

    public function update(MaterialRequest $request, string $id)
    {
        $credentials = $request->validated();

        $response = $this->materialService->update($id, $credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }

    public function search(Request $request)
    {
        $term = $request->get('term', '');
        $page = $request->get('page', 1);

        $response = $this->materialService->search($term, $page);

        return successResponse("Lấy dữ liệu thành công", $response, 200, true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $import = new MaterialsImport(); // <-- KHỞI TẠO TRƯỚC
            Excel::import($import, $request->file('file')); // <-- TRUYỀN VÀO

            return back()->with([
                'success' => $import->success,
                'error' => $import->error,
                'import_errors' => $import->errors,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

}
