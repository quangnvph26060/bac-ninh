<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $config->title }}</title>

    <meta name="description" content="{{ $config->seo_description }}" />
    <meta name="author" content="{{ $config->company }}" />

    <!-- Open Graph Meta Tags (Facebook, LinkedIn, etc.) -->
    <meta property="og:title" content="{{ $config->seo_title }}" />
    <meta property="og:description" content="{{ $config->seo_description }}" />
    <meta property="og:image" content="" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    <link rel="apple-touch-icon" href="{{ showImage($config->favicon) }}" />
    <link rel="icon" href="{{ showImage($config->favicon) }}" type="image/x-icon" />
    <meta property="fb:app_id" content="1234567890" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" href="{{ asset('frontend/assets/css/app.css') }}" />

    @stack('styles')
</head>

<body>

    <header class="header">
        <div class="hamburger-menu d-none">
            <button class="d-flex" id="hamburgerBtn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_6356_153471)">
                        <path d="M0.833328 10H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10"
                            stroke-linecap="square"></path>
                        <path d="M0.833328 4.16699H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10"
                            stroke-linecap="square"></path>
                        <path d="M0.833328 15.834H19.1667" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10"
                            stroke-linecap="square"></path>
                    </g>
                    <defs>
                        <clipPath id="clip0_6356_153471">
                            <rect width="20" height="20" fill="white"></rect>
                        </clipPath>
                    </defs>
                </svg>
            </button>
        </div>
        <div class="logo-header">
            <a href="">
                <img src="{{ showImage($config->logo) }}" alt="" />
            </a>
        </div>
        <div class="content_header">
            <div class="d-flex h-100 w-100 justify-content-between pe-3 align-content-center">
                <div class="promotion__banner"></div>

                <div class="d-flex h-100 justify-content-end row__info__user">
                    <div class="box__money">
                        <div class="balance__box pe-2">
                            <div class="logo__balance">
                                <img src="{{ asset('frontend/assets/img/balance.png') }}" alt="balance" />
                            </div>
                            <div class="amount__balance">
                                <p class="balance_text">Số dư</p>
                                <p class="money__amount">${{ formatPrice($wallet->balance) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="box__money">
                        <div class="balance__box pe-2">
                            <div class="logo__balance">
                                <img src="{{ asset('frontend/assets/img/not-yet-paid.png') }}" alt="balance" />
                            </div>
                            <div class="amount__balance">
                                <p class="balance_text">Chưa trả</p>
                                <p class="money__amount money_unpaid">$0.00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu tượng thông báo -->
                    <div class="d-flex align-content-center justify-content-around">
                        <div class="nofitication">
                            <button id="notificationBtn">
                                <span class="badge">
                                    <svg width="22" height="24" viewBox="0 0 22 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18 13V8C18 6.14348 17.2625 4.36301 15.9497 3.05025C14.637 1.7375 12.8565 1 11 1C9.14348 1 7.36301 1.7375 6.05025 3.05025C4.7375 4.36301 4 6.14348 4 8V13C3.97026 15.47 3.27968 17.8871 2 20H20C18.7203 17.8871 18.0297 15.47 18 13Z"
                                            stroke="white" stroke-width="2" stroke-miterlimit="10"
                                            stroke-linecap="square"></path>
                                        <path
                                            d="M8.17383 22C8.38162 22.5832 8.76478 23.0879 9.27074 23.4447C9.7767 23.8016 10.3807 23.9932 10.9998 23.9932C11.619 23.9932 12.223 23.8016 12.7289 23.4447C13.2349 23.0879 13.618 22.5832 13.8258 22H8.17383Z"
                                            fill="white"></path>
                                    </svg>

                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Popup thông báo -->
                    <div class="notification_popup" id="notificationPopup">
                        <div class="popup_header">
                            <strong>Thông báo</strong>
                            <a href="#" class="read_all">Đọc hết</a>
                        </div>
                        <div class="popup_content">
                            <div class="notification_item">
                                <span class="icon">🎁</span>
                                <div class="text">
                                    <strong>Chào mừng bạn đến với Printway Fulfillment App!</strong>
                                    <p>Chào mừng bạn đến với Printway Fulfillment App!</p>
                                    <span class="time">a day ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="avatar__info__wrapper">
                        <div class="avatar__info cursor-pointer">
                            <div class="avata_image">
                                <img src="{{ showImage(Auth::user()->img_url) }}" alt="" class="avatar" />
                            </div>

                            <!-- User Info -->
                            <div class="user__info__content d-none d-lg-flex">
                                <div class="user__info_name">
                                    <span class="name__user truncate">
                                        <span class="info_name">{{ Auth::user()->name }}</span>
                                        <span class="dropdown_menu">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16 10L12 14L8 10" stroke="#8F9BB3" stroke-width="2"
                                                    stroke-miterlimit="10" stroke-linecap="square"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- Dropdown Menu -->
                            <div class="dropdown_popup">
                                <ul>
                                    <li><a href="/"><i class="bi bi-house-down me-2"></i> Trở về trang chủ</a>
                                    </li>
                                    <li><a href="{{ route('profile') }}"><i class="bi bi-person-fill-check me-2"></i>
                                            Tài khoản của tôi</a></li>
                                    <li><a href="{{ route('logout') }}"><i class="bi bi-box-arrow-left me-2"></i>
                                            Đăng
                                            xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="layout layout-has-sider layout-content" id="main-section">
        <aside class="layout-sider layout-sider-light layout-sider-has-trigger leftSider">
            <div class="drawer-header d-flex d-md-none">
                <div class="drawer-title">
                    <img src="https://fulfill-s3-dev.s3.ap-southeast-1.amazonaws.com/demo-image/le.nt.bn1102_logo-1675313469524-cc535680-783a-4999-bb03-00427daecece.png"
                        alt="logo-printway" />
                </div>
                <button class="close-menu" id="closeMenuBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 6L18 18M6 18L18 6" stroke="#4a2c0f" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div class="layout-sider-children">
                <div class="iconCollapsed">
                    <button class="btn btn-circle btn-unset btn-lg btn-icon-only">
                        <svg class="leftIconColapseMenu" width="32px" height="32px" viewBox="0 0 24 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 16.34 L11.42 11.75 L16 7.16 L14.59 10.42" stroke="#8F9BB3" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="menu_side_bar">
                    <div class="menu_main">
                        <ul class="nav flex-column">
                            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Bảng tin</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                                <a href="{{ route('orders.index') }}" class="nav-link">
                                    <i class="bi bi-bag"></i>
                                    <span>Đơn hàng</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('bills.index') ? 'active' : '' }}">
                                <a href="{{ route('bills.index') }}" class="nav-link">
                                    <i class="bi bi-receipt-cutoff"></i>
                                    <span>Hóa đơn</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('coupons.index') ? 'active' : '' }}">
                                <a href="{{ route('coupons.index') }}" class="nav-link ">
                                    <i class="bi-tags"></i>
                                    <span>Giảm giá</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                                <a href="{{ route('profile') }}" class="nav-link">
                                    <i class="bi bi-person-bounding-box"></i>
                                    <span>Hồ sơ</span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('transaction.history') ? 'active' : '' }}">
                                <a href="{{ route('transaction.history') }}" class="nav-link">
                                    <i class="bi bi-wallet2"></i>
                                    <span>Lịch sử nạp tiền</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="menu_fixed">
                        <ul class="nav flex-column">
                            <li class="nav-item active">
                                <a href="{{ url('/') }}" class="nav-link">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Trang chủ</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
        <main>
            @yield('content')
        </main>
    </section>

    <div class="overlay" id="overlay"></div>
    <div id="loading" style="display: none; text-align: center; padding: 50px;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script src="{{ asset('frontend/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <script src="{{ asset('frontend/assets/js/app.js') }}"></script>

    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const notyf = new Notyf({
            duration: 5000,
            ripple: true,
        });
    </script>
    @stack('scripts')
</body>

</html>
