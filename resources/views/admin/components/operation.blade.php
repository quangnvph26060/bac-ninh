<a target="_blank" href="{{ route('admin.' . pluralModelName($row) . '.edit', $row->id) }}"
    class="btn btn-primary btn-sm table-actions btn-operation-edit">
    <i class="ti ti-edit"></i>
</a>

@if (!empty($show))
    <a href="" class="btn btn-primary btn-sm">
        <i class="ti ti-eye"></i>
    </a>
@endif

<a href="javascript:void(0)" data-id="{{ $row->id }}"
    class="btn btn-danger btn-sm table-actions btn-operation-destroy">
    <i class="ti ti-trash"></i>
</a>
