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
        public Type $type
    ) {
        parent::__construct($material);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['types', 'importDetails'],
            false,
            [],
            [],
            [],
            [],
            [],
            ['importDetails' => 'quantity']
        );
    }

    public function getMaterialNames()
    {
        return $this->model->select('id', 'name')->distinct()->pluck('name', 'id');
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['importDetails.import', 'importDetails.type']);
    }

    public function store(array $data)
    {
        return transaction(function () use ($data) {
            // Tạo hoặc lấy material theo name
            $material = $this->model->firstOrCreate([
                'name' => $data['name']
            ]);

            // Tạo import
            $import = $this->materialImport->create([
                'import_date' => now(),
                'import_code' => $data['import_code'] ?? $this->generateImportCode(),
            ]);

            foreach ($data['data'] as $item) {
                // Tạo hoặc lấy type theo name
                $type = $this->type->firstOrCreate([
                    'name' => $item['type_name']
                ]);

                // Ghi vào bảng trung gian material_type nếu chưa có
                $material->types()->syncWithoutDetaching([$type->id]);

                // Ghi chi tiết import
                $material->importDetails()->create([
                    'material_import_id' => $import->id,
                    'type_id' => $type->id,
                    'supplier_name' => $item['supplier_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                ]);
            }

            return successResponse("Nhập vật liệu thành công", null, 201);
        });
    }

    public function generateImportCode(): string
    {
        $date = Carbon::now()->format('Ymd');
        $countToday = MaterialImport::whereDate('created_at', Carbon::today())->count() + 1;

        return 'IMP' . $date . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
    }
}
