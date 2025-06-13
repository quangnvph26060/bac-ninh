<div class="dropdown text-center position-relative" id="dropdown-wrapper-{{ $row->id }}">
    <button class="btn btn-sm btn-primary" type="button" id="dropdownMenu{{ $row->id }}" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $row->id }}">

        {{-- Xem chi tiết --}}
        <li>
            <a class="dropdown-item btn-operation-show" href="#" data-id="{{ $row->id }}">
                Xem chi tiết
            </a>
        </li>

        {{-- Sửa phiếu nhập --}}
        <li>
            <a class="dropdown-item" href="{{ route('admin.material-imports.edit', $row->id) }}">
                Chỉnh sửa
            </a>
        </li>

        {{-- Xuất PDF --}}
        <li>
            <a class="dropdown-item download-debt" href="#" data-id="{{ $row->id }}">
                Xuất PDF
            </a>
        </li>

        {{-- Xoá phiếu nhập --}}
        <li>
            <button class="dropdown-item btn-operation-destroy" data-id="{{ $row->id }}">
                Xoá
            </button>
        </li>
    </ul>
</div>
