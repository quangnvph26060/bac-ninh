<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Categories;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\PaginateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use PaginateTrait;

    public function __construct(public CategoryService $categoryService)
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
                    ->editColumn('parent_id', fn($row) => $row->parent->name ?? 'Unknown')
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
        $category = null;
        return view('admin.category.save', compact('title', 'category'));
    }
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            session()->flash('success', 'Thêm danh mục mới thành công');
            return redirect()->route('admin.category.index');
        } catch (Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $title = 'Sửa danh mục';

        $category = $this->categoryService->show($id);

        $categories = $this->categoryService->getCategoryTreeFlatWithDepth();

        return view('admin.category.save', compact('title', 'category', 'categories'));
    }

    public function update(string $id, CategoryRequest $request)
    {
        $credentials = $request->validated();

        $messgae =  $this->categoryService->update($id, $credentials);

        return handleResponse($messgae);
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            $category = Categories::orderByDesc('created_at')->paginate(10);
            $view = view('admin.category.table', compact('category'))->render();

            return response()->json(['success' => true, 'message' => 'Xoá danh mục thành công!', 'table' => $view]);
        } catch (Exception $e) {
            Log::error('Failed to delete category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể xóa danh mục']);
        }
    }
}
