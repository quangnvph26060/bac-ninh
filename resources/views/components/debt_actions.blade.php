<div class="dropdown text-center">
    <button class="btn btn-sm btn-primary" type="button" id="dropdownMenu{{ $row->id }}" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $row->id }}">
        <li>
            <a class="dropdown-item show-modal" href="#" data-id="{{ $row->id }}">
                Xem chi tiết
            </a>
        </li>
        <li>
            <a class="dropdown-item make-payment" href="#" data-id="{{ $row->id }}">
                Thanh toán ngay
            </a>
        </li>
        <li>
            <a class="dropdown-item print-debt" href="#" data-id="{{ $row->id }}">
                In phiếu công nợ
            </a>
        </li>
        <li>
            <a href="#" class="dropdown-item download-debt" data-id="{{ $row->id }}">Tải file PDF</a>
        </li>
    </ul>
</div>
