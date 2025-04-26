@switch($status)
    @case('draft')
        <span class="badge bg-light text-dark">Nháp</span>
    @break

    @case('pending')
        <span class="badge bg-warning text-dark">Chờ xử lý</span>
    @break

    @case('processing')
        <span class="badge bg-primary text-light">Chuẩn bị hàng</span>
    @break

    @case('completed')
        <span class="badge bg-success text-light">Hoàn thành</span>
    @break

    @case('cancelled')
        <span class="badge bg-danger text-light">Đã hủy</span>
    @break

    @case('cod')
        <span>Thành toán tiền mặt</span>
    @break

    @case('bank_transfer')
        <span>Thành toán qua ví</span>
    @break

    @case('paypal')
        <span>Thanh toán paypal</span>
    @break

    @case('1')
        <span class="bg_paid status_btn_order">Xuất bản</span>
    @break

    @case('2')
        <span class="bg_unpaid status_btn_order">Tạm ngưng</span>
    @break

    @default
@endswitch
