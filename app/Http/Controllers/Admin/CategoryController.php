<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Services\CategoryService;
use App\Services\CollectionService;
use App\Traits\PaginateTrait;

class CategoryController extends Controller
{
    use PaginateTrait;

    public function __construct(public CategoryService $categoryService, public CollectionService $collectionService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        if (request()->ajax()) {
            $categories = collect($this->categoryService->pagination());

            return $this->processDataTable(
                $categories,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('name', fn($row) => str_repeat('── ', $row->depth) . $row->name)
                    ->editColumn('parent_id', fn($row) => $row->parent->name ?? '---------')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->editColumn('image', fn($row) => '<img width="40" height="40" src="' . showImage($row->image) . '" alt="' . $row->name . '" />')
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'image'],
                'of'
            );
        }

        return view('admin.category.index');
    }

    public function create()
    {
        $title = 'Thêm danh mục';
        $categories = $this->categoryService->getCategoryTreeFlatWithDepth();
        $collections = $this->collectionService->getPluckCollection();
        $category = null;
        return view('admin.category.save', compact('title', 'category', 'categories', 'collections'));
    }
    public function store(CategoryRequest $request)
    {
        $credentials = $request->validated();

        $response = $this->categoryService->store($credentials);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    public function edit(string $id)
    {
        $title = 'Sửa danh mục';

        $category = $this->categoryService->show($id);

        $categories = $this->categoryService->getCategoryTreeFlatWithDepth();

        $collections = $this->collectionService->getPluckCollection();

        $selectedCollections = $this->categoryService->getSelectedCollections($id);

        return view('admin.category.save', compact('category', 'categories', 'collections', 'selectedCollections',  'title'));
    }

    public function update(string $id, CategoryRequest $request)
    {
        $credentials = $request->validated();

        $response = $this->categoryService->update($id, $credentials);

        return handleResponse($response['message'], $response['success'], $response['code']);
    }
}
