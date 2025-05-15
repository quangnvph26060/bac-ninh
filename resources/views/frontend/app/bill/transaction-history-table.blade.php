<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" style="width: 10%">Transaction code</th>
            <th scope="col" class="text-center">Transaction amount</th>
            <th scope="col" class="text-center">Balance before</th>
            <th scope="col" class="text-center">Balance after</th>
            <th scope="col" class="">Note</th>
            <th scope="col" class="">Day</th>
            <th scope="col" class="">Confirmation time</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($walletTransactions as $walletTransaction)
            @php
                $class = 'tran-negative'; // mặc định là rút tiền
                if ($walletTransaction->status === 'failure') {
                    $class = 'tran-failed';
                } elseif ($walletTransaction->type === 'deposit') {
                    $class = 'tran-positive';
                }
            @endphp
            <tr>
                <td class="{{ $class }} fw-bold">{{ $walletTransaction->code }}</td>
                <td class="{{ $class }} text-center">
                    ${{ formatPrice($walletTransaction->amount) }}
                </td>
                <td class="{{ $class }} text-center">${{ formatPrice($walletTransaction->balance_before) }}</td>
                <td class="{{ $class }} text-center">${{ formatPrice($walletTransaction->balance_after) }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->note ?? 'N/A' }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->created_at->format('d-m-Y H:i') }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->status === 'complete' ? $walletTransaction->updated_at->format('d-m-Y H:i') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="fw-bold text-muted">No transaction found</div>
                </td>
            </tr>
        @endforelse

    </tbody>

</table>

{{ $walletTransactions->links('vendor.pagination.custom') }}
