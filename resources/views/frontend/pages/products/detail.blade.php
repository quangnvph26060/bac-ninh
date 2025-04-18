@extends('frontend.master')

@section('content')
    <div class="pw_product_wrapper">
        <div class="breadcrumb_wrapper">
            <div class="d-flex align-items-center mt-2 mb-3">
                <div>
                    @php
                        $items = [
                            ['label' => $category->name, 'url' => route('products.list', $category->slug)],
                            ['label' => $product->name],
                        ];
                    @endphp

                    @include('frontend.includes.breadcrumb', ['items' => $items])
                </div>
            </div>
        </div>

        @php
            $images = array_merge([$product->image], $product->images->pluck('image')->toArray());
        @endphp

        <div class="detail_product mb-5 mt-3">
            <div class="d-flex justify-content-between gap-4">
                <div class="image_product">
                    <div class="swiper swiper-thumbnail">
                        <div class="swiper-wrapper">

                            @foreach ($images as $item)
                                <div class="swiper-slide">
                                    <img src="{{ showImage($item) }}" alt="{{ $item }}" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="swiper swiper-main">
                        <div class="swiper-wrapper">
                            @foreach ($images as $item)
                                <div class="swiper-slide">
                                    <img src="{{ showImage($item) }}" alt="{{ $item }}" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="info_product">
                    <div class="w-100">
                        <div class="mb-4">
                            <h1 class="fs-4 fw-bold text-dark product_name">
                                {{ $product->name }}
                            </h1>
                        </div>

                        @if (count($attributes) > 0)
                            @foreach ($attributes as $attribute)
                                <div class="mb-4">
                                    <p class="fw-bold text-dark text-base">{{ $attribute['name'] }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        @foreach ($attribute['values'] as $valueId => $value)
                                            <div class="form-check">
                                                <input class="form-check-input attribute-radio" type="radio"
                                                    name="attribute_{{ $loop->parent->index }}"
                                                    id="{{ $valueId }}-{{ $value }}"
                                                    value="{{ $valueId }}" />
                                                <label class="form-check-label"
                                                    for="{{ $valueId }}-{{ $value }}">{{ $value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="mb-4 d-flex align-items-center">
                            <label for="quantity" class="fw-bold text-dark text-base me-5">Số lượng:</label>
                            <div class="input-group quantity-wrapper" style="width: 160px;">
                                <button class="btn btn-outline-secondary btn-quantity minus" type="button">−</button>
                                <input type="number" id="quantity" class="form-control text-center" value="1"
                                    min="1" max="100" />
                                <button class="btn btn-outline-secondary btn-quantity plus" type="button">+</button>
                            </div>
                        </div>


                        <div class="mb-4">
                            <p class="price-product pb-0">
                                @php
                                    $record = $product->variants->isNotEmpty() ? $product->variants->first() : $product;
                                @endphp

                                @if (isOnSale($record))
                                    <h2 class="text_color fs-4 mb-0 fw-bold d-inline">
                                        {{ finalPrice($record->discount_price) }}</h2>

                                    <small class="text-muted"> <del
                                            class="ms-2">{{ formatPrice($record->sale_price) }}</del></small>
                                @else
                                    <h2 class="text_color fs-2 mb-0 fw-bold">{{ formatPrice($record->sale_price) }}</h2>
                                @endif
                            </p>

                            {{-- <p class="text-success fw-medium">$4.49 with Diamond Tier</p> --}}
                        </div>

                        <div class="d-flex flex-column flex-md-row gap-3">
                            <button type="button" class="ant-btn-primary w-50 d-inline" id="btnOrderCreate">
                                Start new order
                            </button>

                            {{-- <a href="" class="w-100">
                                <button type="button" class="ant-btn-default text-dark w-100 d-inline">
                                    Mua ngay
                                </button>
                            </a> --}}
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="box_content">
            <div class="textDescription">
                {!! $product->description !!}
            </div>
        </div>

        <div class="_tab_custom">
            <ul class="nav nav-tabs border-bottom w-100" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description"
                        type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button"
                        role="tab">
                        Pricing
                    </button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping"
                        type="button" role="tab">
                        Shipping
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="guidelines-tab" data-bs-toggle="tab" data-bs-target="#guidelines"
                        type="button" role="tab">
                        File Guidelines
                    </button>
                </li> --}}
            </ul>

            <div class="tab-content mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="mt-5 _content_ck_editor">
                        {!! $product->content !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="pricing" role="tabpanel">
                    <p>Content for Pricing tab...</p>
                </div>
                <div class="tab-pane fade" id="shipping" role="tabpanel">
                    <p>Content for Shipping tab...</p>
                </div>
                <div class="tab-pane fade" id="guidelines" role="tabpanel">
                    <p>Content for File Guidelines tab...</p>
                </div>
            </div>
        </div>

        <div class="suggest_product_wrapper my-5">
            <div class="suggest_product_wrapper__inner p-0">
                <div class="d-flex justify-content-center">
                    <h2 class="title_suggest_prd">Bạn cũng có thể thích</h2>
                </div>
                <div class="list_new_arrival">
                    <div class="mx-auto mt-4 mt-md-8 p-xl-0" style="max-width: 1182px">
                        <div class="swiper-container">
                            <div class="swiper my_suggest_product">
                                <div class="swiper-wrapper">
                                    @if ($suggestedProducts->isNotEmpty())
                                        @foreach ($suggestedProducts as $sProduct)
                                            <div class="swiper-slide">
                                                <a href="#">
                                                    <div class="mockup_prd_wrapper">
                                                        <div
                                                            class="position-relative d-flex justify-content-center mockup_img">
                                                            <img loading="lazy" class="img_prd object-cover"
                                                                src="{{ showImage($sProduct->image) }}"
                                                                alt="{{ $sProduct->name }}"
                                                                style="border-radius: 4px 4px 0px 0px" />
                                                        </div>
                                                        <div class="content_prd_card"
                                                            style="border-radius: 0px 0px 4px 4px">
                                                            <h3 class="name_prd wrap_two_line_mb">
                                                                {{ $sProduct->name }}
                                                            </h3>

                                                            <p class="price-product pb-0">
                                                                @php
                                                                    $record = $product->variants->isNotEmpty()
                                                                        ? $product->variants->first()
                                                                        : $product;
                                                                @endphp

                                                                @if (isOnSale($record))
                                                                    <span
                                                                        class="text_color text-sm mb-2">{{ finalPrice($record->sale_price, $record->discount_price) }}</span>

                                                                    <small class="text-muted"> <del
                                                                            class="ms-2">{{ formatPrice($record->sale_price) }}</del></small>
                                                                @else
                                                                    <span
                                                                        class="text_color text-sm mb-2">{{ formatPrice($record->sale_price) }}</span>
                                                                @endif
                                                            </p>

                                                            @if ($sProduct->attributes->isNotEmpty())
                                                                <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                                                    @foreach ($sProduct->attributes as $attribute)
                                                                        @php
                                                                            $valueIds = $valueIds = json_decode(
                                                                                $attribute->pivot->attribute_values_ids,
                                                                                true,
                                                                            );
                                                                            $count = is_array($valueIds)
                                                                                ? count($valueIds)
                                                                                : 0;
                                                                        @endphp

                                                                        @if ($count > 0)
                                                                            <p class="title text-muted small">
                                                                                {{ $count }}
                                                                                {{ $attribute->name }}</p>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        // Create an instance of Notyf
        const notyf = new Notyf({
            duration: 5000,
            ripple: true,
            types: [{
                    type: 'success',
                    background: '#198754',
                    icon: {
                        className: 'bi bi-check-circle-fill',
                        tagName: 'i',
                        color: 'white'
                    }
                },
                {
                    type: 'error',
                    background: '#dc3545',
                    icon: {
                        className: 'bi bi-x-circle-fill',
                        tagName: 'i',
                        color: 'white'
                    }
                }
            ]
        });
        const PRODUCT_ID = {{ $product->id }};
        const ATTRIBUTES_COUNT = {{ count($attributes) }};
    </script>

    <script>
        $(document).ready(function() {
            $('.attribute-radio').on('change', function() {
                const selectedRadios = $('.attribute-radio:checked');
                $('#btnOrderCreate').prop('disabled', true)
                // Disable những radio không được chọn
                $('.attribute-radio').each(function() {
                    const input = $(this);
                    if (!input.prop('checked')) {
                        input.prop('disabled', true);
                    }
                });

                const selectedIds = selectedRadios.map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: '/select-attribute',
                    method: 'POST',
                    data: {
                        product_id: PRODUCT_ID,
                        value_ids: selectedIds
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.attribute-radio').prop('disabled', false);
                        $('#btnOrderCreate').prop('disabled', false)

                        $('.form-check').removeClass('disabled-option');

                        if (response.ids && Array.isArray(response.ids)) {
                            response.ids.forEach(function(id) {
                                const input = $(`.attribute-radio[value="${id}"]`);
                                if (!input.prop('checked')) {
                                    input.prop('disabled', true);
                                    input.closest('.form-check').addClass(
                                        'disabled-option');
                                }
                            });
                        }

                        // ✅ Nếu đã chọn đủ số lượng nhóm thuộc tính thì gửi request tìm biến thể
                        if (selectedRadios.length === ATTRIBUTES_COUNT) {
                            $.ajax({
                                url: '/find-variant',
                                method: 'POST',
                                data: {
                                    product_id: PRODUCT_ID,
                                    value_ids: selectedIds
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                success: function(variant) {
                                    // ✅ Ở đây bạn xử lý biến thể được tìm thấy
                                    console.log('Biến thể phù hợp:', variant);
                                    // Ví dụ: cập nhật giá, ảnh, tồn kho,...
                                },
                                error: function(xhr) {
                                    notyf.error(xhr.responseJSON?.message ||
                                        'Không tìm thấy biến thể phù hợp!');
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON?.message || 'Đã có lỗi xảy ra!');
                        $('.attribute-radio').prop('disabled', false);
                    }
                });
            });


            $('#btnOrderCreate').on('click', function() {
                const selectedAttributes = {};
                let allSelected = true;
                let qty = $('#quantity').val();

                // Duyệt từng nhóm thuộc tính (mỗi nhóm có name như attribute_0, attribute_1...)
                $('[name^="attribute_"]').each(function() {
                    const name = $(this).attr('name');

                    // Chỉ kiểm tra input đầu tiên có cùng name để tránh lặp
                    if (selectedAttributes[name] === undefined) {
                        const selected = $(`input[name="${name}"]:checked`);
                        if (selected.length > 0) {
                            selectedAttributes[name] = selected.val();
                        } else {
                            allSelected = false;
                        }
                    }
                });

                if (!allSelected) {
                    notyf.error(
                        "Vui lòng chọn đầy đủ các thuộc tính sản phẩm trước khi thêm vào giỏ hàng.");
                    return;
                }

                const orderItem = {
                    id: parseInt('{{ $product->id }}'),
                    image: '{{ config('app.url') }}/storage/{{ $product->image }}',
                    time: Date.now()
                };

                // Lấy confirmedOrders từ localStorage
                let existing = localStorage.getItem('confirmedOrders');
                let confirmedOrders = existing ? JSON.parse(existing) : [];

                // Thêm vào mảng
                confirmedOrders.push(orderItem);

                // Lưu lại vào localStorage
                localStorage.setItem('confirmedOrders', JSON.stringify(confirmedOrders));

                window.location.href = '{{ route('orders.create') }}'

                // Chuẩn bị dữ liệu gửi lên
                // const selectedValues = Object.values(selectedAttributes);

                // $.ajax({
                //     url: '{{ route('carts.add.to.cart') }}',
                //     method: 'POST',
                //     data: {
                //         productId: PRODUCT_ID,
                //         valueIds: selectedValues,
                //         qty
                //     },
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
                //     success: function(response) {
                //         notyf.success(response.message);
                //         $('.cart-icon span').text(response.count)
                //     },
                //     error: function(xhr) {
                //         notyf.error(xhr.responseJSON.message ||
                //             "Đã xảy ra lỗi khi thêm vào giỏ hàng.");
                //     }
                // });
            });

            $(document).on('click', '.btn-quantity', function() {
                const input = $('#quantity');
                let currentVal = parseInt(input.val());
                const min = parseInt(input.attr('min')) || 1;
                const max = parseInt(input.attr('max')) || 100;

                if ($(this).hasClass('plus') && currentVal < max) {
                    input.val(currentVal + 1);
                } else if ($(this).hasClass('minus') && currentVal > min) {
                    input.val(currentVal - 1);
                }
            });


        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endpush
