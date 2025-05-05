<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col">Mã đơn hàng</th>
            <th scope="col">Thông tin người nhận</th>
            <th scope="col">Trạng thái</th>
            <th scope="col">Thanh toán</th>
            <th scope="col">Số lượng</th>
            <th scope="col">Tổng tiền</th>
            <th scope="col">Ngày tạo</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            <tr class="align-middle">
                <td>
                    <div class="d-flex flex-column">
                        <a href="{{ route('orders.show', $order->order_code) }}"
                            class="name">{{ $order->order_code }}</a>
                        <p>{{ $order->order_name }}</p>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <a href="{{ route('orders.show', $order->order_code) }}"
                            class="name">{{ $order->user?->name }}</a>
                        <p>{{ $order->phone_number ?? $order->user?->phone }}</p>
                    </div>
                </td>
                <td>@include('frontend.components.switch-status', ['status' => $order->status])</td>
                <td>
                    <div class="d-flex">
                        @switch($order->payment_status)
                            @case('pending')
                                <div class="bg_unpaid status_btn_order"><span class="px-2">Not yet paid</span></div>
                            @break

                            @case('completed')
                                <div class="bg_paid status_btn_order"><span class="px-2">Paid</span></div>
                            @break

                            @case('refunded')
                                <div class="bg_refunded status_btn_order"><span class="px-2">Refunded</span></div>
                            @break
                        @endswitch

                    </div>
                </td>
                <td>{{ $order->orderItems->sum('quantity') }} product</td>
                <td>${{ formatPrice($order->total) }}</td>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="fw-bold text-muted">No orders found</div>
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{ $orders->links('vendor.pagination.custom') }}
