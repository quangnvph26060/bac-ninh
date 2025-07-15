@foreach ($journalEntries as $item)
    <tr>
        <td> <input type="checkbox" id="checked-all" class="form-check-input"></td>
        <td>{{ $item->id }} | {{ $item->created_at->format('d/m/Y') }}</td>
        <td>{{ $item->type }}</td>
        <td>{{ $item->object_type }}</td>
        <td>{{ $item->document }}</td>
        <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
        <td>{{ $item->debit_account }}</td>
        <td>{{ $item->credit_account }}</td>
        <td>{{ $item->note }}</td>
        <td>
            <a href="{{ $item->file }}" target="_blank">Xem file</a>
        </td>
        <td class="text-end position-relative">
            <div class="dropdown">
                <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item" href=""
                            target="_blank">
                            <i class="fas fa-print me-2"></i> In phiếu
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="fas fa-pen me-2"></i> Sửa
                        </a>
                    </li>
                    <li>
                        <form action="" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-trash-alt me-2"></i> Xoá
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>

    </tr>
@endforeach
