<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <!-- Navbar toggler (Đưa sang trái) -->
        <button class="navbar-toggler" type="button" id="openMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Logo (Giữa màn hình mobile) -->
        <a class="navbar-brand" href="#">
            <img src="{{ asset('frontend/assets/img/logo_header.jpg') }}" alt="Logo" />
        </a>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav gap-3">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item" id="catalogDropdown">
                    <a class="nav-link" href="#">Catalog <i class="bi bi-chevron-down arrow-icon"></i></a>

                    <ul class="dropdown-menu full-screen-dropdown">
                        <div class="dropdown-grid px-5">
                            <!-- Cột 1: Danh mục không có con -->
                            <div class="category-column">
                                @foreach ($collections as $collection)
                                    <a class="dropdown-item fw-bold"
                                        href="{{ route('products.list', $collection->slug) }}">{{ $collection->name }}</a>
                                @endforeach
                            </div>

                            <!-- Cột 2-7: Danh mục có con -->
                            <div class="subcategory-columns">

                                @foreach ($categories as $category)
                                    <div class="subcategory-column">
                                        <a class="fw-bold"
                                            href="{{ route('products.list', $category->slug) }}">{{ $category->name }}</a>
                                        @if ($category->children->isNotEmpty())
                                            @foreach ($category->children as $child)
                                                <a class="dropdown-item"
                                                    href="{{ route('products.list', [$category->slug, $child->slug]) }}">{{ $child->name }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Help Center</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </li>
            </ul>
        </div>

        {{-- <a href="#" class="position-relative ms-3 cart-icon">
            <i class="bi bi-cart3 fs-4"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ Cart::instance('shopping')->count() }}
            </span>
        </a> --}}

        <!-- Login Button (Chữ login sẽ đổi thành icon trên mobile) -->
        <a href="#" class="login-btn ms-3">
            <span>Login</span>
            <i class="bi bi-person"></i>
        </a>
    </div>
</nav>
