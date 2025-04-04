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

    public function loadCategoriesWithParent()
    {
        return $this->pluck(['name', 'id'], [], [['parent_id', '<>', null]]);
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
        return $this->pluck(['id', 'name'], [], [], ['name', 'asc'], ['products']);
    }

    public function getSelectedCollections(string $id)
    {
        $category = $this->show($id);
        return $category->collections()->pluck('collections.id')->toArray();
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
    public function store(array $payload)
    {
        $uploadedImage = null;

        return transaction(function () use ($payload, &$uploadedImage) {
            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'categories');
                $payload['image'] = $uploadedImage;
            }

            if (!$category = $this->create($payload)) {
                errorResponse('Thêm danh mục thất bại!');
            }

            $category->collections()->sync($credentials['collection_id'] ?? []);

            if (!isset($payload['parent_id']) || !$payload['parent_id']) {
                $category->saveAsRoot();
            } else {
                $parent = $this->firstdByWhere(['*'], [['id', $payload['parent_id']]], ['parent']);
                if (!$parent) {
                    errorResponse("Không tìm thấy danh mục cha!");
                }
                $depth = $parent->depth + 1;
                $this->updateData($category->id, ['depth' => $depth]);
                $parent->appendNode($category);
            }

            return successResponse('Thêm danh mục thành công.', $category, 201);
        }, function () use (&$uploadedImage) {
            if (!empty($uploadedImage)) {
                deleteImage($uploadedImage);
            }
        });
    }

    public function show(string $id)
    {
        return $this->findById($id, ['*'], ['collections']);
    }

    public function update(string $id, array $credentials)
    {
        $uploadedImage = null;

        return transaction(function () use ($id, $credentials, &$uploadedImage) {
            $category = $this->findById($id);

            if (!$credentials['slug']) {
                $credentials['slug'] = generateSlug($credentials['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'categories');
                $credentials['image'] = $uploadedImage;
            }

            if (! $this->updateData($id, $credentials)) {
                return errorResponse('Cập nhật thất bại!');
            }

            $category->collections()->sync($credentials['collection_id'] ?? []);

            if (!$credentials['parent_id']) {
                $category->saveAsRoot();
            } else {
                $parent = $this->firstdByWhere(['*'], [['id', $credentials['parent_id']]], ['parent']);
                $depth = $parent->depth + 1;
                $this->updateData($id, ['depth' => $depth]);
                $parent->appendNode($category);
            }

            return successResponse('Lưu thay đổi thành công.');
        }, function () use (&$uploadedImage) {
            if (!empty($uploadedImage)) {
                deleteImage($uploadedImage);
            }
        });
    }
}
