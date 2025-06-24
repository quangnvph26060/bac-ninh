<a href="{{ route('admin.' . pluralModelName($row) . '.edit', $row->id) }}"
    class="btn btn-primary btn-sm table-actions btn-operation-edit">
    <i class="ti ti-edit"></i>
</a>

@if (class_basename($row) === 'Order')
    <button type="button" data-code="{{ $row->order_code }}"
        class="btn btn-primary btn-sm table-actions btn-operation-show">
        <i class="ti ti-eye"></i>
    </button>
@endif

{{-- @can('delete-model', class_basename($row)) --}}
@if (class_basename($row) !== 'Order')
    <a href="javascript:void(0)" data-id="{{ $row->id }}"
        class="btn btn-danger btn-sm table-actions btn-operation-destroy">
        <i class="ti ti-trash"></i>
    </a>
@endif
{{-- @endcan --}}
