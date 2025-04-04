<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\ColectionRequest;
use App\Services\CategoryService;
use App\Services\CollectionService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    use PaginateTrait;

    public function __construct(public CollectionService $collectionService, public CategoryService $categoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->collectionService->pagination();

            return $this->processDataTable(
                $query,
                fn($datatable) => $datatable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'image']
            );
        }
        return view('admin.collection.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Thêm mới bộ sưu tập';

        $collection = null;

        return view('admin.collection.save', compact('title', 'collection'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ColectionRequest $request)
    {
        $payload = $request->validated();

        $response =  $this->collectionService->store($payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Cập nhật bộ sưu tập';

        $collection = $this->collectionService->show($id);

        $categories = $this->categoryService->loadCategoriesWithParent();

        $selectedCategories = $this->collectionService->getSelectedCategories($id);

        return view('admin.collection.save', compact('title', 'collection', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ColectionRequest $request, string $id)
    {
        $payload = $request->validated();

        $response =  $this->collectionService->update($id, $payload);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }

}
