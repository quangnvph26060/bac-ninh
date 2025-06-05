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
                        <p class="order-back__text">Return to Orders</p>
                    </a>
                </div>

                <div class="text-center w-100">
                    <div class="d-flex align-items-center justify-content-center gap-3" style="color: #091E42">
                        <p class="required fw-bold fs-6">Order Name
                        </p>
                        <input type="text" placeholder="Order Name" class="input_order_name text-center p-2">
                    </div>
                    {{-- <p class="mb-0 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('j \t\h\á\n\g n \n\ă\m Y') }}</p> --}}
                    <p class="mb-0 mt-1" style="color: #5e6c84; font-weight: 400;">{{ now()->format('M jS Y') }}</p>
                </div>

                <div class="d-flex align-items-center gap-3 w-25">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <span class="text-center fw-normal" style="color: #5E6C84; font-size: 14px;">
                            Total
                        </span>
                        <div class="d-flex gap-3">
                            <p class="fw-bold text-center " style="color: #42526E; font-size: 14px;">
                                <span class="final-price">$0</span>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="ant-btn ant-btn-default py-2 px-4 h-auto" id="top-main-button"
                        disabled="">
                        <span>Transport</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 steps-nav justify-content-center p-5">
        <div class="step active" data-step="1">
            <span class="step-number">1</span>
            <span class="step-title">Product</span>
        </div>
        <span class="divider">–</span>
        <div class="step" data-step="2">
            <span class="step-number">2</span>
            <span class="step-title">Transport</span>
        </div>
        <span class="divider">–</span>
        <div class="step" data-step="3">
            <span class="step-number">3</span>
            <span class="step-title">Rate your order</span>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-product">
            <div class="header_step_create_order_inner">
                <div class="w-100 mx-auto step_product">
                    <button class="d-flex my-4 align-items-center justify-content-center w-100 border rounded"
                        style="border-color: #5BB7AF !important; padding-top: 21px; padding-bottom: 21px;"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                        <p class="d-flex align-items-center fw-bold mb-0 me-2" style="color: #5BB7AF; font-size: 14px;">Add
                            products</p>
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
                                Subtotal (<span id="qty-order-item"></span> pieces)
                            </p>
                            <p class="fw-bold" style="color: #42526E; font-size: 14px;"><span class="final-price">$0</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                    <button type="button" class="ant-btn ant-btn-default py-2 px-4 h-auto " disabled=""
                        id="btn-to-shipping">
                        <span>Transport</span>
                    </button>
                </div>
            </div>

        </div>

        <div class="tab-pane" id="tab-transport" style="display: none;">
            <div class="header_step_create_order_inner">
                <form>
                    <div class="my-4 mx-auto border rounded p-3 step_shipping">
                        <div class="row">
                            <h1 class="fw-bold text-base mb-4" style="color: #091E42">Receiver</h1>
                            <div class="form-group mb-3 col-lg-6">
                                <label for="first_name" class="required form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="first_name"
                                    placeholder="Họ" data-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="last_name" class="required form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name"
                                    placeholder="Tên" data-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Email">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="phone_number" class="form-label">Phone number</label>
                                <input type="text" name="phone_number" class="form-control" id="phone_number"
                                    placeholder="Phone number" pattern="^\d{8,15}$"
                                    title="Phone number must contain 8 to 15 digits, no spaces or special characters.">
                            </div>

                            <hr class="my-3">

                            <h1 class="fw-bold text-base mb-4" style="color: #091E42">Delivery address</h1>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="country" class="required form-label">Nation</label>
                                <input type="text" name="country" id="country" class="form-control"
                                    placeholder="Nation" data-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="state" class="form-label required">State</label>
                                <input type="text" name="state" id="state" class="form-control"
                                    placeholder="State" data-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" name="city" id="city" class="form-control"
                                    placeholder="City" data-required="true">
                            </div>

                            <div class="form-group mb-3 col-lg-6">
                                <label for="zip_code" class="required form-label">Zip code</label>
                                <input type="text" name="zip_code" class="form-control" id="zip_code"
                                    placeholder="Zip code">
                            </div>
                            <div class="form-group mb-3 col-lg-12">
                                <label for="shipping_address" class="required form-label">Detailed address</label>
                                <textarea name="shipping_address" id="shipping_address" class="form-control" data-required="true"></textarea>
                            </div>
                            <div class="form-group mb-3 col-lg-12">
                                <label for="note" class="form-label">Note</label>
                                <textarea name="note" id="note" class="form-control" placeholder="Note"></textarea>
                            </div>

                            <hr class="my-3">

                            <h1 class="fw-bold text-base mb-3" style="color: #091E42">Delivery method</h1>

                            <div style="padding: 0 0.85rem" id="shipping-methods-wrapper">
                                @php
                                    $shippingMethods = [
                                        'standard_shipping' => 'Standard Shipping (US)',
                                        'express_shipping' => 'Express Shipping (US)',
                                        'international_shipping' => 'International Shipping',
                                    ];
                                @endphp
                                @foreach ($shippingMethods as $key => $methodName)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping_method"
                                            id="{{ $key }}" @checked($loop->first)
                                            value="{{ $key }}">
                                        <label class="form-check-label fw-bold text-muted" for="{{ $key }}">
                                            {{ $methodName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                        <button type="button" class="ant-btn ant-btn-primary py-2 px-4 h-auto" id="btn-to-review-order">
                            <span>Rate your order</span>
                        </button>
                        <a href="" class="fw-bold" id="back-step-1">Come back</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane" id="tab-review" style="display: none;">
            <div class="header_step_create_order_inner">

                <div class="card px-3 pt-3 mb-3">
                    <div class="card-header">
                        <h5 class="fs-6 fw-bold">Product</h5>
                    </div>
                    <div class="card-body">
                        <div id="review-product-list">

                        </div>
                    </div>
                </div>

                <div class="card px-3 pt-3 mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold fs-6">Delivery arrives</h5>
                    </div>
                    <div class="card-body card-shipping">

                    </div>
                </div>

                <div class="card px-3 pt-3 mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold fs-6">Payment method</h5>
                    </div>
                    <div class="card-body">
                        <div class="card-payment">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input me-2" type="radio" name="paymentMethod"
                                    id="paymentWallet" value="wallet" onclick="showWalletBalance()">
                                <label class="form-check-label" for="paymentWallet">
                                    Wallet payment
                                </label>
                            </div>
                            <div id="walletBalance" class="ms-4" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center border px-3 py-2 rounded">
                                    <div class="">
                                        <p class="mb-0 fw-bold">Balance</p>
                                        <p class="mb-0 fw-bold">${{ formatPrice($wallet->balance) }}</p>
                                    </div>
                                    <button type="button" class="ant-btn-primary text-white px-3 py-1">Deposit
                                        money</button>
                                </div>
                                <div class="alert alert-danger mt-2" role="alert">
                                    Your balance is insufficient, top up now or choose another payment method.
                                </div>
                            </div>

                            <div class="form-check d-flex align-items-center mt-2">
                                <input class="form-check-input me-2" type="radio" name="paymentMethod"
                                    id="paymentLater" value="later" onclick="hideWalletBalance()" checked>
                                <label class="form-check-label" for="paymentLater">
                                    Pay later
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card px-3 pt-3 mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold fs-6">Discount code</h5>
                    </div>
                    <div class="card-body">
                        <label for="input_coupon" class="form-label">Enter your coupon code</label>
                        <div class="input-group gap-2">
                            <input type="text" id="input_coupon" placeholder="Enter your coupon code"
                                class="form-control rounded-start">
                            <button type="button" onclick="applyCoupon()"
                                class="ant-btn-primary text-white px-3 py-1 rounded-end">Apply</button>
                            <button type="button" id="remove_coupon" style="display: none;"
                                class="btn btn-outline-danger">Cancel code</button>

                        </div>
                    </div>
                </div>

                <div class="card px-3 pt-3 mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold fs-6">Order analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Product price</span>
                            <span class="final-price">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping fee</span>
                            <span id="shipping-method-fee">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount</span>
                            <span id="discount">$0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span id="tax-amount">${{ formatPrice($config['tax_rate']) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span id="order-total-amount">$0</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center my-3 mt-5 gap-3">
                    <div class="d-flex gap-3">
                        <button type="button" class="ant-btn ant-btn-primary py-2 px-4 h-auto" id="btn-save-order">
                            <span>Save order</span>
                        </button>
                    </div>

                    <a href="#" class="fw-bold" id="back-step-2">Come back</a>
                </div>
            </div>
        </div>

    </div>

    <!-- Offcanvas Fullscreen -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel"
        style="height: 100vh;">
        <div class="offcanvas-header ant-drawer-header">
            <h5 class="offcanvas-title ant-drawer-title" id="offcanvasBottomLabel">Add products</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="px-4">
                <input type="search" name="search_text" placeholder="Find products..." class="form-control me-2">
            </div>

            <div class="result-product p-4">
                @include('frontend.app.order._product_list', ['products' => $products])
            </div>

            <div id="selected-products-wrapper" class="selected-wrapper">
                <div id="selected-products-images" class="d-flex gap-3 flex-wrap"></div>
                <div class="text-end">
                    <button class="btn btn-orange">Start creating a new order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreview" class="img-fluid" alt="Preview Image">
                </div>
            </div>
        </div>
    </div>

    @include('frontend.components.modal-photo')
@endsection


@push('scripts')
    <script src="https://unpkg.com/exifr/dist/lite.umd.js"></script>

    <script src="{{ asset('backend/assets/js/helper.js') }}?v={{ filemtime(public_path('backend/assets/js/helper.js')) }}">
    </script>


    <script>
        let originalTotal = null;
        let originalShippingFee = null;
        let oldCoupon = null;
        let defaultTotal = 0;
        let isApplyCoupon = false;

        function getFormData() {
            let result = [];

            $('.custom-form.mb-3').each(function() {
                let $form = $(this);
                let productId = $form.data('id');
                let variantId = $form.find('[id^="info_variant_"]').attr('data-variant-id');
                let qty = $form.find('.step_product_input').val();

                // Lấy model image từ input file (nếu có)
                let modelImageInput = document.getElementById(`model_${productId}`);
                let modelImage = (modelImageInput && modelImageInput.files.length > 0) ? modelImageInput.files[0] :
                    null;

                // 🔍 Lấy đường dẫn ảnh design từ <img src="">
                let designImageElement = document.getElementById(`show_design_${productId}`);
                let designImage = designImageElement ? designImageElement.getAttribute('src') : null;

                // ✅ Kiểm tra nếu chưa có ảnh design hoặc chưa có class has-image thì bỏ qua
                let imageWrapper = document.getElementById(`image_container_${productId}`);
                let hasImage = imageWrapper?.classList.contains('has-image');

                if (!hasImage || !designImage || designImage === '{{ showImage('') }}') {
                    datgin.error(`Please select image design for product ID: ${productId}`);
                    return; // Bỏ qua sản phẩm này
                }

                let item = {
                    productId: productId,
                    qty: qty,
                    design_image: designImage.split('/storage/')[1]
                };

                if (variantId !== undefined) {
                    item.variant_id = variantId;
                }

                if (modelImage) {
                    item.model_image = modelImage;
                }

                result.push(item);
            });

            return result;
        }


        function applyCoupon(forceApply = false) {
            let $input_coupon = $('#input_coupon');
            let couponVal = $input_coupon.val().trim();

            // Nếu không nhập gì hoặc trùng với coupon cũ thì không làm gì cả
            if ((couponVal === '' || couponVal === oldCoupon) && !forceApply) return;

            let shipping = $('input[name="shipping_method"]:checked').val();

            let data = getFormData();

            $.ajax({
                url: '{{ route('orders.apply.coupon') }}',
                method: 'POST',
                data: {
                    coupon: couponVal,
                    options: data,
                    shipping
                },
                success: function(response) {
                    isApplyCoupon = true;

                    defaultTotal = response.subTotal;
                    $('#remove_coupon').show();
                    $('#discount').show().text(`-${formatCurrency(response.discount)}`);
                    $('#order-total-amount, .header_step_order .final-price').text(
                        `${formatCurrency(response.grand_total)}`);

                    // Lưu lại originalTotal nếu chưa lưu
                    if (originalTotal === null && originalShippingFee === null && oldCoupon === null) {
                        originalTotal = $('#order-total-amount').text().replace('$', '').trim();
                        originalShippingFee = $('#discount').text().replace(/[$-]/g, '').trim();
                    }

                    // Cập nhật oldCoupon
                    oldCoupon = couponVal;
                },
                error: function(xhr) {
                    isApplyCoupon = false;

                    datgin.error(xhr.responseJSON.message);

                    $input_coupon.val('');

                    // Khôi phục lại giá trị trước khi giảm
                    if (originalTotal !== null && originalShippingFee !== null && oldCoupon !== null) {
                        $('#order-total-amount, .header_step_order .final-price').text(
                            `${formatCurrency(originalTotal)}`);
                        $('#discount').text(`-${formatCurrency(originalShippingFee)}`);
                        $input_coupon.val(oldCoupon);
                    }
                }
            });
        }

        $('#remove_coupon').on('click', function() {
            oldCoupon = null;
            isApplyCoupon = false;

            // Khôi phục lại tổng tiền ban đầu
            // if (originalTotal !== null) {
            //     $('.final-price, #order-total-amount').text(`${formatCurrency(defaultTotal)}`);
            // }

            $('#discount').text('$0')

            calculateOrderTotal()

            $('#input_coupon').val(''); // Xóa mã
            $('#remove_coupon').hide(); // Ẩn nút Hủy mã
        });

        $('#btn-save-order').on('click', async function() {
            const formData = new FormData();
            let products =
                getFormData();

            products.forEach((product, index) => {
                // ✅ Nếu model_image là Base64 hoặc URL (tuỳ bạn), giữ nguyên
                if (product.model_image) {
                    formData.append(`products[${index}][model_image]`, product.model_image);
                }

                // ✅ design_image giờ là URL ảnh
                if (product.design_image) {
                    formData.append(`products[${index}][design_image]`, product.design_image);
                }

                formData.append(`products[${index}][productId]`, product.productId);
                formData.append(`products[${index}][qty]`, product.qty);
                if (product.variant_id) {
                    formData.append(`products[${index}][variant_id]`, product.variant_id);
                }
            });

            // ✅ Thông tin đặt hàng
            let coupon = isApplyCoupon ? $('#input_coupon').val() : '';
            let paymentMethod = $('input[name="paymentMethod"]:checked').val();
            let orderInfo = {
                coupon,
                paymentMethod,
                shipping_method: $('input[name="shipping_method"]:checked').val(),
                orderName: $('.input_order_name').val()
            };

            let inputIds = [
                'first_name', 'last_name', 'email', 'phone_number', 'country', 'state', 'city',
                'zip_code', 'shipping_address', 'note'
            ];
            inputIds.forEach(function(id) {
                formData.append(`orderInfo[${id}]`, $(`#${id}`).val());
            });

            // Lặp lại thông tin order vào formData
            formData.append('orderInfo[coupon]', coupon);
            formData.append('orderInfo[payment_method]', paymentMethod);
            formData.append('orderInfo[shipping_method]', orderInfo.shipping_method);
            formData.append('orderInfo[order_name]', orderInfo.orderName);

            // 🚀 Gửi AJAX
            try {
                $('#loading').show();

                const response = await $.ajax({
                    url: "{{ route('orders.store.order') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false
                });

                window.location.href = '{{ route('orders.index') }}';

                localStorage.clear();
            } catch (error) {
                datgin.error(error.responseJSON?.message || "Đã xảy ra lỗi.");
            } finally {
                $('#loading').hide();
            }
        });

        function cleanCurrency(value) {
            return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
        }

        function calculateOrderTotal() {
            const wallet = "{{ $wallet->balance }}"
            const productPrice = cleanCurrency($('.final-price').first().text());
            const shippingFee = cleanCurrency($('#shipping-method-fee').text());
            const tax = cleanCurrency($('#tax-amount').text());
            const extraFee = cleanCurrency($('#extra-fee').text());

            const total = productPrice + shippingFee + tax + extraFee;

            $('#order-total-amount').text(`${formatCurrency(total)}`);

            if (total <= wallet) {
                $('.alert.alert-danger').hide();
            } else {
                $('.alert.alert-danger').show();
            }
        }

        function showWalletBalance() {
            document.getElementById('walletBalance').style.display = 'block';
        }

        function hideWalletBalance() {
            document.getElementById('walletBalance').style.display = 'none';
        }

        function renderReviewProducts(products) {

            const container = $('#review-product-list');
            container.html(''); // clear trước

            products.forEach((product, index) => {
                const html = `
                <div class="">
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-auto">
                            <img src="${product.image}" alt="Product" class="img-thumbnail"
                                style="width: 100px; height: auto;">
                        </div>
                        <div class="col">
                            <h6 class="mb-1 fw-bold">${product.name}</h6>
                            <div class="text-muted small">sản phẩm: ${product.attributes_text}</div>
                            <div class="text-muted small">sku: ${product.sku}</div>
                            <div class="fw-semibold mt-2">
                                Giá sản phẩm: <span class="text-primary">USD ${product.price}</span>
                                <small class="text-muted">(USD ${product.price} x ${product.quantity})</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-3">

                        ${product.model_image ? `
                                                        <div style="width: 103px;">
                                                            <div class="fw-semibold">Mockup</div>
                                                            <div class="image-container">
                                                                <img class="img-thumbnail"
                                                                    style="cursor: pointer;"
                                                                    src="${product.model_image}">
                                                            </div>
                                                        </div>
                                                        ` : ''}

                        ${product.design_image ? `
                                                        <div style="width: 103px;">
                                                            <div class="fw-semibold">Design photo</div>
                                                            <div class="image-container">
                                                                <img class="img-thumbnail"
                                                                    style="cursor: pointer;"
                                                                    src="${product.design_image}">
                                                            </div>
                                                        </div>
                                                        ` : ''}
                    </div>
                </div>

            ${index < products.length - 1 ? '<hr class="my-3 border-primary">' : ''}
        `;
                container.append(html);
            });
        }

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

        $(document).on('click', '#top-main-button', function() {
            const buttonText = $(this).find('span').text().trim();

            if (buttonText === 'Vận chuyển') {
                goToTransportStep()
            }

            if (buttonText === 'Đánh giá đơn hàng') {
                getShippingInfo()
            }
        });

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

            const targetButton = $('#' + getButtonIdByTab(nextStep.tabId)); // Lấy nút của tab mới

            const topButton = $('#top-main-button'); // Nút trên cùng bạn muốn đổi

            if (targetButton.length && topButton.length) {
                const newText = targetButton.find('span').text();

                topButton.find('span').text(newText);
            }
        }

        function getButtonIdByTab(tabId) {
            switch (tabId) {
                case 'tab-transport':
                    return 'btn-to-review-order'; // Tab vận chuyển → nút Đánh giá đơn hàng
                case 'tab-review':
                    return 'btn-save-order'; // Tab đánh giá → nút Lưu đơn hàng
                case 'tab-product':
                    return 'btn-to-shipping'; // Tab sản phẩm → nút Vận chuyển
                default:
                    return '';
            }
        }

        function goToTransportStep() {
            if (!checkAllProductsSelected()) {
                datgin.error('Vui lòng chọn đầy đủ các thuộc tính.');
                return;
            }

            const products = [];
            let allImagesSelected = true;
            const promises = [];

            $('#confirmed-products-wrapper .custom-form').each(function() {
                const productEl = $(this);
                const productId = parseInt(productEl.attr('data-id'));
                const time = productEl.attr('data-time');
                const name = productEl.find('.product-title').text().trim();
                const sku = productEl.find('.product-sku').text().trim();
                const image = productEl.find('img').attr('src');
                const price = productEl.find('.variant-price span').text().trim();
                const totalPrice = productEl.find('.total-price span').text().trim();
                const quantity = productEl.find('.step_product_input').val();

                const imageWrapper = $(`#image_container_${productId}`);
                const imgEl = imageWrapper.find(`#show_design_${productId}`);
                const imageSrc = imgEl.attr('src') || '';
                const hasImage = imageWrapper.hasClass('has-image');
                let designImageBase64 = null;
                let modelImageBase64 = null;

                // Kiểm tra xem đã có ảnh design chưa
                if (!hasImage || imageSrc === '' || imageSrc.includes('default')) {
                    datgin.error(`Vui lòng chọn ảnh thiết kế cho sản phẩm: ${name}`);
                    allImagesSelected = false;
                } else {
                    designImageBase64 = imageSrc; // Lưu URL vào
                }

                // Xử lý ảnh model (vẫn là file input)
                const modelImageInput = productEl.find(`#model_${productId}`);
                if (modelImageInput.get(0)?.files.length > 0) {
                    const modelPromise = new Promise((resolve) => {
                        const modelFile = modelImageInput.get(0).files[0];
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            modelImageBase64 = e.target.result;
                            resolve();
                        };
                        reader.readAsDataURL(modelFile);
                    });
                    promises.push(modelPromise);
                }

                // Thu thập attributes
                const attributes = [];
                productEl.find('select.product-attr-select').each(function() {
                    const attrName = $(this).find('option:selected').text();
                    const attrValueId = $(this).val();
                    if (attrValueId) {
                        attributes.push({
                            name: attrName,
                            value_id: attrValueId
                        });
                    }
                });

                const attributesText = attributes.map(attr => attr.name).join(' - ');

                // Push product vào danh sách sau khi model ảnh load (hoặc ngay nếu không có)
                const finalPush = () => {
                    products.push({
                        id: productId,
                        time: time,
                        name: name,
                        sku: sku,
                        image: image,
                        price: parseFloat(price),
                        total_price: parseFloat(totalPrice),
                        quantity: parseInt(quantity),
                        attributes: attributes,
                        attributes_text: attributesText,
                        design_image: designImageBase64,
                        model_image: modelImageBase64
                    });
                };

                if (modelImageInput.get(0)?.files.length > 0) {
                    promises.push(
                        new Promise((resolve) => {
                            const checkDone = setInterval(() => {
                                if (modelImageBase64 !== null) {
                                    finalPush();
                                    clearInterval(checkDone);
                                    resolve();
                                }
                            }, 100);
                        })
                    );
                } else {
                    finalPush();
                }
            });

            Promise.all(promises).then(() => {
                if (!allImagesSelected) {
                    return;
                }

                renderReviewProducts(products);
                updateStepStatus(1, {
                    step: 2,
                    tabId: 'tab-transport',
                });
            });
        }

        // Sang bước 2
        $(document).on('click', '#btn-to-shipping', function() {
            goToTransportStep()
        });


        // Quay lại bước 1
        $(document).on('click', '#back-step-1', function(e) {
            $('#top-main-button').find('span').text('')

            e.preventDefault(); // Chặn chuyển trang nếu là thẻ <a>
            updateStepStatus(2, {
                step: 1,
                tabId: 'tab-product'
            });

            // Gỡ active ở bước 2
            $('.step[data-step="2"]').removeClass('active');
        });

        async function getShippingInfo() {
            const form = $('#btn-to-review-order').closest("form")[0];

            if (!form.checkValidity()) {
                form.reportValidity(); // Hiển thị lỗi input
                return;
            }

            let isValid = true;
            $('[data-required="true"]').each(function() {
                const $field = $(this);
                const value = $field.val();

                if (!value || value.toString().trim() === '') {
                    $field.addClass('is-invalid');
                    isValid = false;
                } else {
                    $field.removeClass('is-invalid');
                }
            });

            if (!isValid) {
                datgin.error("Vui lòng điền đầy đủ thông tin bắt buộc.");
                return;
            }

            const formDataArray = $(form).serializeArray();

            const formData = {};
            formDataArray.forEach(field => {
                formData[field.name] = field.value;
            });

            // Gộp họ và tên
            const fullName = (formData.first_name || '') + ' ' + (formData.last_name || '');
            const cityName = $('#city').val();
            const stateName = $('#state').val();
            const countryName = $('#country').val();
            const selectedShipping = $('input[name="shipping_method"]:checked');
            const shippingName = selectedShipping.closest('.form-check').find('label').text().trim();
            const shippingFee = selectedShipping.data('fee') || 0;

            const addressParts = [
                formData.shipping_address?.trim(),
                cityName?.trim(),
                stateName?.trim(),
                countryName?.trim()
            ].filter(Boolean); // Xoá các phần tử rỗng, null, undefined

            const fullAddress = addressParts.join(', ');

            // Hiển thị dữ liệu lên tab-review
            const shippingInfoHTML = `
                <p>Họ tên: ${fullName}</p>
                <p>Email: ${formData.email || ''}</p>
                <p>Số điện thoại: ${formData.phone_number || ''}</p>
                <p>Địa chỉ giao hàng: ${fullAddress}</p>
                <p>Phương thức vận chuyển: ${shippingName}</p>
            `;

            const products = getFormData().map(item => {
                return {
                    productId: item.productId,
                    qty: item.qty,
                    variant_id: item.variant_id
                };
            });

            const response = await $.ajax({
                url: '{{ route('orders.get-shipping-fee') }}',
                type: 'POST',
                data: {
                    shipping_method: selectedShipping.val(),
                    products
                },
                success: function(response) {
                    $('#shipping-method-fee').text(`${formatCurrency(response.shipping_fee)}`)
                },
                error: function(error) {
                    datgin.error(error.responseJSON.message);
                }
            })

            if (isApplyCoupon) {
                await applyCoupon(true)
            }

            $('.card-shipping').find('p').remove(); // Xoá thông tin cũ nếu có
            $('.card-shipping').append(shippingInfoHTML); // Thêm mới

            updateStepStatus(2, {
                step: 3,
                tabId: 'tab-review'
            });

            calculateOrderTotal()

            $('.step[data-step="2"] .step-number').html('&#10003;');
            $('.step[data-step="3"]').addClass('active');
        }

        // Sang bước 3
        $(document).on('click', '#btn-to-review-order', function() {
            getShippingInfo()
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
                $('#loading').show();

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
                        $('#loading').hide();
                    },
                    error: function() {
                        $('#loading').hide();
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

            $('.input_order_name').on('input', function() {
                toggleShippingButton()
            })

            function toggleShippingButton() {

                if (checkAllProductsSelected() && $('.input_order_name').val() != "") {
                    $('#btn-to-shipping, #top-main-button').prop('disabled', false).removeClass('ant-btn-default')
                        .addClass(
                            'ant-btn-primary');
                } else {
                    $('#btn-to-shipping, #top-main-button').prop('disabled', true).removeClass('ant-btn-primary')
                        .addClass(
                            'ant-btn-default');
                }
            }

            // Khi hover vào .design-tooltip, hiện tooltip content
            $(document).on('mouseenter', '.design-tooltip', function() {
                const width = $(this).attr('data-width');
                const height = $(this).attr('data-height');
                const dpi = $(this).attr('data-dpi');
                const format = $(this).attr('data-format');
                let content = '';

                if (width && height && dpi && format) {
                    content = `
                        <p class="mb-0"><strong>Width:</strong>  ${width} px</p>
                        <p class="mb-0"><strong>Height:</strong>  ${height} px</p>
                        <p class="mb-0"><strong>DPI:</strong>  ${dpi}</p>
                        <p class="mb-0"><strong>File format:</strong>  ${format}</p>
                    `;
                } else {
                    content = 'Chưa có dữ liệu';
                }

                // Đặt nội dung cho tooltip
                $(this).find('.design-tooltip-content').html(content).fadeIn(200);
            });

            // Khi rời khỏi, ẩn tooltip content
            $(document).on('mouseleave', '.design-tooltip', function() {
                $(this).find('.design-tooltip-content').fadeOut(200);
            });


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
                            'd-block';

                        const isVariant = product.type === 'variant';

                        return `
                            <div class="custom-form mb-3" data-id="${product.id}" data-time="${product.time}" data-type="${product.type}">
                                <div class="d-flex align-items-center mb-3 justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="${product.image}" alt="${product.name}" class="me-3" style="width: 60px; height: auto;">
                                        <div class="lh-1">
                                            <h2 class="product-title mb-0 fs-5 mb-2">${product.name}</h2>
                                            <span class="text-muted product-sku" id="sku_${product.id}_${product.time}">
                                                ${isVariant ? '' : 'SKU: ' + product.sku}
                                            </span>
                                        </div>

                                    </div>
                                    <div class="btn-action">
                                        ${isVariant ? `<button type="button" class="btn btn-outline-primary btn-sm btn-clone-product" data-id="${product.id}">Nhân bản</button>` : ''}
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-confirmed" data-id="${product.id}">Xóa</button>
                                    </div>
                                </div>
                                ${selectsHtml ? `<div class="row">${selectsHtml}</div>` : ''}
                                <div class="is-variant ${infoVariantClass}">
                                    <div class="d-flex justify-content-between align-items-stretch" id="info_variant_${product.id}" data-time="${product.time}">
                                        <div class="d-flex align-items-center w-100 gap-2">
                                            <div class="d-flex flex-column justify-content-center">
                                                <p class="mb-1 " style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                    Product price
                                                </p>
                                                <p class="variant-price text-primary fw-bold fs-6 mb-0" style="color: #091E42 !important" data-product-id="${product.id}" data-time="${product.time}">
                                                    $<span>${isVariant ? '' : product.price}</span>
                                                </p>
                                            </div>
                                            <div class="ms-3 d-flex flex-column justify-content-center">
                                                <p class="required mb-1" style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                    Quantity
                                                </p>
                                                <input class="step_product_input form-control" style="height: 48px;" min="1" value="1"/>
                                            </div>
                                            <div class="ms-3 d-flex flex-column justify-content-center">
                                                <p class="mb-1 " style="font-size: 14px; color: #42526E; font-weight: normal;">
                                                    Total price
                                                </p>
                                                <p class="text-primary fw-bold fs-6 mb-0 total-price" style="color: #091E42 !important" data-product-id="${product.id}" data-time="${product.time}">
                                                    $<span>${isVariant ? '' : product.price}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex gap-4 pb-4">
                                        <div class="model-upload" style="width: 11%;">
                                            <label class="form-label fw-bold d-block ">Mockup</label>
                                            <div class="image-container">
                                                <img class="img-thumbnail" id="show_model_${product.id}"
                                                    style="cursor: pointer;"
                                                    src="{{ showImage('') }}" alt=""
                                                    onclick="document.getElementById('model_${product.id}').click();">

                                                <!-- Icon thùng rác -->
                                                <div class="image-hover-actions" id="delete_icon_${product.id}">
                                                    <i class="fa-solid fa-eye" onclick="viewImage('show_model_${product.id}')"></i>
                                                    <i class="fa-solid fa-trash" onclick="removeImage('show_model_${product.id}', 'model_${product.id}', '{{ showImage('') }}')"></i>
                                                </div>
                                            </div>

                                            <input type="file" name="model" id="model_${product.id}" class="form-control d-none"
                                                accept="image/*" onchange="previewImage(event, 'show_model_${product.id}')">
                                        </div>
                                        <div class="design-upload position-relative" style="width: 11%;" id="design_form_${product.id}">
                                            <div class="design-tooltip position-absolute"
                                                id="design-tooltip-${product.id}"
                                                style="top: 31px; right: 5px; z-index: 10; cursor: pointer;">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <div class="design-tooltip-content"></div>
                                            </div>

                                            <label class="form-label fw-bold d-block required">Design photo</label>

                                            <div class="position-relative image-preview-wrapper" id="image_container_${product.id}">
                                                <button class="custom-btn btn open-mockup-modal w-100" data-bs-toggle="modal" data-bs-target="#mockupModal" data-product-id="${product.id}">
                                                    <img src="{{ showImage('') }}" class="img-fluid rounded" alt="" id="show_design_${product.id}">
                                                </button>

                                                <div class="image-hover-icons justify-content-center align-items-center position-absolute top-0 start-0 w-100 h-100" style="pointer-events: auto;">
                                                    <i class="bi bi-trash me-2 cursor-pointer remove-image" title="Xoá"></i>
                                                    <a href="{{ showImage('') }}" class="image-zoom-link ms-2 text-white" title="Phóng to">
                                                        <i class="bi bi-arrows-fullscreen cursor-pointer"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }).join('');
                $('#confirmed-products-wrapper').append(html);

                updateSubtotal()
                toggleShippingButton()
            }

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
                            datgin.error('Biến thể bạn chọn đã được sử dụng!');

                            $(`#sku_${productId}_${time}`).text('');

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
                            form.find('.is-variant.d-block').removeClass('d-block').addClass(
                                'd-none');

                            return;
                        }

                        // Nếu chưa trùng, tiến hành xử lý như bình thường
                        info_variant.attr('data-variant-id', response.variant_id);

                        // Cập nhật SKU sau khi fetch thành công
                        $(`#sku_${productId}_${time}`).text(response.sku);

                        // Cập nhật giá
                        $(`.variant-price[data-product-id="${productId}"][data-time="${time}"] span`)
                            .text(`${response.price}`);

                        const form = $(`.custom-form[data-id="${productId}"][data-time="${time}"]`);

                        // $(`#show_model_${variant_id}`)
                        form.find(`#show_model_${productId}, #show_design_${productId}`).attr('src',
                            '{{ showImage('') }}')
                        form.find(`.image-container`).removeClass('has-image')

                        form.find('.is-variant.d-none').removeClass('d-none').addClass('d-block');

                        $(`#design-tooltip-${productId}`).attr({
                            'data-width': response.design_width,
                            'data-height': response.design_height,
                            'data-dpi': response.design_ppi,
                            'data-format': response.design_format
                        });


                        const totalPriceEl = form.find(
                            `.total-price[data-product-id="${productId}"][data-time="${time}"] span`
                        );
                        totalPriceEl.text(`${response.price}`);

                        updateSubtotal();
                        toggleShippingButton()
                    },
                    error: function(xhr) {
                        datgin.error(xhr.responseJSON?.message || "Đã xảy ra lỗi.");

                        const info_variant = $(`#info_variant_${productId}[data-time="${time}"]`);
                        info_variant.removeAttr('data-variant-id');

                        $(`.product-attr-select[data-product-id="${productId}"][data-time="${time}"]`)
                            .each(function() {
                                $(this).val("").trigger("change");
                            });

                        // $(`.custom-form[data-id="${productId}"][data-time="${time}"] input`).val(1);

                        $(`#sku_${productId}_${time}`).text('');

                        const form = $(`.custom-form[data-id="${productId}"][data-time="${time}"]`);
                        form.find('.d-flex.align-items-stretch').removeClass('d-flex').addClass(
                            'd-none');

                        toggleShippingButton()
                    }
                });
            }

            function updateSubtotal() {
                // Update total price of all products
                let total = 0;
                $('.total-price span').each(function() {
                    // Remove currency symbol and commas, then parse as float
                    const value = parseFloat($(this).text().replace('$', '').replace(/,/g, ''));
                    if (!isNaN(value)) {
                        total += value;
                    }
                });

                $('.final-price').text(`${formatCurrency(total)}`);
            }

            $(document).on('blur', '.step_product_input', function() {
                const $input = $(this);
                const quantity = $input.val().trim();
                const productId = $input.closest('.custom-form').data('id');
                const time = $input.closest('.custom-form').data('time');
                const variantId = $(`#info_variant_${productId}[data-time="${time}"]`).data('variant-id');
                const price = $input.closest('.custom-form').find('.variant-price span').text();
                const totalPrice = $input.closest('.custom-form').find('.total-price span');

                // Lấy giá trị cũ từ data attribute
                const oldQuantity = $input.data('old-value') || 1;

                // Nếu giá trị không thay đổi thì không làm gì cả
                if (parseInt(quantity) === parseInt(oldQuantity)) {
                    return;
                }

                // Kiểm tra số lượng hợp lệ (phải là số nguyên dương)
                if (!quantity || isNaN(quantity) || parseInt(quantity) < 1) {
                    alert('Vui lòng nhập số lượng hợp lệ (>= 1)');
                    $input.focus();
                    $input.val(1);
                    updateSubtotal();
                    totalPrice.text(price);
                    return;
                }

                // Gửi Ajax kiểm tra tồn kho
                $.ajax({
                    url: '{{ route('orders.check-stock') }}',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            // Lưu giá trị mới vào data attribute
                            $input.data('old-value', quantity);

                            $(`.total-price[data-product-id="${productId}"][data-time="${time}"] span`)
                                .text(`${response.totalPrice}`);

                            updateSubtotal();
                            toggleShippingButton();
                        }
                    },
                    error: function(xhr) {
                        datgin.error(xhr.responseJSON?.message ||
                            "Đã có lỗi xảy ra khi kiểm tra tồn kho!");

                        $input.val(oldQuantity);
                        totalPrice.text(price);
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
                toggleShippingButton()
            });


            function fetchConfirmedProductDetails(confirmedOrders, callback) {

                $.ajax({
                    url: '{{ route('orders.get.products') }}',
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
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        hr {
            border-top: 1px solid #dcdee1;
        }

        /* Tooltip icon */
        .design-tooltip {
            top: 5px;
            right: 5px;
            z-index: 10;
            cursor: pointer;
            color: #007bff;
            font-size: 16px;
        }

        .arrow-up {
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #ffffff;
            z-index: 101;
            border-color: transparent transparent #dcdcdc transparent;
        }

        /* Tooltip content */
        .design-tooltip-content {
            display: none;
            position: absolute;
            top: -100px;
            left: -60px;
            background-color: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            padding: 8px;
            z-index: 100;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            color: #333;
            line-height: 1.4;

            /* Thêm các dòng này */
            width: auto;
            max-width: 250px;
            /* Bạn có thể thay đổi giá trị này tùy ý */
            white-space: nowrap;
            /* Không xuống dòng */
            overflow-wrap: break-word;
            /* Nếu quá dài sẽ xuống dòng */
        }


        /* Mũi tên nhỏ ở tooltip */
        .design-tooltip-content::before {
            content: "";
            position: absolute;
            bottom: -6px;
            /* Đặt phía dưới của tooltip */
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #ffffff;
            /* Tạo viền mỏng */
            z-index: 101;
        }

        .image-hover-container {
            position: relative;
        }

        .image-hover-container img {
            transition: opacity 0.2s;
        }

        .image-hover-container:hover img {
            opacity: 0.7;
        }

        .image-hover-actions {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            cursor: pointer;
            color: #ff4d4f;
            font-size: 24px;
            display: none;
            background-color: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
            justify-content: center;
            align-items: center;
            gap: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .image-hover-actions i {
            font-size: 11px;
            color: white;
            cursor: pointer;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        /* Hiệu ứng hover */
        .image-hover-actions i:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        .image-container {
            position: relative;
            width: 100%;
            /* Chiều rộng cố định */
            height: 103px;
            /* Chiều cao cố định */
            border: 1px solid #ccc;
            display: flex;
            justify-content: center;
            /* Canh giữa theo chiều ngang */
            align-items: center;
            /* Canh giữa theo chiều dọc */
            overflow: hidden;
            background-color: #f9f9f9;
            border-radius: 6px;
        }

        .img-thumbnail {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* Giữ nguyên tỷ lệ ảnh, không bị cắt */
        }

        .image-container.has-image:hover .image-hover-actions {
            display: flex;
            opacity: 1;
        }
    </style>
@endpush
