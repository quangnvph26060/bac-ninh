<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequestFormRequest;
use App\Models\Bom;
use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialUsage;
use App\Models\MaterialUsageDetail;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\MaterialRequestService;
use App\Services\OrderService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    use PaginateTrait;

    public function __construct(
        public MaterialRequestService $materialRequestService,
        public OrderService $orderService
    ) {}

    public function index()
    {
        if (request()->ajax()) {

            $buider = $this->materialRequestService->pagination();

            return $this->processDataTable(
                $buider,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y'))
                    ->addColumn(
                        'operations',
                        fn($row) =>
                        '
                            <a href="/admin/material-requests/' . $row->id . '/edit"
                                class="btn btn-primary btn-sm table-actions btn-operation-edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button type="button" data-id="' . $row->id . '"
                                class="btn btn-danger btn-sm table-actions handle-delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        '
                    ),
                ['operations']
            );
        }
        return view('admin.material-request.index');
    }

    public function create()
    {
        return view('admin.material-request.save');
    }

    public function store(MaterialRequestFormRequest $request)
    {
        try {
            DB::beginTransaction();

            [$itemId] = explode('-', $request->item_id);

            $exists = MaterialRequest::where('order_id', $request->order_id)
                ->where('order_item_id', $itemId)
                ->where('status', 'pending') // chỉ kiểm tra bản ghi chưa xử lý
                ->exists();

            if ($exists) {
                return errorResponse("Yêu cầu xuất vật tư cho sản phẩm này đang chờ xử lý!", true);
            }

            $materialRequest = MaterialRequest::create([
                'order_id' => $request->order_id,
                'order_item_id' => $itemId,
                'code' => generateUniqueCode('material_requests'),
                'quantity' => count($request->materials),
                'note' => $request->note,
                'created_by' => auth('admin')->id(),
                'created_at' => now()
            ]);

            $data = [];

            foreach ($request->materials as $key => $material) {
                $data[] = [
                    'material_id' => $key,
                    'quantity' => $material['quantity'],
                    'note' => $material['note']
                ];
            }

            $materialRequest->items()->createMany($data);

            DB::commit();

            return handleResponse("Tạo phiếu yêu cầu xuất thành công", true, 201, ['redirect' => '/admin/material-requests']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return errorResponse("Đã có lỗi xảy ra, vui lòng thử lại sau!", true);
        }
    }

    public function edit(string $id)
    {
        $materialRequest = MaterialRequest::query()->with(['order', 'items.material', 'orderItem.product', 'orderItem.productVariant'])->findOrFail($id);
        $order = $materialRequest->order;
        $orderItem = $materialRequest->orderItem;
        // dd($orderItem);
        return view('admin.material-request.save', compact('materialRequest', 'order', 'orderItem'));
    }

    public function update(MaterialRequestFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $materialRequest = MaterialRequest::with('items')->findOrFail($id);

            // ❗ Chỉ cho sửa nếu còn trạng thái pending
            if ($materialRequest->status !== 'pending') {
                return errorResponse("Chỉ phiếu ở trạng thái chờ xử lý mới có thể chỉnh sửa", true);
            }

            [$itemId] = explode('-', $request->item_id);

            // 👉 Cập nhật thông tin chính
            $materialRequest->update([
                'order_id' => $request->order_id,
                'order_item_id' => $itemId,
                'quantity' => count($request->materials),
                'note' => $request->note,
                'updated_by' => auth('admin')->id(),
                'updated_at' => now()
            ]);

            // 👉 Cập nhật danh sách vật tư
            $newMaterials = collect($request->materials); // mảng [material_id => ['quantity' => ..., 'note' => ...]]

            $existingItems = $materialRequest->items->keyBy('material_id');

            $newData = [];

            // Thêm hoặc cập nhật
            foreach ($newMaterials as $materialId => $material) {
                if ($existingItems->has($materialId)) {
                    // Nếu đã có → update
                    $existingItems[$materialId]->update([
                        'quantity' => $material['quantity'],
                        'note' => $material['note'] ?? null
                    ]);
                    // Xóa khỏi danh sách cũ để lát nữa xác định phần còn lại là cần xóa
                    $existingItems->forget($materialId);
                } else {
                    // Nếu là mới → thêm
                    $newData[] = [
                        'material_id' => $materialId,
                        'quantity' => $material['quantity'],
                        'note' => $material['note'] ?? null
                    ];
                }
            }

            // Xóa các vật tư không còn trong danh sách mới
            if ($existingItems->isNotEmpty()) {
                $materialRequest->items()->whereIn('id', $existingItems->pluck('id'))->delete();
            }

            // Tạo mới các dòng mới
            if (!empty($newData)) {
                $materialRequest->items()->createMany($newData);
            }

            DB::commit();

            return handleResponse("Cập nhật phiếu yêu cầu thành công", true, 200, ['redirect' => '/admin/material-requests']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return errorResponse("Có lỗi xảy ra khi cập nhật!", true);
        }
    }

    public function orderSelect(Request $request)
    {
        $orders = $this->orderService->orderSelect($request);

        return response()->json($orders);
    }

    public function getItemsByOrderId(Request $request, $orderId)
    {
        $orderItems = $this->orderService->getItemsByOrderId($orderId);

        return response()->json($orderItems);
    }

    public function getBoms(Request $request)
    {
        [$orderItemId, $productId, $variantId] = explode('-', $request->item_id);

        // Ưu tiên tìm BOM theo variant
        if (!empty($variantId)) {
            $boms = Bom::query()
                ->where([
                    'productable_type' => ProductVariant::class,
                    'productable_id' => $variantId,
                ])
                ->with('bomItems.material')
                ->first();
        }

        // Nếu không có hoặc không tìm thấy variant BOM → fallback qua sản phẩm đơn
        if (empty($boms)) {
            $boms = Bom::query()
                ->where([
                    'productable_type' => Product::class,
                    'productable_id' => $productId,
                ])
                ->with('bomItems.material')
                ->first();
        }

        // Trả dữ liệu JSON nếu có
        if ($boms) {
            return response()->json([
                'status' => true,
                'materials' => $boms->bomItems->map(function ($item) {
                    return [
                        'id' => $item->material_id,
                        'code' => $item->material->code,
                        'name' => $item->material->name,
                        'unit' => $item->material->unit,
                        'quantity' => number_format($item->quantity_required, 0)
                    ];
                }),
            ]);
        }

        return response()->json([
            'status' => false,
            'materials' => [],
            'message' => 'Không tìm thấy BOM.'
        ]);
    }

    public function destroy(string $id)
    {
        $materialRequest = MaterialRequest::query()->findOrFail($id);

        if ($materialRequest->status === "approved") return errorResponse('Yêu cầu đã được xác nhận, không thể xóa.', true);
        $materialRequest->delete();

        return handleResponse("Xóa yêu cầu xuất vật tư thành công.", true, 200, null, false);
    }
}
