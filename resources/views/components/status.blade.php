@switch($status)
    @case('pending')
        <span class="badge bg-warning text-dark">Pending</span>
    @break

    @case('confirmed')
        <span class="badge rounded-pill bg-secondary">Confirmed</span>
    @break

    @case('shipping')
        <span class="badge rounded-pill bg-info text-dark">Shipping</span>
    @break

    @case('completed')
        <span class="badge rounded-pill bg-success">Completed</span>
    @break

    @case('cancelled')
        <span class="badge rounded-pill bg-danger">Cancelled</span>
    @break
@endswitch
