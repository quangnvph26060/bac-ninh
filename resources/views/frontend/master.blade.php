<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $config['title'] }}</title>

    <meta name="description" content="{{ $config['seo_description'] }}" />
    <meta name="author" content="{{ $config['company'] }}" />

    <!-- Open Graph Meta Tags (Facebook, LinkedIn, etc.) -->
    <meta property="og:title" content="{{ $config['seo_title'] }}" />
    <meta property="og:description" content="{{ $config['seo_description'] }}" />
    <meta property="og:image" content="" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <link rel="apple-touch-icon" href="{{ showImage($config['favicon']) }}" />
    <link rel="icon" href="{{ showImage($config['favicon']) }}" type="image/x-icon" />
    <meta property="fb:app_id" content="1234567890" />

    <!-- Google Font -->
    @include('frontend.includes.styles')
</head>

<body>
    <header>
        @include('frontend.includes.nav')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('frontend.includes.footer')
    </footer>

    @include('frontend.includes.sidebar')

    @include('frontend.includes.chat')

    @include('frontend.includes.script')

</body>

</html>
