@php
    $totalThu = 0;
    $totalChi = 0;
@endphp

@forelse ($transactions as $transaction)
    @php
        if ($transaction->type === 'income') {
            $totalThu += $transaction->amount;
        } elseif ($transaction->type === 'expense') {
            $totalChi += $transaction->amount;
        }
    @endphp

    <tr>
        <td>
            <input type="checkbox" class="form-check-input item-checkbox" data-id="{{ $transaction->id }}">
        </td>
        <td>{{ $transaction->id }} | {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
        <td>
            @if ($transaction->cashAccount)
                {{ $transaction->cashAccount->code ?? '' }}<br>
                {{ $transaction->cashAccount->name ?? '' }}
            @endif
        </td>
        <td>
            @if ($transaction->contraMoneyAccount)
                {{ $transaction->contraMoneyAccount->code ?? '' }}<br>
                {{ $transaction->contraMoneyAccount->name ?? '' }}
            @endif
        </td>
        <td>
            @if ($transaction->objectable)
                @php
                    $objectCode = $transaction->objectable->code ?? $transaction->objectable->employee_code;
                    $objectName = match ($transaction->object_type) {
                        'customer' => $transaction->objectable->name ?? '',
                        'supplier' => $transaction->objectable->name ?? '',
                        'employee' => $transaction->objectable->full_name ?? '',
                        default => '',
                    };
                @endphp
                {{ $objectCode }}<br>{{ $objectName }}
            @endif
        </td>
        <td>
            {{ $transaction->type === 'income' ? formatPrice($transaction->amount) : 0 }}
        </td>
        <td>
            {{ $transaction->type === 'expense' ? formatPrice($transaction->amount) : 0 }}
        </td>
        <td>{{ $transaction->creator->full_name ?? '' }}</td>
        <td>
            @if ($transaction->file_path)
                <a href="{{ asset('storage/' . $transaction->file_path) }}" target="_blank"
                    class="text-primary fw-bold text-decoration-none">
                    <i class="bi bi-file-earmark-text me-1"></i>
                    Xem file đính kèm
                </a>
            @endif
        </td>
        <td class="text-center position-relative">
            <button type="button" class="btn btn-sm btn-light action-toggle-btn">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="action-menu list-group position-absolute shadow-sm rounded"
                style="display: none; min-width: 150px; z-index: 1000;">
                <li class="list-group-item action-print cursor-pointer">In phiếu</li>
                <li class="list-group-item action-edit cursor-pointer"
                    data-url="/admin/bank-transactions/save/{{ $transaction->id }}">Sửa</li>
            </ul>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">Không có dữ liệu</td>
    </tr>
@endforelse

<tr>
    <td colspan="4"></td>
    <td class="text-end"><strong>Tổng</strong></td>
    <td>{{ formatPrice($totalThu) }}</td>
    <td>{{ formatPrice($totalChi) }}</td>
    <td></td>
    <td></td>
    <td></td>
</tr>
