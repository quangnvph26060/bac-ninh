@foreach ($journalEntries as $item)
    <tr>
        <td>
            <input type="checkbox" class="form-check-input item-checkbox" data-id="{{ $item->id }}">
        </td>
        <td>{{ $item->id }} | {{ $item->transaction_date->format('d/m/Y') }}</td>
        <td>{{ $item->typeLabel }}</td>
        <td>{{ $item->object->code }} <br> {{ $item->object->name ?? $item->object->company_name }}</td>
        <td>{{ $item->document }}</td>
        <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
        <td>{{ $item->debit_account }}</td>
        <td>{{ $item->credit_account }}</td>
        <td>{{ $item->note }}</td>
        <td>
            @if ($item->file)
                <a href="{{ $item->file }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-alt me-1"></i> Xem file
                </a>
            @endif
        </td>

        <td class="text-end position-relative">
            <div class="dropdown">
                <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item" href="" target="_blank">
                            <i class="fas fa-print me-2"></i> In phiếu
                        </a>
                    </li>
                    {{-- <li>
                        <a class="dropdown-item" href="">
                            <i class="fas fa-pen me-2"></i> Sửa
                        </a>
                    </li> --}}
                    <li>
                        <button type="button" class="dropdown-item text-danger action-delete"
                            data-id="{{ $item->id }}">
                            <i class="fas fa-trash-alt me-2"></i> Xoá
                        </button>
                    </li>
                </ul>
            </div>
        </td>

    </tr>
@endforeach
