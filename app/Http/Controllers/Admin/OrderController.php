<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendOrderCancelledEmail;
use App\Models\Order;
use App\Models\Wallet;
use App\Services\OrderService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use PaginateTrait;

    public function __construct(protected OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index()
    {
        $this->authorize('view', Order::class);

        if (request()->ajax()) {
            $query = $this->orderService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('product_count', fn($row) => $row->orderItems->sum('quantity'))
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                    ->editColumn('status', fn($row) => view('components.status', ['status' => $row->status]))
                    ->editColumn('customer_information', function ($row) {
                        return '<strong>' . e($row->full_name) . '</strong><br>' .
                            '<a href="mailto:' . e($row->email) . '">' . e($row->email) . '</a><br>' .
                            e($row->phone_number);
                    })->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations', 'customer_information', 'status']
            );
        }
        return view('admin.order.index');
    }

    public function getItemByCode(Request $request)
    {
        $this->authorize('showItem', Order::class);

        $request->validate([
            'code' => 'required|exists:orders,order_code'
        ]);

        $order = $this->orderService->getItemsByOrderCode($request->code);

        return successResponse("get data successfully!", $order, 200, true);
    }

    public function edit(string $id)
    {
        $this->authorize('edit', Order::class);

        $order = $this->orderService->show($id);
        return view('admin.order.edit', compact('order'));
    }

    public function cancelOrder(Request $request)
    {
        $this->authorize('cancel', Order::class);

        $credentials = $request->validate([
            'code' => 'required|exists:orders,order_code',
            'reason' => 'required|string|max:400',
            'user_id' => 'required|exists:users,id'
        ]);

        return DB::transaction(function () use ($credentials) {
            $order = Order::query()->with('user')->where('order_code', $credentials['code'])->firstOrFail();

            if ($order->status !== "pending") {
                return errorResponse("Đơn hàng của bạn không thể bị hủy!", true, 400);
            }

            $order->reason = $credentials['reason'];
            $order->status = "cancelled";
            $order->payment_status = "refunded";
            $order->save();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $credentials['user_id']],
                ['balance' => 0]
            );

            $amount = $order->total;
            $note = "HOÀN TIỀN DO QUẢN TRỊ VIÊN HỦY ĐƠN HÀNG #{$order->order_code}";
            $balanceBefore = $wallet->balance;

            $wallet->increment('balance', $amount);
            $balanceAfter = $wallet->fresh()->balance;

            $wallet->transactions()->create([
                'code' => generateTransactionCode(),
                'amount' => $amount,
                'note' => $note,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'type' => "deposit"
            ]);

            Mail::to($order->user->email)->send(new SendOrderCancelledEmail($order, $balanceBefore, $balanceAfter));
            return successResponse("Hủy đơn hàng thành công.", ['wallet' => formatPrice($wallet->balance)], 200, true);
        });
    }

    public function printInvoice(string $id)
    {
        $this->authorize('print', Order::class);

        $order = $this->orderService->show($id);
        return view('frontend.template.invoice', compact('order'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $this->authorize('changeStatus', Order::class);

        $request->validate(
            [
                'status' => 'required|in:pending,confirmed_pending_production,in_production,produced_awaiting_completion,completed_waiting_for_shipment,shipped',
            ],
            __('request.messages'),
            [
                'status' => 'Trạng thái'
            ]
        );

        $response = $this->orderService->updateStatus($id, $request->status);

        return handleResponse($response['message'], $response['success'], $response['code'], $response['data'], false);
    }

    public function barcodeScanner()
    {
        return view('admin.order.barcode-scanner');
    }

    public function getByBarcode(Request $request)
    {
        $order = Order::query()->where('barcode', $request->barcode)->first();

        if (!$order) {
            return errorResponse("Không tìm thấy đơn hàng!", true, 404);
        }

        return successResponse("Lấy đơn hàng thành công!",route('admin.orders.edit', $order->id), 200, true);
    }
}


// $order = Order::query()->where('order_code', $credentials['code'])->firstOrFail();

// if ($order->status !== "pending") return errorResponse("Your order cannot be cancelled.", true, 400);

// $order->reason = $credentials['reason'];
// $order->status = "cancelled";
// $order->payment_status = "refunded";
// $order->save();

// $wallet = Wallet::firstOrCreate(
//     ['user_id' => $credentials['user_id']],
//     ['balance' => 0]
// );

// $amount = $order->total;
// $note = "REFUND DUE TO ADMIN CANCELLATION OF ORDER #{$order->order_code}";

// $balanceBefore = $wallet->balance;

// $wallet->increment('balance', $amount);

// $balanceAfter = $wallet->fresh()->balance;

// $wallet->transactions()->create([
//     'code' => generateTransactionCode(),
//     'amount' => $amount,
//     'note' => $note,
//     'balance_before' => $balanceBefore,
//     'balance_after' => $balanceAfter,
//     'type' => "deposit"
// ]);

// dispatch(new SendOrderCancelledEmail($order, $balanceBefore, $balanceAfter));

// return successResponse("Hủy đơn hàng thành công.", ['wallet' => formatPrice($wallet->balance)], 200, true);
