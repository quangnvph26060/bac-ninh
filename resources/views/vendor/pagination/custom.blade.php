@php
    $total = $paginator->lastPage();
    $current = $paginator->currentPage();
    $start = 1;
    $end = $total;

    if ($total <= 7) {
        $start = 1;
        $end = $total;
    } elseif ($current <= 3) {
        $start = 1;
        $end = 5;
    } elseif ($current >= $total - 2) {
        $start = $total - 4;
        $end = $total;
    } else {
        $start = $current - 2;
        $end = $current + 2;
    }
@endphp

<div class="pagination">
    <div class="d-flex justify-content-center align-items-center">
        <ul class="custom-pagination">

            {{-- Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><i class="bi bi-chevron-left"></i></li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                </li>
            @endif

            {{-- First page and leading dots --}}
            @if ($start > 1)
                <li class="page-item"><a class="page-url-link" href="{{ $paginator->url(1) }}">1</a></li>
                @if ($start > 2)
                    <li class="page-item dots">...</li>
                @endif
            @endif

            {{-- Main page range --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <li class="page-item active page-url-link">{{ $i }}</li>
                @else
                    <li class="page-item"><a class="page-url-link"
                            href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endif
            @endfor

            {{-- Trailing dots and last page --}}
            @if ($end < $total)
                @if ($end < $total - 1)
                    <li class="page-item dots">...</li>
                @endif
                <li class="page-item"><a class="page-url-link"
                        href="{{ $paginator->url($total) }}">{{ $total }}</a></li>
            @endif

            {{-- Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                </li>
            @else
                <li class="page-item disabled"><i class="bi bi-chevron-right"></i></li>
            @endif

        </ul>

        {{-- Per Page Dropdown (luôn hiện, nhưng disable nếu tổng số bản ghi < 10) --}}
        <form id="per-page-form">
            <select name="per_page" class="per-page-selector ms-3" {{ $paginator->total() < 10 ? 'disabled' : '' }}>
                @foreach ([10, 20, 50, 100] as $limit)
                    <option value="{{ $limit }}" {{ request('per_page', 10) == $limit ? 'selected' : '' }}>
                        {{ $limit }} / page
                    </option>
                @endforeach
            </select>
        </form>

    </div>
</div>
