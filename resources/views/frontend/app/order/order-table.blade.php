<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col">Mã đơn hàng</th>
            <th scope="col">Thông tin người nhận</th>
            <th scope="col">Trạng thái</th>
            <th scope="col">Thanh toán</th>
            <th scope="col">Số lượng</th>
            <th scope="col">Thanh toán</th>
            <th scope="col">Ngày tạo</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            <tr class="align-middle">
                <td><a href="" class="name">{{ $order->order_code }}</a></td>
                <td>
                    <div class="d-flex flex-column">
                        <a href="#" class="name">{{ $order->user?->name }}</a>
                        <p>{{ $order->phone_number ?? $order->user?->phone }}</p>
                    </div>
                </td>
                <td>@include('frontend.components.switch-status', ['status' => $order->status])</td>
                <td>
                    <div class="d-flex">
                        @switch($order->payment_status)
                            @case('pending')
                                <div class="bg_unpaid status_btn_order"><span class="px-2">Chưa thanh toán</span></div>
                            @break

                            @case('completed')
                                <div class="bg_paid status_btn_order"><span class="px-2">Đã thanh toán</span></div>
                            @break
                        @endswitch
                    </div>
                </td>
                <td>{{ $order->orderItems->sum('quantity') }} sản phẩm</td>
                <td>@include('frontend.components.switch-status', ['status' => $order->payment_method])</td>
                <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="fw-bold text-muted">Không có đơn hàng nào được tìm thấy</div>
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>

    {{ $orders->links('vendor.pagination.custom') }}
