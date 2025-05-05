@switch($status)
    @case('draft')
        <span class="badge bg-light text-dark border">Draft</span>
    @break

    @case('pending')
        <span class="badge bg-warning text-dark">Waiting for processing</span>
    @break

    @case('shipping')
        <span class="badge bg-info text-light">Shipping</span>
    @break

    @case('confirmed')
        <span class="badge bg-primary text-light">Confirmed</span>
    @break

    @case('completed')
        <span class="badge bg-success text-light">Complete</span>
    @break

    @case('cancelled')
        <span class="badge bg-danger text-light">Canceled</span>
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
