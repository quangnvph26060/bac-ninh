<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" href="{{ showImage($config->favicon) }}" />
    <link rel="icon" href="{{ showImage($config->favicon) }}" type="image/x-icon" />

    <title>document</title>

    @include('admin.layout.includes.styles')
</head>


<body>
    <div class="wrapper">
        @include('admin.layout.sidebar')

        <div class="main-panel">

            @include('admin.layout.header');

            <div class="container">
                @yield('content')
            </div>


            @include('admin.layout.footer')

        </div>

    </div>

    <div id="loadingSpinner" style="display: none;" class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    @include('admin.layout.includes.script')

</body>

</html>
