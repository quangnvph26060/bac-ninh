<div class="overlay" id="overlay"></div>

<div class="sidebar-menu" id="sidebarMenu">
    <div class="close-container">
        <button class="close-btn" id="closeMenu">&times;</button>
    </div>

    <!-- Menu chính -->
    <ul class="menu active" data-level="0">
        <li><a href="{{ route('home') }}" class="fw-bold">Home</a></li>
        <li class="has-children">
            <a href="#">Catalogue</a>
            <i class="bi bi-chevron-right arrow-icon"></i>
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

    <!-- Menu Catalogue -->
    <ul class="menu" data-level="1">
        <li class="menu-header">
            <i class="bi bi-chevron-left arrow-icon back-btn"></i>
            <span class="menu-title">Catalogue</span>
        </li>

        {{-- Các danh mục không có con (collections) --}}
        @foreach ($collections as $collection)
            <li>
                <a href="{{ route('products.collection', $collection->slug) }}">{{ $collection->name }}</a>
            </li>
        @endforeach

        {{-- Các danh mục có con (categories) --}}
        @foreach ($categories as $category)
            <li class="has-children">
                <a href="{{ route('products.category', $category->slug) }}">{{ $category->name }}</a>
                <i class="bi bi-chevron-right arrow-icon"></i>
            </li>
        @endforeach
    </ul>

    {{-- Menu con cho từng category có children --}}
    @foreach ($categories as $category)
        @if ($category->children->isNotEmpty())
            <ul class="menu" data-level="2">
                <li class="menu-header">
                    <i class="bi bi-chevron-left arrow-icon back-btn"></i>
                    <span class="menu-title">{{ $category->name }}</span>
                </li>
                @foreach ($category->children as $child)
                    <li>
                        <a href="{{ route('products.category', [$category->slug, $child->slug]) }}">{{ $child->name }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
    @endforeach
</div>
