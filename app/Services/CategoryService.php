<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService extends BaseService
{

    public function __construct(public Category $category)
    {
        parent::__construct($category);
    }

    public function pagination()
    {
        $categories = $this->all(['id', 'name', 'image', 'status', 'parent_id', 'depth', 'created_at'], ['parent:id,name,image,status,parent_id,depth,created_at']);

        return $this->sortCategories($categories);
    }

    private function sortCategories($categories, $parentId = null)
    {
        $result = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $result[] = $category;

                // Gọi đệ quy để lấy danh mục con
                $children = $this->sortCategories($categories, $category->id);
                $result = array_merge($result, $children);
            }
        }

        return $result;
    }

    public function getCategoryTreeFlatWithDepth()
    {
        return $this->category->defaultOrder()->get()->map(fn($category)
        =>
        [
            'id' => $category->id,
            'name' => str_repeat('── ', $category->depth) . $category->name,
            'depth' => $category->depth,
        ]);
    }
    public function getCategoryAll()
    {
        return $this->pluck(['name', 'id'], [], [], ['name', 'asc']);
    }

    public function getCategoryAllStaff()
    {
        try {
            Log::info('Fetching all categories');
            return $this->categories->all();
        } catch (Exception $e) {
            Log::error('Failed to fetch categories: ' . $e->getMessage());
            throw new Exception('Failed to fetch categories');
        }
    }
    public function createCategory(array $data): Category
    {
        try {
            Log::info('Creating new category');
            $category = $this->categories->create($data);
            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create category: ' . $e->getMessage());
            throw new Exception('Failed to create category');
        }
    }

    public function show(string $id)
    {
        return $this->findById($id);
    }

    public function update(string $id, array $credentials)
    {
        if (!$credentials['slug']) {
            $credentials['slug'] = generateSlug($credentials['name']);
        }

        if (hasFile('image')) {
            $credentials['image'] = uploadImages('image', 'categories');
        }

        if (!$category = $this->updateData($id, $credentials)) {
            return errorResponse('Cập nhật thất bại!');
        }

        if (!$credentials['parent_id']) {
            $category->saveAsRoot();
        } else {
            $parent = $this->firstdByWhere(['*'], [['id', $credentials['parent_id']]], ['parent']);
            $depth = $parent->depth + 1;
            $this->updateData($id, ['depth' => $depth]);
            $parent->appendNode($category);
        }

        return successResponse('Lưu thay đổi thành công.');
    }

    public function deleteCategory(int $id): void
    {
        DB::beginTransaction();
        try {
            Log::info("Deleting category with ID: $id");
            $category = $this->categories->findOrFail($id);
            $category->delete();
            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Category not found: ' . $e->getMessage());
            throw new ModelNotFoundException('Category not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete category: ' . $e->getMessage());
            throw new Exception('Failed to delete category');
        }
    }
    public function findOrFailCategory($id)
    {
        try {
            Log::info('Creating new category');
            $category = $this->categories->findOrFail($id);

            return $category;
        } catch (Exception $e) {

            Log::error('Failed to find category: ' . $e->getMessage());
            throw new Exception('Failed to find category');
        }
    }
    public function findCategoryByName($name): LengthAwarePaginator
    {
        try {
            $category = $this->categories->where('name', 'LIKE', '%' . $name . '%')->paginate(10);
            return $category;
        } catch (Exception $e) {
            Log::error('Failed to find category: ' . $e->getMessage());
            throw new Exception('Failed to find category');
        }
    }
}
