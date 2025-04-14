<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
        @foreach ($items as $item)
            <li class="breadcrumb-item">
                @isset($item['url'])
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    {{ $item['label'] }}
                @endisset
            </li>
        @endforeach
    </ol>
</nav>

