<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Hệ thống quản lý phần mềm AICRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="asset/images/favicon.ico">
    <!-- Layout config Js -->
    <script src="{{ asset('backend/auth/assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/auth/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('backend/auth/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('backend/auth/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('backend/auth/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>
    <div
        class=" content auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('backend/auth/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/auth/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/auth/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/auth/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('backend/auth/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('backend/auth/assets/js/plugins.js') }}"></script>

    @stack('scripts')
</body>

</html>
