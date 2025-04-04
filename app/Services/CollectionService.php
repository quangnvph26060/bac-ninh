<?php

namespace App\Services;

use App\Models\Collection;

class CollectionService extends BaseService
{

    public function __construct(public Collection $collection)
    {
        parent::__construct($collection);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'slug',
            'created_at',
            'status',
        ];

        return $this->queryBuilder(
            $columns,
            [],
        );
    }

    public function getPluckCollection()
    {
        return $this->pluck(['name', 'id']);
    }

    public function getSelectedCategories(string $id)
    {
        return $this->getSelected($id, 'categories', 'categories.id');
    }

    public function store(array $payload)
    {
        return transaction(function () use ($payload) {
            if (!$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (! $collection = $this->create($payload)) {
                return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            $collection->categories()->sync($payload['category_id'] ?? []);

            return successResponse('Thêm bộ sưu tập thành công.', 201);
        });
    }

    public function show(string $id)
    {
        return $this->findById($id);
    }

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($id, $payload) {
            if (!$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (!$collection = $this->updateData($id, $payload)) {
                return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            $collection->categories()->sync($payload['category_id'] ?? []);

            return successResponse('Lưu thay đổi thành công.', 200);
        });
    }
}
