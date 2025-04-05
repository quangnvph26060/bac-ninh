<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navbar with Header</title>

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

    @include('frontend.includes.script')

</body>

</html>
