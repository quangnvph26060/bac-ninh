<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class BrandService extends BaseService
{
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'logo',
            'created_at',
            'website',
            'status'
        ];

        return $this->queryBuilder(
            $columns,
        );
    }

    public function getBrandAll($useWithCount = true)
    {
        $withCounts =  $useWithCount ? ['products'] : [];
        return $this->pluck(['id', 'name'], [], [], ['name', 'asc'], $withCounts);
    }

    public function store(array $payload)
    {
        $uploadedImage = null;

        return transaction(function () use ($payload, &$uploadedImage) {
            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('logo')) {
                $uploadedImage = uploadImages('logo', 'brands');
                $payload['logo'] = $uploadedImage;
            }

            if (!$brand = $this->create($payload)) {
                errorResponse('Thêm thương hiệu thất bại!');
            }

            return successResponse('Thêm thương hiệu thành công.', $brand, 201);
        }, function () use (&$uploadedImage) {
            if (!empty($uploadedImage)) {
                deleteImage($uploadedImage);
            }
        });
    }

    public function show(string $id)
    {
        return $this->findById($id);
    }

    public function update(string $id, array $payload)
    {
        $uploadedImage = null;

        return transaction(function () use ($id, $payload, &$uploadedImage) {
            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('logo')) {
                $uploadedImage = uploadImages('logo', 'brands');
                $payload['logo'] = $uploadedImage;
            }

            if (!$brand = $this->updateData($id, $payload)) {
                errorResponse('Thêm thương hiệu thất bại!');
            }

            return successResponse('Thêm thương hiệu thành công.', $brand, 201);
        }, function () use (&$uploadedImage) {
            if (!empty($uploadedImage)) {
                deleteImage($uploadedImage);
            }
        });
    }
}
