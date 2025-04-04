<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>document </title>

    @include('admin.layout.includes.styles')
</head>


<body>
    <div id="wrapper">
        @include('admin.layout.sidebar')

        <div class="main-panel">

            @include('admin.layout.header');

            <div class="container">
                @yield('content')
            </div>


            @include('admin.layout.footer')

        </div>

    </div>

    @include('admin.layout.includes.script')



</body>

</html>
