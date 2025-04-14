@switch($status)
    @case('pending')
        <span class="badge bg-warning text-dark">Chờ xử lý</span>
    @break

    @case('processing')
        <span class="badge bg-primary">Chuẩn bị hàng</span>
    @break

    @case('completed')
        <span class="badge bg-success">Hoàn thành</span>
    @break

    @case('cancelled')
        <span class="badge bg-danger">Đã hủy</span>
    @break

    @case('cod')
        <span>Thành toán tiền mặt</span>
    @break

    @case('bank_transfer')
        <span>Thành toán chuyển khoản</span>
    @break

    @case('paypal')
        <span>Thanh toán paypal</span>
    @break

    @default
@endswitch
