<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\MaterialImport;
use App\Models\SupplierPayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MaterialImportService extends BaseService
{
    public function __construct(
        MaterialImport $materialImport,
        public SupplierPayment $supplierPayment,
        public Inventory $inventory
    ) {
        parent::__construct($materialImport);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'code',
            'supplier_id',
            'date',
            'note',
            'created_by',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['supplier', 'details', 'debt.payments'],
            false,
            [],
            [],
            [],
            [],
            [],
            [],
            'date'
        );
    }

    public function store($data)
    {

        return transaction(function () use ($data) {
            $data['code'] ??= generateUniqueCode('material_imports');
            $data['created_by'] = auth('admin')->id();
            $data['date'] = Carbon::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d');

            $materials = collect($data['materials']);

            $total = $materials->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            if ($total < $data['summary_paid']) {
                return errorResponse("Số tiền phải trả không được vượt quá tổng tiền!", false, 400);
            }

            // Tạo phiếu nhập
            $materialImport = $this->create($data);

            // Tạo chi tiết
            foreach ($materials as $materialId => $item) {
                // Tạo chi tiết phiếu nhập
                $materialImport->details()->create([
                    'material_id' => $materialId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Cập nhật tồn kho
                $this->inventory->updateOrCreate(
                    ['material_id' => $materialId],
                    ['quantity' => DB::raw("quantity + {$item['quantity']}")]
                );
            }


            // Tạo công nợ
            if ($data['summary_paid'] >= 0) {
                $paid = $data['summary_paid'];

                $status = match (true) {
                    $paid == 0      => 'unpaid',
                    $paid < $total  => 'partial',
                    $paid == $total => 'paid',
                    default         => 'unpaid',
                };

                // Tạo công nợ
                $supplierDebt =  $materialImport->debt()->create([
                    'code'         => generateUniqueCode('supplier_debts'),
                    'supplier_id'  => $data['supplier_id'],
                    'total_amount' => $total,
                    'paid_amount'  => $paid,
                    'status'       => $status,
                    'note'         => "Công nợ phát sinh từ phiếu nhập nguyên vật liệu #{$materialImport->code}"
                ]);

                // Chỉ ghi nhận khoản thanh toán nếu đã thanh toán > 0
                if ($paid > 0) {
                    $supplierDebt->payments()->create([
                        'supplier_id' => $data['supplier_id'],
                        'date'        => now(),
                        'amount'      => $paid,
                        'note'        => "Thanh toán khi nhập nguyên vật liệu #{$materialImport->code}",
                        'created_by'  => auth('admin')->id(),
                    ]);
                }
            }

            return successResponse("Tạo phiếu nhập thành công", $materialImport, 201);
        });
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['supplier', 'employee', 'details.material', 'debt.payments']);
    }

    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $materialImport = $this->findById($id);

            $data['code'] ??= $materialImport->code;
            $data['updated_by'] = auth('admin')->id();
            $data['date'] = Carbon::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d');

            $materials = collect($data['materials']);

            $total = $materials->sum(fn($item) => $item['quantity'] * $item['unit_price']);

            $payments = collect($data['payments'] ?? []);
            $summaryPaid = $payments->sum(fn($p) => floatval(str_replace(',', '', $p['amount'] ?? 0)));

            if ($summaryPaid > $total) {
                return errorResponse("Tổng số tiền thanh toán không được vượt quá tổng tiền!", false, 400);
            }

            // Cập nhật phiếu nhập
            $materialImport->update($data);

            // Cập nhật chi tiết
            $existingDetailIds = $materialImport->details()->pluck('material_id')->toArray();

            // Thêm hoặc cập nhật
            foreach ($materials as $materialId => $item) {
                $detail = $materialImport->details()->updateOrCreate(
                    ['material_id' => $materialId],
                    [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                    ]
                );

                // Cập nhật tồn kho
                $quantityDiff = $item['quantity'] - optional($detail)->getOriginal('quantity');
                $this->inventory->updateOrCreate(
                    ['material_id' => $materialId],
                    ['quantity' => DB::raw("quantity + {$quantityDiff}")]
                );
            }

            // Xóa chi tiết không còn
            $materialIds = array_keys($materials->toArray());
            $materialImport->details()->whereNotIn('material_id', $materialIds)->each(function ($detail) {
                $this->inventory->where('material_id', $detail->material_id)
                    ->update(['quantity' => DB::raw("quantity - {$detail->quantity}")]);
                $detail->delete();
            });

            // Cập nhật công nợ
            $status = match (true) {
                $summaryPaid == 0      => 'unpaid',
                $summaryPaid < $total  => 'partial',
                $summaryPaid == $total => 'paid',
                default                => 'unpaid',
            };

            $supplierDebt =  $materialImport->debt()->updateOrCreate(
                ['material_import_id' => $materialImport->id],
                [
                    'code'         => generateUniqueCode('supplier_debts'),
                    'supplier_id'  => $data['supplier_id'],
                    'total_amount' => $total,
                    'paid_amount'  => $summaryPaid,
                    'status'       => $status,
                    'note'         => "Cập nhật công nợ từ phiếu nhập #{$materialImport->code}"
                ]
            );

            // Cập nhật thanh toán
            $supplierDebt->payments()->delete();

            foreach ($payments as $paymentKey => $payment) {
                if (floatval($payment['amount']) > 0) {
                    $paymentDate = Carbon::createFromFormat('d/m/Y', $payment['date'])->format('Y-m-d');

                    $supplierDebt->payments()->create([
                        'supplier_id' => $data['supplier_id'],
                        'date'        => $paymentDate,
                        'amount'      => floatval($payment['amount']),
                        'note'        => "Thanh toán từ phiếu nhập #{$materialImport->code}",
                        'created_by'  => auth('admin')->id(),
                    ]);
                }
            }

            return successResponse("Cập nhật phiếu nhập thành công", $materialImport, 200);
        });
    }
}
