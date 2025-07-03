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
        <td>{{ $transaction->id }} | {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
        <td>{{ $transaction->voucherType->name ?? '' }}</td>
        <td>{{ ($transaction->cashAccount->code ?? '') . ' - ' . ($transaction->cashAccount->name ?? '') }}</td>

        <td>
            {{ $transaction->type === 'income' ? formatPrice($transaction->amount) : 0 }}
        </td>

        <td>
            {{ $transaction->type === 'expense' ? formatPrice($transaction->amount) : 0 }}
        </td>

        <td>{{ $transaction->creator->full_name ?? '' }}</td>

        <td>
            @if ($transaction->attachment)
                <a href="{{ asset('storage/' . $transaction->attachment) }}" target="_blank"
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
                    data-url="/admin/cashbook/save/{{ $transaction->id }}">Sửa</li>
            </ul>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center">Không có dữ liệu</td>
    </tr>
@endforelse

<tr>
    <td colspan="3"></td>
    <td class="text-end"><strong>Tổng</strong></td>
    <td>{{ formatPrice($totalThu) }} USD</td>
    <td>{{ formatPrice($totalChi) }} USD</td>
    <td></td>
    <td></td>
    <td></td>
</tr>
