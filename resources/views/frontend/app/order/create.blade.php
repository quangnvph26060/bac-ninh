@extends('frontend.app')

@section('content')
    <div class="header_steps_create_order position-relative ">
        <div class="header_step_order">
            <div class=" w-100 d-flex align-items-center gap-4">
                <div class="order-back__wrapper">
                    <a href="" class="d-flex gap-2 align-items-center">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1429 12L4.85718 12" stroke="#42526E" stroke-width="2" stroke-miterlimit="10">
                            </path>
                            <path d="M9.85718 17L4.85718 12L9.85718 7" stroke="#42526E" stroke-width="2"
                                stroke-miterlimit="10" stroke-linecap="square"></path>
                        </svg>
                        <p class="order-back__text">Trở lại Đơn hàng</p>
                    </a>
                </div>

                <div class="text-center w-100">
                    <div class="d-flex align-items-center justify-content-center gap-3" style="color: #091E42">
                        <p class="required fw-bold fs-6">Tên Đơn Hàng
                        </p>
                        <input type="text" placeholder="Tên đơn hàng" class="input_order_name text-center p-2">
                    </div>
                    {{-- <p class="mb-0 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('j \t\h\á\n\g n \n\ă\m Y') }}</p> --}}
                    <p class="mb-0 mt-1" style="color: #5e6c84; font-weight: 400;">{{ now()->format('M jS Y') }}</p>
                </div>

                <div class="d-flex align-items-center gap-3 w-25">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <span class="text-center fw-normal" style="color: #5E6C84; font-size: 14px;">
                            Tổng
                        </span>
                        <div class="d-flex gap-3">
                            <p class="fw-bold text-center " style="color: #42526E; font-size: 14px;">
                                $ <span class="final-price">0</span>
                            </p>
                        </div>
                    </div>
                    <button style="width: 158px; height: 40px;" type="button"
                        class="ant-btn ant-btn-default py-2 px-4 btn-to-shipping" disabled=""><span>Vận
                            chuyển</span></button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 steps-nav justify-content-center p-5">
        <div class="step active" data-step="1">
            <span class="step-number">1</span>
            <span class="step-title">Sản phẩm</span>
        </div>
        <span class="divider">–</span>
        <div class="step" data-step="2">
            <span class="step-number">2</span>
            <span class="step-title">Vận chuyển</span>
        </div>
        <span class="divider">–</span>
        <div class="step" data-step="3">
            <span class="step-number">3</span>
            <span class="step-title">Đánh giá đơn hàng</span>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-product">
            <div class="header_step_create_order_inner">
                <div class="w-100 mx-auto step_product">
                    <button class="d-flex my-4 align-items-center justify-content-center w-100 border rounded"
                        style="border-color: #5BB7AF !important; padding-top: 21px; padding-bottom: 21px;"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                        <p class="d-flex align-items-center fw-bold mb-0 me-2" style="color: #5BB7AF; font-size: 14px;">Thêm
                            sản
                            phẩm</p>
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 8V16" stroke="#1ABC9C" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="square" />
                            <path d="M16 12H8" stroke="#1ABC9C" stroke-width="2" stroke-miterlimit="10"
                                stroke-linecap="square" />
                        </svg>
                    </button>
                </div>

                <div class="my-4 position-relative" id="confirmed-products-wrapper">
                </div>

                <div class="my-4 rounded border w-100"
                    style="border-color: #DFE1E6; box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 2px;">
                    <div class="p-3">
                        <div class="d-flex justify-content-between">
                            <p class="fw-bold" style="color: #42526E; font-size: 14px;">
                                Tổng phụ (<span id="qty-order-item"></span> cái)
                            </p>
                            <p class="fw-bold" style="color: #42526E; font-size: 14px;">$ <span class="final-price">0</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                    <button type="button" class="ant-btn ant-btn-default py-2 px-4 h-auto btn-to-shipping"
                        disabled=""><span>Vận
                            chuyển</span></button>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab-transport" style="display: none;">
            <div class="header_step_create_order_inner">
                <form>
                    <div class="my-4 mx-auto border rounded p-3 step_shipping">
                        <div class="row">
                            <h1 class="fw-bold text-base mb-4" style="color: #091E42">Người nhận</h1>
                            <div class="form-group mb-3 col-lg-6">
                                <label for="first_name" class="required form-label">Họ</label>
                                <input type="text" class="form-control" name="first_name" id="first_name"
                                    placeholder="Họ" required="" aria-required="true">
                            </div>
                            <div class="form-group mb-3 col-lg-6">
                                <label for="last_name" class="required form-label">Tên</label>
                                <input type="text" class="form-control" name="last_name" id="last_name"
                                    placeholder="Tên" required="" aria-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" id="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="form-group mb-3 col-lg-6">
                                <label for="phone_number" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone_number"
                                    placeholder="Số điện thoại">
                            </div>

                            <hr class="my-3">

                            <h1 class="fw-bold text-base mb-4" style="color: #091E42">Địa chỉ giao hàng</h1>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="" class="required form-label">Quốc gia</label>
                                <select name="" id="" class="form-select" required=""
                                    aria-required="true"></select>
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="" class="required form-label">Tiểu bang</label>
                                <select name="" id="" class="form-select" required=""
                                    aria-required="true"></select>
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="city" class="required form-label">Thành phố</label>
                                <select name="" id="city" class="form-select" required=""
                                    aria-required="true"></select>
                            </div>
                            <div class="form-group mb-3 col-lg-6">
                                <label for="phone_number" class="required form-label">Mã zip</label>
                                <input type="text" class="form-control" id="phone_number"
                                    placeholder="Số điện thoại">
                            </div>
                            <div class="form-group mb-3 col-lg-12">
                                <label for="phone_number" class="required form-label">Địa chỉ chi tiết</label>
                                <textarea name="" id="" class="form-control" required="" aria-required="true"></textarea>
                            </div>
                            <div class="form-group mb-3 col-lg-12">
                                <label for="tax_number" class=" form-label">Mã số thuế</label>
                                <input type="text" class="form-control" id="tax_number" placeholder="Mã số thuế">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                        <button type="button" class="ant-btn ant-btn-primary py-2 px-4 h-auto"
                            id="btn-to-review-order"><span>Đánh giá đơn hàng</span></button>
                        <a href="" class="fw-bold" id="back-step-1">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane" id="tab-review" style="display: none;">
            <div class="header_step_create_order_inner">

                <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                    <div class="d-flex gap-3">
                        <button type="button" class="ant-btn ant-btn-default py-2 px-4 h-auto"><span>Thanh toán
                                ngay</span></button>
                        <button type="button" class="ant-btn ant-btn-primary py-2 px-4 h-auto"><span>Lưu đơn
                                hàng</span></button>
                    </div>

                    <a href="#" class="fw-bold" id="back-step-2">Quay lại</a>
                </div>
                <!-- Nội dung đánh giá -->
            </div>
        </div>

    </div>



    <!-- Offcanvas Fullscreen -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel"
        style="height: 100vh;">
        <div class="offcanvas-header ant-drawer-header">
            <h5 class="offcanvas-title ant-drawer-title" id="offcanvasBottomLabel">Thêm sản phẩm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="px-4">
                <input type="search" name="search_text" placeholder="Tìm sản phẩm..." class="form-control me-2">
            </div>

            <div class="result-product p-4">
                @include('frontend.app.order._product_list', ['products' => $products])
            </div>

            <div id="selected-products-wrapper" class="selected-wrapper">
                <div id="selected-products-images" class="d-flex gap-3 flex-wrap"></div>
                <div class="text-end">
                    <button class="btn btn-orange">Bắt đầu tạo đơn hàng mới</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        function updateStepStatus(currentStep, nextStep) {
            // Ẩn tất cả tab
            $('.tab-pane').hide();

            // Hiện tab mới
            $('#' + nextStep.tabId).show();

            // Gỡ active cũ
            $('.step').removeClass('active');

            // Với mỗi bước từ 1 đến bước hiện tại → thêm lại class active
            for (let i = 1; i <= nextStep.step; i++) {
                $('.step[data-step="' + i + '"]').addClass('active');

                if (i < nextStep.step) {
                    $('.step[data-step="' + i + '"] .step-number').html('&#10003;');
                } else {
                    $('.step[data-step="' + i + '"] .step-number').html(i);
                }
            }
        }


        // Sang bước 2
        $(document).on('click', '.btn-to-shipping', function() {
            updateStepStatus(1, {
                step: 2,
                tabId: 'tab-transport'
            });
        });

        // Quay lại bước 1
        $(document).on('click', '#back-step-1', function(e) {
            e.preventDefault(); // Chặn chuyển trang nếu là thẻ <a>
            updateStepStatus(2, {
                step: 1,
                tabId: 'tab-product'
            });

            // Gỡ active ở bước 2
            $('.step[data-step="2"]').removeClass('active');
        });

        // Sang bước 3
        $(document).on('click', '#btn-to-review-order', function() {
            const form = $(this).closest("form")[0];

            if (!form.checkValidity()) {
                form.reportValidity(); // Hiển thị lỗi input
                return;
            }

            updateStepStatus(2, {
                step: 3,
                tabId: 'tab-review'
            });

            $('.step[data-step="2"] .step-number').html('&#10003;');
            $('.step[data-step="3"]').addClass('active');
        });



        // Quay lại bước 2
        $(document).on('click', '#back-step-2', function(e) {
            e.preventDefault();

            updateStepStatus(3, {
                step: 2,
                tabId: 'tab-transport'
            });

            // Gỡ active ở bước 3
            $('.step[data-step="3"]').removeClass('active');
            $('.step[data-step="3"] .step-number').html('3');
        });


        $(function() {
            const confirmed = getConfirmedOrders();

            $('#qty-order-item').text(confirmed.length)

            if (confirmed.length <= 0) {
                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById(
                    'offcanvasBottom'));
                offcanvas.show();
            } else {
                fetchConfirmedProductDetails(confirmed, function(products) {
                    renderConfirmedProducts(products);
                });
            }

            $('#offcanvasBottom').on('hide.bs.offcanvas', function(e) {
                const confirmed = getConfirmedOrders();
                if (confirmed.length === 0) {
                    e.preventDefault(); // Ngăn không cho đóng
                }
            });

            let debounceTimer;

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                fetchProducts(url);
            });

            $('input[name="search_text"]').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    fetchProducts(window.location.href);
                }, 500); // đợi 0.5 giây sau khi người dùng dừng gõ
            });

            $(document).on('change', 'select[name="per_page"]', function() {
                fetchProducts(window.location.href);
            });

            function fetchProducts(url) {
                const searchText = $('input[name="search_text"]').val();
                const perPage = $('select[name="per_page"]').val();

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        search_text: searchText,
                        per_page: perPage,
                    },
                    success: function(data) {
                        $('.result-product').html(data);
                        highlightSelectedProducts();
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi khi tải dữ liệu.');
                    }
                });
            }

            function getSelectedProducts() {
                return JSON.parse(localStorage.getItem('selectedProducts') || '[]');
            }

            function setSelectedProducts(data) {
                localStorage.setItem('selectedProducts', JSON.stringify(data));
            }

            function getConfirmedOrders() {
                return JSON.parse(localStorage.getItem('confirmedOrders') || '[]');
            }

            function setConfirmedOrders(data) {
                localStorage.setItem('confirmedOrders', JSON.stringify(data));
            }

            $('.btn-orange').on('click', function() {
                const currentSelection = getSelectedProducts();
                if (currentSelection.length === 0) return;

                let confirmed = getConfirmedOrders();

                // Bước 1: Giữ lại các sản phẩm trong `confirmed` nếu vẫn còn trong `currentSelection`
                confirmed = confirmed.filter(c =>
                    currentSelection.some(item => item.id === c.id)
                );

                // Bước 2: Thêm các sản phẩm mới chưa có trong `confirmed`
                const newItems = currentSelection.filter(item =>
                    !confirmed.some(c => c.id === item.id)
                );

                // Gộp lại
                confirmed = confirmed.concat(newItems);

                console.log(confirmed);


                setConfirmedOrders(confirmed);
                localStorage.removeItem('selectedProducts');
                renderSelectedProducts();

                const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById(
                    'offcanvasBottom'));
                offcanvas.hide();

                $('#qty-order-item').text(confirmed.length)

                fetchConfirmedProductDetails(confirmed, function(products) {
                    renderConfirmedProducts(products);
                });
            });

            function checkAllProductsSelected() {
                let allSelected = true;

                // Kiểm tra tất cả các sản phẩm đã chọn
                $('.custom-form').each(function() {
                    const productId = $(this).data('id');
                    const time = $(this).data('time');

                    // Nếu sản phẩm là biến thể, kiểm tra xem tất cả các select đã được chọn
                    const selects = $(
                        `.product-attr-select[data-product-id="${productId}"][data-time="${time}"]`);
                    selects.each(function() {
                        if (!$(this).val()) {
                            allSelected = false;
                        }
                    });

                    // Kiểm tra số lượng hợp lệ
                    const quantity = $(this).find('.step_product_input').val();
                    if (!quantity || isNaN(quantity) || parseInt(quantity) < 1) {
                        allSelected = false;
                    }
                });

                return allSelected;
            }

            function toggleShippingButton() {
                if (checkAllProductsSelected()) {
                    $('.btn-to-shipping').prop('disabled', false).removeClass('ant-btn-default').addClass(
                        'ant-btn-primary');
                } else {
                    $('.btn-to-shipping').prop('disabled', true).removeClass('ant-btn-primary').addClass(
                        'ant-btn-default');
                }
            }

            function renderConfirmedProducts(products) {
                const newProductIds = products.map(product => product.id);

                // Xóa các sản phẩm đã bị loại khỏi danh sách mới
                document.querySelectorAll('#confirmed-products-wrapper .custom-form').forEach(el => {
                    const id = parseInt(el.getAttribute('data-id'));
                    if (!newProductIds.includes(id)) {
                        el.remove();
                    }
                });

                const existingIds = Array.from(document.querySelectorAll('.custom-form'))
                    .map(el => parseInt(el.getAttribute('data-id')));

                const html = products
                    .filter(product => !existingIds.includes(product.id))
                    .map(product => {
                        const totalAttrs = product.attributes.length;
                        const colClass = totalAttrs === 1 ? 'col-md-12' : totalAttrs === 2 ? 'col-md-6' :
                            'col-md-4';

                        // Nếu là variant thì tạo selects
                        const selectsHtml = product.type === 'variant' ?
                            product.attributes.map(attr => `
                                <div class="${colClass} mb-3">
                                    <label class="form-label">${attr.attribute_name} <span class="text-danger">*</span></label>
                                    <select class="form-select product-attr-select" data-product-id="${product.id}" data-time="${product.time}" required>
                                        <option value="" disabled selected>${attr.attribute_name}</option>
                                        ${Object.entries(attr.values).map(([id, value]) => `<option value="${id}">${value}</option>`).join('')}
                                    </select>
                                </div>
                        `).join('') :
                            '';

                        // Nếu là variant thì thêm class d-none để ẩn, ngược lại hiển thị d-flex luôn
                        const infoVariantClass = product.type === 'variant' ?
                            'd-none' :
                            'd-flex';

                        const isVariant = product.type === 'variant';

                        return `
                            <div class="custom-form mb-3" data-id="${product.id}" data-time="${product.time}">
                                <div class="d-flex align-items-center mb-3 justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="${product.image}" alt="${product.name}" class="me-3" style="width: 60px; height: auto;">
                                        <h2 class="product-title mb-0 fs-5">${product.name}</h2>
                                    </div>
                                    <div class="btn-action">
                                        ${isVariant ? `<button type="button" class="btn btn-outline-primary btn-sm btn-clone-product" data-id="${product.id}">Nhân bản</button>` : ''}
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-confirmed" data-id="${product.id}">Xóa</button>
                                    </div>
                                </div>
                                ${selectsHtml ? `<div class="row">${selectsHtml}</div>` : ''}
                                <div class="${infoVariantClass} justify-content-between align-items-stretch pb-4" id="info_variant_${product.id}" data-time="${product.time}">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div class="d-flex flex-column justify-content-center">
                                            <p class="mb-1 " style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                Giá sản phẩm
                                            </p>
                                            <p class="variant-price text-primary fw-bold fs-6 mb-0" style="color: #091E42 !important" data-product-id="${product.id}" data-time="${product.time}">
                                                <span>${isVariant ? '' : product.price}</span>$
                                            </p>
                                        </div>
                                        <div class="ms-3 d-flex flex-column justify-content-center">
                                            <p class="required mb-1" style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                Số lượng
                                            </p>
                                            <input class="step_product_input form-control" style="height: 48px;" min="1" value="1"/>
                                        </div>
                                        <div class="ms-3 d-flex flex-column justify-content-center">
                                            <p class="mb-1 " style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                Tổng giá
                                            </p>
                                            <p class="text-primary fw-bold fs-6 mb-0 total-price" style="color: #091E42 !important" data-product-id="${product.id}" data-time="${product.time}">
                                                <span>${isVariant ? '' : product.price}</span>$
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }).join('');

                $('#confirmed-products-wrapper').append(html);

                updateSubtotal()
                toggleShippingButton()

                document.querySelector('#confirmed-products-wrapper').addEventListener('click', function(e) {
                    if (e.target.classList.contains('btn-clone-product')) {
                        const productId = e.target.getAttribute('data-id');

                        const productElement = e.target.closest('.custom-form');
                        const clone = productElement.cloneNode(true);
                        const newTime = Date.now() + Math.random();

                        // Cập nhật lại tất cả data-time
                        clone.querySelectorAll('[data-time]').forEach(el => {
                            el.setAttribute('data-time', newTime);
                        });
                        clone.setAttribute('data-time', newTime);

                        // Làm trống các select
                        const selects = clone.querySelectorAll('.product-attr-select');
                        selects.forEach(select => {
                            select.value = "";
                        });

                        // Ẩn info_variant_ và reset giá, tổng giá
                        const infoVariant = clone.querySelector(`#info_variant_${productId}`);
                        if (infoVariant) {
                            infoVariant.classList.remove('d-flex');
                            infoVariant.classList.add('d-none');

                            const priceEl = infoVariant.querySelector('.variant-price span');
                            const totalPriceEl = infoVariant.querySelector('.total-price span');
                            if (priceEl) priceEl.textContent = '';
                            if (totalPriceEl) totalPriceEl.textContent = '';
                        }

                        // Reset input số lượng về 1
                        const quantityInput = clone.querySelector('.step_product_input');
                        if (quantityInput) quantityInput.value = 1;

                        // Thêm phần tử vào DOM
                        $('#confirmed-products-wrapper').append(clone);

                        // Cập nhật localStorage
                        const confirmed = getConfirmedOrders();
                        const imgEl = productElement.querySelector('img');
                        const imgSrc = imgEl ? imgEl.getAttribute('src') : '';

                        confirmed.push({
                            id: parseInt(productId),
                            image: imgSrc,
                            time: newTime
                        });

                        $('#qty-order-item').text(confirmed.length);
                        localStorage.setItem('confirmedOrders', JSON.stringify(confirmed));

                        updateSubtotal();
                        toggleShippingButton();
                    }
                });
            }

            $(document).on('change', '.product-attr-select', function() {
                const productId = $(this).data('product-id');
                const time = $(this).data('time'); // Lấy time để phân biệt

                // Tìm tất cả các select thuộc sản phẩm này và bản clone cụ thể (phân biệt bằng time)
                const selects = $(
                    `.product-attr-select[data-product-id="${productId}"][data-time="${time}"]`);

                const selectedValues = [];
                let allSelected = true;

                selects.each(function() {
                    const value = $(this).val();
                    if (!value) {
                        allSelected = false;
                    } else {
                        selectedValues.push(value);
                    }
                });

                if (allSelected) {
                    // Gọi API với productId và selectedValues
                    fetchVariantPrice(productId, selectedValues, time);
                }
            });


            function fetchVariantPrice(productId, selectedValues, time) {
                $.ajax({
                    url: 'get-variant-price',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        value_ids: selectedValues
                    },
                    success: function(response) {
                        const info_variant = $(`#info_variant_${productId}[data-time="${time}"]`);

                        // Kiểm tra nếu biến thể này đã được dùng ở 1 sản phẩm khác
                        const isUsedElsewhere = $(`[id^="info_variant_"]`).filter(function() {
                            return (
                                $(this).attr('data-variant-id') == response.variant_id &&
                                !$(this).is(info_variant)
                            );
                        }).length > 0;

                        if (isUsedElsewhere) {
                            notyf.error('Biến thể bạn chọn đã được sử dụng!');

                            // Đóng info_variant lại
                            info_variant.removeAttr('data-variant-id');

                            // Reset tất cả select
                            $(`.product-attr-select[data-product-id="${productId}"][data-time="${time}"]`)
                                .each(function() {
                                    $(this).val("").trigger("change");
                                });

                            // Reset input fields nếu có
                            $(`.custom-form[data-id="${productId}"][data-time="${time}"] input`).val(1);

                            // Ẩn phần hiển thị giá / tổng giá
                            const form = $(`.custom-form[data-id="${productId}"][data-time="${time}"]`);
                            form.find('.d-flex.align-items-stretch').removeClass('d-flex').addClass(
                                'd-none');

                            return;
                        }

                        // Nếu chưa trùng, tiến hành xử lý như bình thường
                        info_variant.attr('data-variant-id', response.variant_id);

                        // Cập nhật giá
                        $(`.variant-price[data-product-id="${productId}"][data-time="${time}"] span`)
                            .text(`${response.price}`);

                        const form = $(`.custom-form[data-id="${productId}"][data-time="${time}"]`);
                        form.find('.d-none').removeClass('d-none').addClass('d-flex');

                        const totalPriceEl = form.find(
                            `.total-price[data-product-id="${productId}"][data-time="${time}"] span`
                        );
                        totalPriceEl.text(`${response.price}`);

                        updateSubtotal();
                        toggleShippingButton()
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON?.message || "Đã xảy ra lỗi.");

                        const info_variant = $(`#info_variant_${productId}[data-time="${time}"]`);
                        info_variant.removeAttr('data-variant-id');

                        $(`.product-attr-select[data-product-id="${productId}"][data-time="${time}"]`)
                            .each(function() {
                                $(this).val("").trigger("change");
                            });

                        $(`.custom-form[data-id="${productId}"][data-time="${time}"] input`).val(1);

                        const form = $(`.custom-form[data-id="${productId}"][data-time="${time}"]`);
                        form.find('.d-flex.align-items-stretch').removeClass('d-flex').addClass(
                            'd-none');

                        toggleShippingButton()
                    }
                });
            }

            function updateSubtotal() {
                // Cập nhật tổng tiền tất cả sản phẩm
                let total = 0;
                $('.total-price span').each(function() {
                    const value = parseFloat($(this).text());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
                $('.final-price').text(total.toFixed(0));
            }

            $(document).on('blur', '.step_product_input', function() {
                const $input = $(this);
                const quantity = $input.val().trim();
                const productId = $input.closest('.custom-form').data('id');
                const time = $input.closest('.custom-form').data('time');
                const variantId = $(`#info_variant_${productId}[data-time="${time}"]`).data('variant-id');

                // Kiểm tra số lượng hợp lệ (phải là số nguyên dương)
                if (!quantity || isNaN(quantity) || parseInt(quantity) < 1) {
                    alert('Vui lòng nhập số lượng hợp lệ (>= 1)');
                    $input.focus();
                    $input.val(1)
                    updateSubtotal()
                    return;
                }

                // Gửi Ajax kiểm tra tồn kho
                $.ajax({
                    url: '{{ route('app.orders.check-stock') }}',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`.total-price[data-product-id="${productId}"][data-time="${time}"] span`)
                                .text(
                                    `${response.totalPrice}`)

                            updateSubtotal()
                            toggleShippingButton();
                        }
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON?.message ||
                            "Đã có lỗi xảy ra khi kiểm tra tồn kho!");

                        $input.val(1)

                        let total_price = $(
                            `.total-price[data-product-id="${productId}"][data-time="${time}"] span`
                        );

                        total.text(
                            `${xhr.responseJSON.totalPrice == 0 ? total_price.text() : xhr.responseJSON.totalPrice}`
                        )
                    }
                });
            });


            $(document).on('click', '.btn-delete-confirmed', function() {
                const $form = $(this).closest('.custom-form');
                const productId = $form.data('id');
                const productTime = $form.data('time');

                let confirmed = getConfirmedOrders();

                // Xóa theo productId và time (đảm bảo xóa đúng sản phẩm gốc, không phải clone)
                confirmed = confirmed.filter(item => !(item.id === productId && item.time === productTime));

                // Cập nhật lại localStorage
                setConfirmedOrders(confirmed);

                if (confirmed.length <= 0) {
                    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(document.getElementById(
                        'offcanvasBottom'));
                    offcanvas.show();
                }

                // Xóa phần tử HTML
                $form.remove();

                $('#qty-order-item').text(confirmed.length)

                updateSubtotal();
            });


            function fetchConfirmedProductDetails(confirmedOrders, callback) {

                $.ajax({
                    url: '{{ route('app.orders.get.products') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        product_ids: confirmedOrders.map(p => p.id)
                    }),
                    success: function(response) {
                        const merged = confirmedOrders.map(confirmed => {
                            const product = response.find(p => p.id === confirmed.id);
                            return {
                                ...product,
                                time: confirmed.time || Date.now() + Math.random()
                            };
                        });

                        callback(merged); // Trả lại đủ số lượng bản
                    },
                    error: function() {
                        console.error('Không thể load thông tin sản phẩm');
                    }
                });
            }


            $('#offcanvasBottom').on('hidden.bs.offcanvas', function() {
                localStorage.removeItem('selectedProducts');
                $('.product_card.selected').removeClass('selected');
                renderSelectedProducts();
            });

            $(window).on('load', function() {
                localStorage.removeItem('selectedProducts');
                $('.product_card.selected').removeClass('selected');
                renderSelectedProducts();
            });

            $(window).on('beforeunload', function(event) {
                // localStorage.clear();
            });

            $('#offcanvasBottom').on('show.bs.offcanvas', function() {
                const currentConfirmed = getConfirmedOrders();

                if (currentConfirmed.length === 0) return;

                let selection = getSelectedProducts();

                selection = selection.concat(currentConfirmed);

                setSelectedProducts(selection)

                renderSelectedProducts();
                highlightSelectedProducts();
            });

            $(document).on('click', '.product_card', function() {
                const $item = $(this).closest('.product_item');
                const id = $item.data('id');
                const imgSrc = $item.find('.product_image img').attr('src');

                let selected = getSelectedProducts();
                const exists = selected.find(p => p.id === id);

                if (exists) {
                    selected = selected.filter(p => p.id !== id);
                    $(this).removeClass('selected');
                } else {
                    selected.push({
                        id,
                        image: imgSrc,
                        time: Date.now()
                    });
                    $(this).addClass('selected');
                }

                setSelectedProducts(selected);
                renderSelectedProducts();
            });

            function highlightSelectedProducts() {
                const selected = getSelectedProducts();
                $('.product_item').each(function() {
                    const id = $(this).data('id');
                    if (selected.some(p => p.id === id)) {
                        $(this).find('.product_card').addClass('selected');
                    }
                });
            }

            $(document).on('click', '.remove-selected-product', function() {
                const id = parseInt($(this).data('id'));
                let selected = getSelectedProducts().filter(p => p.id !== id);
                setSelectedProducts(selected);
                renderSelectedProducts();

                $(`.product_item[data-id="${id}"] .product_card`).removeClass('selected');
            });


            function renderSelectedProducts() {
                const selected = getSelectedProducts();

                if (selected.length === 0) {
                    $('#selected-products-wrapper').removeClass('show');
                    $('#selected-products-images').html('');
                    $('.result-product').css('margin-bottom', '0');
                    return;
                }

                const html = selected.map(({
                    id,
                    image,
                    time
                }) => {
                    return `
                        <div class="position-relative">
                            <img src="${image}" class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                            <button class="remove-selected-product position-absolute top-0 start-100 translate-middle bg-white border rounded-circle shadow-sm px-2 py-0" data-id="${id}" data-time="${time}">&times;</button>
                        </div>
                    `;
                }).join('');

                $('#selected-products-images').html(html);
                $('#selected-products-wrapper').addClass('show');
                $('.result-product').css('margin-bottom', '100px');
            }

            highlightSelectedProducts();
            renderSelectedProducts()
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endpush
