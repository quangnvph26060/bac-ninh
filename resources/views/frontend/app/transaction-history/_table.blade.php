<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" style="width: 5%">ID</th>
            <th scope="col" style="width: 10%">Mã giao dịch</th>
            <th scope="col">Số tiền giao dịch</th>
            <th scope="col"> Số dư trước</th>
            <th scope="col">Số dư sau</th>
            <th scope="col">Ghi chú</th>
            <th scope="col">Ngày giao dịch</th>
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
                <td class="{{ $class }}">{{ $walletTransaction->id }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->code }}</td>
                <td class="{{ $class }}">
                    {{ $walletTransaction->type === 'deposit' ? '+' : '-' }}
                    ${{ formatPrice($walletTransaction->amount) }}
                </td>
                <td class="{{ $class }}">${{ formatPrice($walletTransaction->balance_before) }}</td>
                <td class="{{ $class }}">${{ formatPrice($walletTransaction->balance_after) }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->note }}</td>
                <td class="{{ $class }}">{{ $walletTransaction->created_at->format('d-m-Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="fw-bold text-muted">Không có giao dịch nào được tìm thấy</div>
                </td>
            </tr>
        @endforelse

    </tbody>

</table>

{{ $walletTransactions->links('vendor.pagination.custom') }}
