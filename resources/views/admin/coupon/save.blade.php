@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $breadcrumbItems = [
                    ['name' => 'mã giảm giá', 'url' => route('admin.coupons.index')],
                    ['name' => $coupon ? "$title - {$coupon->name}" : $title],
                ];
            @endphp

            <div class="page-header">
                <x-breadcrumb :items="$breadcrumbItems" />
            </div>
        </div>

        <form id="myForm">

            @if ($coupon)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">

                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-9">
                                        <label for="code" class="form-label required">CODE</label>
                                        <input type="text" placeholder="Code" aria-required="true" required
                                            class="form-control" name="code" id="code"
                                            value="{{ optional($coupon)->code }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-3">
                                        <label for="type" class="form-label required">Loại</label>
                                        <select name="type" id="type" class="form-select">
                                            <option value="order" @selected(($coupon->type ?? '') == 'order')>Giảm theo đơn hàng</option>
                                            <option value="product" @selected(($coupon->type ?? '') == 'product')>Giảm theo sản phẩm</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="value" class="form-label required">Giá trị giảm
                                            <code>(%)</code></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="value"
                                                placeholder="Nhập giá trị giảm"
                                                value="{{ formatPrice(optional($coupon)->value) }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="max_discount" class="form-label">Giá trị giảm tối đa
                                            <code>($)</code></label>
                                        <input type="text" placeholder="Giá trị giảm tối đa" class="form-control"
                                            name="max_discount" id="max_discount"
                                            value="{{ formatPrice(optional($coupon)->max_discount) }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu
                                            <code>($)</code></label>
                                        <input type="text" placeholder="Giá trị đơn hàng tối thiểu" class="form-control"
                                            name="min_order_value" id="min_order_value"
                                            value="{{ formatPrice(optional($coupon)->min_order_value) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" for="">
                                                Bắt đầu
                                            </label>
                                            <input class="form-control form-date-time" type="text" name="start_date"
                                                id="start_date" placeholder="d-m-Y H:i"
                                                value="{{ $coupon && $coupon->start_date ? $coupon->start_date->format('d-m-Y H:i') : '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" for="">
                                                Kết thúc
                                            </label>
                                            <input class="form-control form-date-time" type="text" name="end_date"
                                                id="end_date" placeholder="d-m-Y H:i"
                                                value="{{ $coupon && $coupon->end_date ? $coupon->end_date->format('d-m-Y H:i') : '' }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="usage_limit" class="form-label">Số lượt sử dụng</label>
                                        <input type="number" placeholder="Số lượt sử dụng" class="form-control"
                                            name="usage_limit" id="usage_limit"
                                            value="{{ optional($coupon)->usage_limit }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="usage_per_user" class="form-label">Lượt dùng mỗi người</label>
                                        <input type="number" placeholder="Lượt dùng mỗi người" class="form-control"
                                            name="usage_per_user" id="usage_per_user"
                                            value="{{ optional($coupon)->usage_per_user }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" id="description" placeholder="Mô tả">{!! optional($coupon)->description !!}</textarea>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card" style="display: none" id="search-product">
                        <input name="product_id[]" type="hidden"
                            value="{{ !empty($coupon) && $coupon->products ? $coupon->products->pluck('id')->implode(',') : '' }}">

                        <div class="card-body">
                            <div class="mb-3 mt-3 position-relative">
                                <input class="form-control" type="text" name="search_input" id="searchInput"
                                    placeholder="Tìm kiếm sản phẩm">
                                <div class="card position-absolute z-1 shadow w-100 active" style="display:none"
                                    id="popup-dropdown">
                                    <div class="card-body p-0">
                                        <div class="list-search-data">
                                            <div class="list-group list-group-flush overflow-y-auto overflow-x-hidden"
                                                style="max-height: 25rem;">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer pb-0 d-flex justify-content-end">
                                        <nav>
                                            <ul class="pagination">
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group list-group-flush list-group-hoverable list-selected-products"
                                style="@if (isset($coupon) && $coupon->products->isNotEmpty()) display:block; @else display:none @endif">
                                <label class="form-label">Sản phẩm đã chọn</label>

                                @foreach ($coupon->products ?? [] as $product)
                                    <div class="list-group-item" data-id="{{ $product->id }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar"
                                                    style="background-image: url('{{ showImage($product->image) }}')"></span>
                                            </div>
                                            <div class="col text-truncate">
                                                <a href="javascript:void(0);"
                                                    class="text-body d-block text-truncate fs-6">{{ $product->name }}</a>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0)" data-bb-toggle="product-delete-item"
                                                    data-bb-target="1"
                                                    class="text-decoration-none list-group-item-actions btn-trigger-remove-selected-product"
                                                    title="Xóa bỏ">
                                                    <svg class="icon text-secondary svg-icon-ti-ti-x"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M18 6l-12 12"></path>
                                                        <path d="M6 6l12 12"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.coupons.index')])

                    <x-status :status="optional($coupon)->status" />
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        let ids = @json(isset($coupon) ? $coupon->products->pluck('id')->toArray() : []);

        let debounceTimer;

        function loadPage(page) {
            let query = $('#searchInput').val();
            fetchSearchResults(query, page);
        };

        function fetchSearchResults(query, page = 1) {
            $.ajax({
                url: '{{ route('admin.products.search.products') }}', // Địa chỉ API của bạn
                method: 'GET',
                data: {
                    query: query,
                    page: page,
                    per_page: 10 // Giới hạn 10 sản phẩm mỗi trang
                },
                success: function(response) {
                    displaySearchResults(response.data, response.pagination);
                },
                error: function() {
                    console.log("Lỗi khi gọi API tìm kiếm.");
                }
            });
        }

        function displaySearchResults(products, pagination) {
            let resultList = $('.list-search-data .list-group');
            resultList.empty(); // Xóa nội dung cũ

            // Hiển thị sản phẩm tìm thấy
            products.forEach(function(product) {
                let path = "{{ config('app.url') }}/storage/" + product.image;

                resultList.append(`
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action selectable-item"
                            data-id="${product.id}" data-name="${product.name}" data-image="${path}" data-price="${product.price}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar" style="background-image: url('${path}')"></span>
                                </div>
                                <div class="col text-truncate">
                                    <h4 class="text-body d-block mb-0">${product.name}</h4>
                                </div>
                            </div>
                        </a>
                    `);
            });

            // Hiển thị phân trang
            let paginationList = $('.pagination');
            paginationList.empty();

            if (pagination.prev_page_url) {
                paginationList.append(
                    `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage(${pagination.current_page - 1})">Trước</a></li>`
                );
            } else {
                paginationList.append(
                    '<li class="page-item disabled"><span class="page-link">Trước</span></li>');
            }

            if (pagination.next_page_url) {
                paginationList.append(
                    `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage(${pagination.current_page + 1})">Kế tiếp</a></li>`
                );
            } else {
                paginationList.append(
                    '<li class="page-item disabled"><span class="page-link">Kế tiếp</span></li>');
            }

        }

        function checkListSelected() {
            if ($('.list-selected-products .list-group-item').length > 0) {
                $('.list-selected-products').css('display', 'block');
            } else {
                $('.list-selected-products').css('display', 'none');
            }
        }

        $(document).ready(function() {

            let dropdownHeight = 0;

            $('#type').on('change', function() {
                let value = $(this).val();

                if (value == 'product') {
                    $('#search-product').show();
                } else {
                    $('#search-product').hide();
                }
            }).trigger('change');


            $('#searchInput').on('focus', function() {
                let input = $(this);
                let popup = $('#popup-dropdown');

                // Hiển thị popup tạm thời để đo kích thước
                popup.show();

                let inputOffset = input.offset();
                let inputHeight = input.outerHeight();
                let popupHeight = popup.outerHeight();
                let windowHeight = $(window).height();
                let spaceBelow = windowHeight - (inputOffset.top + inputHeight);
                let spaceAbove = inputOffset.top;

                // Xác định nên hiển thị bên dưới hay bên trên
                if (spaceBelow < popupHeight && spaceAbove > popupHeight) {
                    popup.css({
                        top: 'auto',
                        bottom: inputHeight + 'px'
                    });
                } else {
                    popup.css({
                        top: inputHeight + 'px',
                        bottom: 'auto'
                    });
                }

                if (!$('.selectable-item').length > 0) {
                    fetchSearchResults('');
                }
            });

            $(document).on('click', '.btn-trigger-remove-selected-product', function() {
                let item = $(this).closest('.list-group-item');
                let productId = item.data('id');
                item.remove();

                ids = ids.filter(id => id !== productId);

                // Cập nhật lại input ẩn
                $('input[name="product_id[]"]').val(ids.join(','));

                checkListSelected()
            });

            $(document).on('click', '.selectable-item', function() {
                let productId = $(this).data('id');
                let productName = $(this).data('name');
                let productImage = $(this).data('image');
                let productPrice = $(this).data('price');

                if (ids.includes(productId)) {
                    // Nếu sản phẩm đã tồn tại, xóa nó khỏi danh sách
                    $('.list-selected-products .list-group-item[data-id="' + productId + '"]').remove();

                    // Loại bỏ productId khỏi mảng ids
                    ids = ids.filter(id => id !== productId);

                } else {
                    ids.push(productId);
                    // Cập nhật danh sách sản phẩm đã chọn
                    $('.list-selected-products').append(`
                    <div class="list-group-item" data-id="${productId}">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-image: url('${productImage}')"></span>
                            </div>
                            <div class="col text-truncate">
                                <a href="javascript:void(0);" class="text-body d-block text-truncate fs-6">${productName}</a>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0)" data-bb-toggle="product-delete-item" data-bb-target="1" class="text-decoration-none list-group-item-actions btn-trigger-remove-selected-product" title="Xóa bỏ">
                                    <svg class="icon text-secondary svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M18 6l-12 12"></path>
                                        <path d="M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                `);
                }

                checkListSelected()

                $('input[name="product_id[]"]').val(ids)

            });

            $('#searchInput').on('input', function() {
                let query = $(this).val();

                clearTimeout(debounceTimer);

                debounceTimer = setTimeout(function() {
                    fetchSearchResults(query);
                }, 500);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#searchInput').length && !$(e.target).closest('#popup-dropdown')
                    .length) {
                    $('#popup-dropdown').hide();
                    $('#search-product').css('margin-bottom', '0');
                }
            });

            flatpickr(".form-date-time", {
                enableTime: true, // Bật chọn giờ
                dateFormat: "d-m-Y H:i", // Định dạng ngày + giờ
                time_24hr: true, // Hiển thị giờ theo định dạng 24h
                locale: "vn" // Ngôn ngữ tiếng Việt
            });

            updateCharCount('#code', 250)

            convertToAsciiUpper('#code')

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.coupons.index') }}"
            })
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">

    <style>
        .list-group-item {
            display: block !important;
        }

        #popup-dropdown {
            left: 0;
            right: 0;
            z-index: 1050;
            /* đảm bảo nằm trên các phần tử khác */
        }
    </style>
@endpush
