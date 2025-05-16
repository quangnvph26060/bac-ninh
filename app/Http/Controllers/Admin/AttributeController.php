<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\AttributeRequest;
use App\Models\Attribute;
use App\Services\AttributeService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class AttributeController extends Controller
{

    use PaginateTrait;

    public function __construct(public AttributeService $attributeService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Attribute::class);

        if (request()->ajax()) {
            $query = $this->attributeService->pagination();

            return $this->processDataTable(
                $query,
                fn($datatable) => $datatable
                    ->editColumn('values', fn($row) => $row->values->pluck('value')->implode(', '))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }

        return view('admin.attribute.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Attribute::class);

        $title = 'Thêm mới thuộc tính';
        $attribute = null;

        return view('admin.attribute.save', compact('title', 'attribute'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeRequest $request)
    {
        $this->authorize('create', Attribute::class);

        $payload = $request->validated();

        $response = $this->attributeService->store($payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit', Attribute::class);

        $title = 'Cập nhật thuộc tính';
        $attribute = $this->attributeService->show($id);
        return view('admin.attribute.save', compact('title', 'attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeRequest $request, string $id)
    {
        $this->authorize('edit', Attribute::class);

        $payload = $request->validated();

        $response = $this->attributeService->update($id, $payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
