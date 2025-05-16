@switch($status)
    @case('pending')
        <span class="badge bg-warning rounded-pill">
            <i class="fas fa-hourglass-half me-1"></i> Chờ xác nhận
        </span>
    @break

    @case('confirmed_pending_production')
        <span class="badge bg-primary rounded-pill">
            <i class="fas fa-check-circle me-1"></i> Đã xác nhận, chờ sản xuất
        </span>
    @break

    @case('in_production')
        <span class="badge bg-info text-dark rounded-pill">
            <i class="fas fa-industry me-1"></i> Đang sản xuất
        </span>
    @break

    @case('produced_awaiting_completion')
        <span class="badge bg-info text-dark rounded-pill">
            <i class="fas fa-industry me-1"></i> Đang sản xuất
        </span>
    @break

    @case('produced_awaiting_completion')
        <span class="badge bg-secondary rounded-pill">
            <i class="fas fa-box-open me-1"></i> Đã sản xuất xong, chờ hoàn thiện
        </span>
    @break

    @case('completed_waiting_for_shipment')
        <span class="badge bg-dark rounded-pill">
            <i class="fas fa-truck-loading me-1"></i> Đã hoàn thiện, chờ giao hàng
        </span>
    @break

    @case('cancelled')
        <span class="badge bg-danger rounded-pill">
            <i class="fas fa-times-circle me-1"></i> Đã hủy
        </span>
    @break
@endswitch
