@switch($status)
    @case('draft')
        <span class="badge bg-light text-dark border">Draft</span>
    @break

    @case('pending')
        <span class="badge bg-warning">
            <i class="fas fa-hourglass-half me-1"></i>Pending
        </span>
    @break

    @case('confirmed_pending_production')
        <span class="badge bg-primary text-white">
            <i class="fas fa-check-circle me-1"></i>Confirmed, waiting for production
        </span>
    @break

    @case('in_production')
        <span class="badge bg-info text-dark">
            <i class="fas fa-industry me-1"></i>In production
        </span>
    @break

    @case('produced_awaiting_completion')
        <span class="badge bg-secondary text-white">
            <i class="fas fa-box-open me-1"></i>Produced, waiting for completion
        </span>
    @break

    @case('completed_waiting_for_shipment')
        <span class="badge bg-dark text-white">
            <i class="fas fa-truck-loading me-1"></i>Completed, waiting for shipment
        </span>
    @break

    @case('shipped')
        <span class="badge bg-success text-white">
            <i class="fas fa-truck me-1"></i>Shipped
        </span>
    @break

    @case('cancelled')
        <span class="badge bg-danger text-white">
            <i class="fas fa-times-circle me-1"></i>Cancelled
        </span>
    @break

    @case(null)
        <span>Not updated yet...</span>
    @break

    @case('bank_transfer')
        <span>Payment via wallet</span>
    @break

    @case('paypal')
        <span>Paypal payment</span>
    @break

    @case('1')
        <span class="bg_paid status_btn_order">Publish</span>
    @break

    @case('2')
        <span class="bg_unpaid status_btn_order">Pause</span>
    @break

    @default
@endswitch
