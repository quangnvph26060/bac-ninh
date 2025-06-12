<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialImport;
use App\Models\MaterialImportDetail;
use App\Models\Type;
use Illuminate\Support\Carbon;

class MaterialService extends BaseService
{
    public function __construct(
        Material $material,
        public MaterialImport $materialImport,
        public MaterialImportDetail $materialImportDetail,
    ) {
        parent::__construct($material);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'code',
            'unit',
            'min_stock',
            'note',
            'created_at',
        ];

        return $this->queryBuilder(
            $columns,
            ['inventory'],
            false,
            ['unit']
        );
    }

    public function getMaterialNames()
    {
        return $this->model->select('id', 'name')->distinct()->pluck('name', 'id');
    }

    public function getUnits()
    {
        return $this->model->select('unit')->distinct()->pluck('unit', 'unit');
    }

    public function show($id)
    {
        return $this->findById($id);
    }

    public function store(array $data)
    {
        return transaction(function () use ($data) {


            $data['code'] ??= generateUniqueCode('materials');

            $material =  $this->create($data);

            return successResponse("Thêm mới vật tư thành công", $material, 201);
        });
    }

    public function update($id, $data)
    {
        return transaction(function () use ($id, $data) {

            $data['code'] ??= generateUniqueCode('materials');

            $this->updateData($id, $data);

            return successResponse("Cập nhật vật tư thành công", null, 201);
        });
    }

    public function search($term, $page)
    {
        $perPage = 10;

        $query = $this->model->query();

        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }

        $materials = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $materials->items(),
            'meta' => [
                'current_page' => $materials->currentPage(),
                'last_page' => $materials->lastPage(),
                'per_page' => $materials->perPage(),
                'total' => $materials->total(),
            ]
        ];
    }
}
