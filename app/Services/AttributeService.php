<?php

namespace App\Services;

use App\Models\Attribute;

class AttributeService extends BaseService
{
    public function __construct(public Attribute $attribute)
    {
        parent::__construct($attribute);
    }

    public function pagination()
    {
        $columns = ['id', 'name', 'slug', 'status'];

        return $this->queryBuilder($columns, ['values']);
    }

    public function getPluck()
    {
        return $this->pluck(['id', 'name'], [], [], ['name', 'asc']);
    }

    public function store(array $payload)
    {
        return transaction(function () use ($payload) {

            $payload['slug'] = generateSlug($payload['name']);

            if (!$attribute = $this->create($payload)) {
                return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            if (isset($payload['values']) && is_array($payload['values'])) {
                $values = collect($payload['values'])->map(fn($item) =>  ['attribute_id' => $attribute->id, 'value' => $item['value'], 'status' => ($item['status'] ?? null) == 'on' ? 1 : 2]);
                $attribute->values()->createMany($values);
            }


            return successResponse('Thêm mới thuộc tính thành công.');
        });
    }

    public function show(string $id)
    {
        return $this->findById($id, ['*'], ['values']);
    }

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($payload, $id) {

            $payload['slug'] = generateSlug($payload['name']);

            if (! $attribute = $this->updateData($id, $payload)) {
                return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            $oldValues = $attribute->values->keyBy('id');

            $newValues = [];

            foreach ($payload['values'] as $key => $item) {
                if (isset($key) && isset($oldValues[$key])) {
                    $oldValues[$key]->update([
                        'value' => $item['value'],
                        'status' => ($item['status'] ?? null) == 'on' ? 1 : 2
                    ]);
                } else {
                    $newValues[] = [
                        'attribute_id' => $attribute->id,
                        'value' => $item['value'],
                        'status' => ($item['status'] ?? null) == 'on' ? 1 : 2
                    ];
                }
            }

            if (!empty($newValues)) {
                $attribute->values()->createMany($newValues);
            }

            foreach ($oldValues as $key => $oldValue) {
                if (!isset($payload['values'][$key])) {
                    $oldValue->update(['status' => 2]);
                    // $oldValue->delete();
                }
            }

            return successResponse('Lưu thay đổi thành công.');
        });
    }

    public function getValueByAttributeId(string $attributeId)
    {
        $attribute = $this->findById($attributeId, ['*'], ['values']);
        return $attribute->values->pluck('value', 'id')->toArray();
    }
}
