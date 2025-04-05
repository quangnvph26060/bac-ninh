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
                    <a class="nav-link active" href="#">Home</a>
                </li>
                <li class="nav-item" id="catalogDropdown">
                    <a class="nav-link" href="#">Catalog <i class="bi bi-chevron-down arrow-icon"></i></a>

                    <ul class="dropdown-menu full-screen-dropdown">
                        <div class="dropdown-grid px-5">
                            <!-- Cột 1: Danh mục không có con -->
                            <div class="category-column">
                                <a class="dropdown-item fw-bold" href="#">New Arrivals</a>
                                <a class="dropdown-item fw-bold" href="#">Easter</a>
                                <a class="dropdown-item fw-bold" href="#">Mother's Day</a>
                                <a class="dropdown-item fw-bold" href="#">Valentine's Day</a>
                                <a class="dropdown-item fw-bold" href="#">Christmas Collection</a>
                            </div>

                            <!-- Cột 2-7: Danh mục có con -->
                            <div class="subcategory-columns">
                                <div class="subcategory-column">
                                    <a class="fw-bold">Home and Living</a>
                                    <a class="dropdown-item" href="#">Cards</a>
                                    <a class="dropdown-item" href="#">Ornaments</a>
                                    <a class="dropdown-item" href="#">Signs</a>
                                    <a class="dropdown-item" href="#">Lights</a>
                                    <a class="dropdown-item" href="#">Posters</a>
                                    <a class="dropdown-item" href="#">Canvas</a>
                                    <a class="dropdown-item" href="#">Mugs</a>
                                    <a class="dropdown-item" href="#">Tumblers</a>
                                    <a class="dropdown-item" href="#">Water Bottles</a>
                                    <a class="dropdown-item" href="#">Blankets</a>
                                    <a class="dropdown-item" href="#">Puzzles</a>
                                    <a class="dropdown-item" href="#">Beddings</a>
                                    <a class="dropdown-item" href="#">Rugs</a>
                                    <a class="dropdown-item" href="#">Doormats</a>
                                    <a class="dropdown-item" href="#">Flags</a>
                                    <a class="dropdown-item" href="#">Home and Kitchen</a>
                                    <a class="dropdown-item" href="#">Pillows & Cases</a>
                                    <a class="dropdown-item" href="#">Stationery</a>
                                    <a class="dropdown-item" href="#">Home Decor</a>
                                </div>
                                <div class="subcategory-column">
                                    <a class="fw-bold">Accessories</a>
                                    <a class="dropdown-item" href="#">Phone Cases</a>
                                    <a class="dropdown-item" href="#">Keychains</a>
                                    <a class="dropdown-item" href="#">Bookmarks</a>
                                    <a class="dropdown-item" href="#">Pets </a>
                                    <a class="dropdown-item" href="#">Tech accessories</a>
                                    <a class="dropdown-item" href="#">Car accessories</a>
                                    <a class="dropdown-item" href="#">Face masks</a>
                                    <a class="dropdown-item" href="#">Beauty Accessories</a>
                                    <a class="dropdown-item" href="#">Packaging</a>
                                    <a class="dropdown-item" href="#">Bags</a>
                                    <a class="dropdown-item" href="#">Hats</a>
                                    <a class="dropdown-item" href="#">Shoes</a>
                                    <a class="dropdown-item" href="#">Sports & games</a>
                                </div>
                                <div class="subcategory-column">
                                    <a class="fw-bold">Men's Clothing</a>
                                    <a class="dropdown-item" href="#">T-shirts</a>
                                    <a class="dropdown-item" href="#">Sweatshirts</a>
                                    <a class="dropdown-item" href="#">Hoodies</a>
                                    <a class="dropdown-item" href="#">Tank-tops</a>
                                    <a class="dropdown-item" href="#">Polo Shirts</a>
                                    <a class="dropdown-item" href="#">Jackets & Coats</a>
                                    <a class="dropdown-item" href="#">Bottoms</a>
                                    <a class="dropdown-item" href="#">Long Sleeve T-shirts</a>
                                </div>
                                <div class="subcategory-column">
                                    <a class="fw-bold">Women's Clothing</a>
                                    <a class="dropdown-item" href="#">Dresses</a>
                                    <a class="dropdown-item" href="#">Women's Tank-tops</a>
                                    <a class="dropdown-item" href="#">Women's Sweatshirts</a>
                                    <a class="dropdown-item" href="#">Women's Hoodies</a>
                                    <a class="dropdown-item" href="#">Women's Jackets & Coats</a>
                                    <a class="dropdown-item" href="#">Women's Bottoms</a>
                                    <a class="dropdown-item" href="#">Women's Long Sleeve T-shirts</a>
                                    <a class="dropdown-item" href="#">Women's T-shirt</a>
                                </div>
                                <div class="subcategory-column">
                                    <a class="fw-bold">Kid's Clothing</a>
                                    <a class="dropdown-item" href="#">Baby bodysuits</a>
                                    <a class="dropdown-item" href="#">Kid's T-shirts</a>
                                    <a class="dropdown-item" href="#">Kid's Hoodies</a>
                                    <a class="dropdown-item" href="#">Kid's Jackets & Coats</a>
                                    <a class="dropdown-item" href="#">Kid's Sweatshirts</a>
                                </div>
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

        <!-- Login Button (Chữ login sẽ đổi thành icon trên mobile) -->
        <a href="#" class="login-btn ms-3">
            <span>Login</span>
            <i class="bi bi-person"></i>
        </a>
    </div>
</nav>
