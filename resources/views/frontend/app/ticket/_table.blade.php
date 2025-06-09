<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" style="width: 12%;">Ticket ID</th>
            <th scope="col">Order ID/ Name</th>
            <th scope="col">Subject</th>
            <th scope="col">Status</th>
            <th scope="col">Created by</th>
            <th scope="col">Created Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($tickets as $ticket)
            <tr>
                <td class="align-middle">{{ $ticket->code }}</td>
                <td class="align-middle">
                    <a href="{{ route('orders.show', $ticket->order->order_code) }}" class="fw-bold d-block">
                        {{ $ticket->order->order_code }}
                    </a>
                    <span>{{ $ticket->order->order_name }}</span>
                </td>
                <td class="align-middle">{{ $ticket->subject->title }}</td>
                <td class="align-middle">
                    @switch($ticket->status)
                        @case('open')
                            <span class="badge-soft badge-soft-primary">Open</span>
                        @break

                        @case('resolving')
                            <span class="badge-soft badge-soft-warning">Resolving</span>
                        @break

                        @case('resolved')
                            <span class="badge-soft badge-soft-success">Resolved</span>
                        @break

                        @case('closed')
                            <span class="badge-soft badge-soft-secondary">Closed</span>
                        @break

                        @default
                            <span class="badge-soft badge-soft-dark">Unknown</span>
                    @endswitch
                </td>
                <td class="align-middle">
                    {{ $ticket->user->email }}
                </td>
                <td class="align-middle">
                    {{ $ticket->created_at->format('F j, Y \a\t g:i a') }}
                </td>
                <td class="align-middle">
                    <button class="btn btn-sm btn-primary btn-view-ticket" title="Xem chi tiết"
                        data-id="{{ $ticket->id }}">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="fw-bold text-muted">No tickets found</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $tickets->links('vendor.pagination.custom') }}
