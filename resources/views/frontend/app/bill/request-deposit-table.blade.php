<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" class="text-center" style="width: 10%">Invoice code</th>
            <th scope="col" class="text-center">Transaction code</th>
            <th scope="col" class="text-center">Amount</th>
            <th scope="col" class="text-center">Payment gateway</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-center">Note</th>
            <th scope="col" class="text-center">Reason</th>
            <th scope="col" class="text-center">Day</th>
            <th scope="col" class="text-center">Image</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($walletTransactions as $walletTransaction)
            <tr>
                <td class="fw-bold text-center">#{{ $walletTransaction->code }}</td>
                <td class="text-center">{{ $walletTransaction->transaction_code }}</td>
                <td class="text-center">${{ formatPrice($walletTransaction->amount) }}</td>
                <td class="text-center">
                    <img width="50" height="50" src="{{ showImage($walletTransaction->configPayment->image) }}"
                        alt="">
                    {{ $walletTransaction->configPayment->title }}
                </td>
                <td class="text-center" style="width: 10%;">
                    @switch($walletTransaction->status)
                        @case('pending')
                            <div class="bg_refunded status_btn_order"><span class="px-2">pending processing</span></div>
                        @break

                        @case('complete')
                            <div class="bg_paid status_btn_order"><span class="px-2">success</span></div>
                        @break

                        @case('failure')
                            <div class=" bg_unpaid status_btn_order"><span class="px-2">failed</span></div>
                        @break
                    @endswitch
                </td>
                <td class="text-center">{{ $walletTransaction->note ?? 'N/A' }}</td>
                <td class="text-center">{{ $walletTransaction->reason ?? 'N/A' }}</td>
                <td class="text-center">{{ $walletTransaction->created_at->format('d-m-Y H:i') }}</td>
                <td class="text-center">
                    <img width="50" height="50" src="{{ showImage($walletTransaction->proof) }}" alt="">
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <div class="fw-bold text-muted">No transaction found</div>
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>

    {{ $walletTransactions->links('vendor.pagination.custom') }}
