<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('admin') }}" class="mb-0 d-inline-block lh-1 text-uppercase">
                trang chủ
            </a>
        </li>
        @foreach ($items as $item)
            <li class="breadcrumb-item active text-uppercase" aria-current="page">
                @isset($item['url'])
                    <a href="{{ $item['url'] }}" class="mb-0 d-inline-block lh-1 text-uppercase">
                        {{ $item['name'] }}
                    </a>
                @else
                    {{ $item['name'] }}
                @endisset
            </li>
        @endforeach
    </ol>
</nav>
