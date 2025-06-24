<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MaterialRequestService;
use App\Services\OrderService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

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
            );
        }
        return view('admin.material-request.index');
    }

    public function create()
    {

        return view('admin.material-request.create');
    }

    public function orderSelect(Request $request)
    {
        $orders = $this->orderService->orderSelect($request);

        return response()->json($orders);
    }

    public function getItemsByOrderId(Request $request, $orderId)
    {
        $orderItems = $this->orderService->getItemsByOrderId($orderId);

        // dd($orderItems);

        // $items = \App\Models\OrderItem::with('productVariant.product') // lấy cả tên sản phẩm cha nếu cần
        //     ->where('order_id', $orderId)
        //     ->get();

        // $results = $items->map(function ($item) {
        //     $variant = $item->productVariant;
        //     $productName = $variant->product->name ?? '---';
        //     $sku = $variant->sku ?? '';
        //     return [
        //         'id' => $variant->id,
        //         'text' => "$productName - SKU: $sku (SL: $item->quantity)"
        //     ];
        // });

        return response()->json($orderItems);
    }
}
