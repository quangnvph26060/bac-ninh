<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" style="width: 10%;">Order Code</th>
            <th scope="col">Information recipient</th>
            <th scope="col">Order Name</th>
            <th scope="col">Status</th>
            <th scope="col">Payment</th>
            <th scope="col">Quantity</th>
            <th scope="col">Total</th>
            <th scope="col">Reason</th>
            <th scope="col">Created At</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orders as $order)
            <tr class="align-middle">
                <td>
                    <a href="{{ route('orders.show', $order->order_code) }}" class="name">{{ $order->order_code }}</a>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <a href="{{ route('orders.show', $order->order_code) }}"
                            class="name">{{ $order->user?->name }}</a>
                        <a href="mailto:{{ $order->user?->email }}">{{ $order->user?->email }}</a>
                        <p>{{ $order->phone_number ?? $order->user?->phone }}</p>
                    </div>
                </td>
                <td>
                    {{ $order->order_name }}
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
                <td>{{ $order->reason ?? 'N/A' }}</td>
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
