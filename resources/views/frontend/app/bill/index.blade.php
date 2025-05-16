@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Bill</h1>
        </div>
    </div>

    <div class="d-flex gap-3">
        <!-- Card Số dư ví -->
        <div class="wallet-card w-50 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('frontend/assets/img/balance.png') }}" alt="Wallet Icon" class="wallet-icon">
                <div>
                    <h6 class="mb-0">Wallet balance</h6>
                    <h5 class="mb-0 fw-bold fs-3">${{ formatPrice($wallet->balance) }}</h5>
                </div>
            </div>
            {{-- <a class="btn deposit-btn text-white" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Nạp tiền
            </a> --}}
            <button type="button" class="btn deposit-btn text-white" data-bs-toggle="modal" href="#modalTopupForm"
                role="button" onclick="resetForm()">
                Deposit money
            </button>
        </div>

        <!-- Card Chưa thanh toán -->
        <div class="pending-card w-50">
            <div>
                <h6 class="mb-0">Not yet paid</h6>
                <h5 class="pending-amount mb-0 fs-3">$0.00</h5>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <ul class="nav nav-pills mb-3" style="gap: 8px;">
            <li class="nav-item">
                <button data-tab="transaction-history" class="nav-link custom-tab active">Transaction history</button>
            </li>
            {{-- <li class="nav-item">
                <button class="nav-link custom-tab">Waiting for payment</button>
            </li> --}}
            <li class="nav-item">
                <button data-tab="request-deposit" class="nav-link custom-tab">Request deposit</button>
            </li>
        </ul>
    </div>

    <form id="order-filter-form" class="d-flex flex-wrap gap-3 mt-4">
        <!-- Ô tìm kiếm -->
        <div class="form-group position-relative">
            <label class="form-label fw-bold">Search</label>
            <div class="form-group input-icon-right">
                <input type="search" class="form-control" name="search" placeholder="Search by transaction code">
                <i class="bi bi-search"></i>
            </div>
        </div>

        <!-- Date range -->
        <div class="form-group">
            <label class="form-label fw-bold">Date</label>
            <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Select date range" />
        </div>
    </form>

    <div class="table-responsive mt-4">
        <div id="result">
        </div>
    </div>

    <div class="modal fade" id="modalTopupForm" aria-hidden="true" aria-labelledby="modalTopupFormLabel" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTopupFormLabel">Nạp tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" id="topupForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <label for="bank" class="form-label">Chọn ngân hàng</label>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($configPayments as $configPayment)
                                    <div class="swiper-slide {{ $loop->first ? 'active' : '' }} cursor"
                                        data-id="{{ $configPayment->id }}" data-content="{{ $configPayment->content }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ $configPayment->title }}">
                                        <div class="image-wrapper">
                                            <img src="{{ showImage($configPayment->image) }}"
                                                alt="{{ $configPayment->title }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="topup_modal_guide" style="margin-top: 1rem;">
                            <p>{!! $configPayments->first()->content !!}</p>
                        </div>

                        <hr class="my-3">


                        <div class="row g-3">
                            <!-- Cột trái -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Số tiền</label>
                                    <input type="text" name="amount" class="form-control usd-price-format"
                                        placeholder="Số tiền, USD" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bằng chứng giao dịch</label>
                                    <div onclick="document.getElementById('proofInput').click();"
                                        class="border rounded  upload-proof-wrapper"
                                        style="background: #fafbfc; border: 1px dashed #d9d9d9; min-height: 120px; padding: 10px 0;">
                                        <img id="previewImage" src="" alt="Preview"
                                            style="max-width: 100%; max-height: 100%; display: none; object-fit: contain;" />
                                        <div class="upload-proof-content flex-column align-items-center justify-content-center"
                                            style="display: flex">
                                            <div class="mb-2">
                                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_3697_7237)">
                                                        <path
                                                            d="M10.674 14.8782C12.2071 14.6297 13.2484 13.1854 12.9999 11.6523C12.7514 10.1192 11.3071 9.07784 9.77398 9.32637C8.24088 9.5749 7.19952 11.0192 7.44805 12.5523C7.69658 14.0854 9.14088 15.1268 10.674 14.8782Z"
                                                            fill="#DFE1E6"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M10.2274 14.9137C8.67413 14.9137 7.41488 13.6546 7.41488 12.1013C7.41488 10.5479 8.67403 9.28875 10.2274 9.28875C11.7807 9.28875 13.0399 10.5479 13.0399 12.1013C13.0399 13.6546 11.7807 14.9137 10.2274 14.9137ZM0.703125 32.5664L11.6336 19.8355L15.2699 24.0708L21.4718 16.2988L28.377 24.9521C29.972 23.2919 32.0836 22.1316 34.4532 21.7297V4.44141H0.703125V32.5664Z"
                                                            fill="white"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M27.0518 23.2917C29.0094 21.3387 31.5817 20.0021 34.4528 19.5957V21.7294C32.0831 22.1313 29.9716 23.2916 28.3765 24.9518L27.0518 23.2917Z"
                                                            fill="#DFE1E6"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M21.4715 16.2988L15.2695 24.0708L22.5638 32.5665H25.3121C25.3121 29.6108 26.4787 26.9275 28.3766 24.9521L21.4715 16.2988Z"
                                                            fill="#DFE1E6"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M23.2031 32.5666H25.3125C25.3125 29.6108 26.4791 26.9276 28.377 24.9522L27.0522 23.292C24.6746 25.664 23.2031 28.9438 23.2031 32.5666Z"
                                                            fill="#B3BAC5"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M15.2699 24.0712L11.6336 19.8359L0.703125 32.5668H22.5641L15.2699 24.0712Z"
                                                            fill="#DFE1E6"></path>
                                                        <path
                                                            d="M36.3047 43.5586C42.3755 43.5586 47.2969 38.6372 47.2969 32.5664C47.2969 26.4956 42.3755 21.5742 36.3047 21.5742C30.2339 21.5742 25.3125 26.4956 25.3125 32.5664C25.3125 38.6372 30.2339 43.5586 36.3047 43.5586Z"
                                                            fill="#F4F5F7"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M25.3125 32.5664C25.3125 39.0682 30.9109 44.122 37.3594 43.5079C31.7834 42.977 27.4219 38.2814 27.4219 32.5664C27.4219 26.8542 31.7817 22.1558 37.3594 21.6248C30.9113 21.0107 25.3125 26.0651 25.3125 32.5664Z"
                                                            fill="#DFE1E6"></path>
                                                        <path d="M33.75 15.4014H35.1562V16.8076H33.75V15.4014Z"
                                                            fill="#5E6C84">
                                                        </path>
                                                        <path
                                                            d="M1.40625 5.14453H33.75V13.9952H35.1562V3.73828H0V7.95694H1.40625V5.14453Z"
                                                            fill="#5E6C84"></path>
                                                        <path
                                                            d="M6.71191 12.1015C6.71191 14.04 8.28898 15.6171 10.2275 15.6171C12.166 15.6171 13.7431 14.04 13.7431 12.1015C13.7431 10.163 12.166 8.58594 10.2275 8.58594C8.28898 8.58594 6.71191 10.163 6.71191 12.1015ZM12.3368 12.1015C12.3368 13.2645 11.3906 14.2108 10.2275 14.2108C9.06448 14.2108 8.11816 13.2645 8.11816 12.1015C8.11816 10.9384 9.06448 9.99219 10.2275 9.99219C11.3906 9.99219 12.3368 10.9384 12.3368 12.1015Z"
                                                            fill="#5E6C84"></path>
                                                        <path d="M30.9375 6.55078H32.3438V7.95703H30.9375V6.55078Z"
                                                            fill="#5E6C84">
                                                        </path>
                                                        <path d="M28.125 6.55078H29.5312V7.95703H28.125V6.55078Z"
                                                            fill="#5E6C84">
                                                        </path>
                                                        <path
                                                            d="M35.6014 29.0865V34.932H37.0076V29.0865L39.4935 31.5724L40.4879 30.5779L36.3045 26.3945L32.1211 30.5779L33.1155 31.5724L35.6014 29.0865Z"
                                                            fill="#5E6C84"></path>
                                                        <path
                                                            d="M39.6389 37.041H32.9697V35.6348H31.5635V38.4473H41.0452V35.6348H39.6389V37.041Z"
                                                            fill="#5E6C84"></path>
                                                        <path
                                                            d="M36.3047 20.8711C35.9172 20.8711 35.5342 20.8905 35.1562 20.9275V18.2139H33.75V21.1529C31.7388 21.6028 29.9212 22.5716 28.4465 23.9118L21.4718 15.1715L15.2501 22.9683L11.6336 18.7561L1.40625 30.6681V9.36328H0V33.2695H24.6311C24.9961 39.3922 30.0921 44.2617 36.3047 44.2617C42.7535 44.2617 48 39.0152 48 32.5664C48 26.1176 42.7535 20.8711 36.3047 20.8711ZM21.4718 17.4261L27.4568 24.9263C25.8269 26.8112 24.7886 29.2206 24.631 31.8633H22.887L16.1824 24.0544L21.4718 17.4261ZM21.0337 31.8633H2.23359L11.6336 20.915L21.0337 31.8633ZM36.3047 42.8555C30.6312 42.8555 26.0156 38.2399 26.0156 32.5664C26.0156 26.8929 30.6312 22.2773 36.3047 22.2773C41.9782 22.2773 46.5938 26.8929 46.5938 32.5664C46.5938 38.2399 41.9782 42.8555 36.3047 42.8555Z"
                                                            fill="#5E6C84"></path>
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_3697_7237">
                                                            <rect width="48" height="48" fill="white"></rect>
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div class="mb-2 upload-hint">
                                                Nhấp hoặc thả tệp vào đây để tải lên bằng chứng của bạn
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="file" name="proof" class="form-control"
                                                    style="display:none;" id="proofInput"
                                                    accept="image/*,application/pdf">

                                                <button type="button" class="ant-btn ant-btn-default">Chọn Tệp
                                                    <svg width="20px" height="20px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 19.1429V4.85718" stroke="#8F9BB3" stroke-width="2"
                                                            stroke-miterlimit="10"></path>
                                                        <path d="M7 9.85718L12 4.85718L17 9.85718" stroke="#8F9BB3"
                                                            stroke-width="2" stroke-miterlimit="10"
                                                            stroke-linecap="square">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Cột phải -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Mã giao dịch</label>
                                    <input type="text" name="transaction_code" class="form-control"
                                        placeholder="Mã giao dịch" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" name="note" rows="6" placeholder="Ghi chú (Tùy chọn)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="ant-btn ant-btn-default px-2" data-bs-dismiss="modal">Hủy</button>
                        <button class="ant-btn ant-btn-primary px-2" id="confirmTopupBtn">Xác
                            nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="{{ asset('backend/assets/js/helper.js') }}"></script>
    <script>
        $(document).on('click', '.custom-tab', function() {
            $('.custom-tab').removeClass('active');
            $(this).addClass('active');
            fetchWalletTransaction();
        });

        function fetchWalletTransaction(url = "{{ route('bills.index') }}", page = 1) {
            const search = $('input[name="search"]').val();

            const urlWithParams = new URL(url, window.location.href); // URL gốc
            const searchParams = new URLSearchParams(urlWithParams
                .search); // Tạo đối tượng để truy xuất tham số query string
            const pageParam = searchParams.get('page') ||
                page; // Nếu có 'page' trong URL thì lấy, nếu không thì dùng giá trị mặc định

            const isTopupRequest = $('button[data-tab="request-deposit"]').hasClass('active');

            $.ajax({
                url: urlWithParams.pathname,
                method: 'GET',
                data: {
                    is_topup_request: isTopupRequest,
                    search: search,
                    page: pageParam, // Truyền 'page' vào data của AJAX
                    date_range: $('input[name="date_range"]').val()
                },
                beforeSend: () => {
                    $('#result').hide();
                    $('#loading').show();
                },
                success: function(response) {
                    $('#result').html(response.html).fadeIn(200);
                    $('#loading').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi lọc:", xhr);
                    notyf.error('Đã có lỗi xảy ra. Vui lòng thử lại sau!');
                },
                complete: () => {
                    $('#loading').hide();
                    $('#result').show();
                }
            });
        }

        function resetForm() {
            // Reset lại form
            $('form')[0].reset();

            // Ẩn ảnh preview
            $('#previewImage').hide().attr('src', '');

            // Hiển thị lại nội dung khi chưa upload
            $('.upload-proof-content').show();
        }
        $(function() {

            $('#date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Áp dụng',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#date-range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                fetchWalletTransaction();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchWalletTransaction();
            });

            // Gõ tìm kiếm (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchWalletTransaction();
                }, 500); // 500ms chờ sau khi ngừng gõ
            });

            // Phân trang
            $(document).on('click', '.page-url-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    fetchWalletTransaction(url);
                }
            });

            $(document).on('change', '.per-page-selector', function() {
                fetchWalletTransaction();
            });

            // Gửi AJAX để lọc đơn hàng


            fetchWalletTransaction()

            $('#topupForm').on('submit', function(e) {
                e.preventDefault();

                const amountInput = $('.usd-price-format');
                const cleanAmount = amountInput.val().replace(/,/g, "");
                amountInput.val(cleanAmount);

                const isTopupRequest = $('button[data-tab="request-deposit"]').hasClass('active');

                const configPaymentId = $('.swiper-slide.active').data('id');

                // Thực hiện submit form
                const formData = new FormData(this);
                formData.append('config_payment_id', configPaymentId);

                $.ajax({
                    url: '{{ route('bills.process') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        $('#loading').show();
                        $('#confirmTopupBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        notyf.success(response.message);
                        if (isTopupRequest) fetchWalletTransaction();
                        resetForm();
                        $('#modalTopupForm').modal('hide');
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON.message);
                    },
                    complete: () => {
                        $('#loading').hide();
                        $('#confirmTopupBtn').prop('disabled', false);
                    }
                });
            });

            // Khi chọn file
            $('#proofInput').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result).css({
                            display: 'block'
                        });
                        $('.upload-proof-content').hide();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.ant-btn[data-bs-dismiss="modal"]').on('click', function() {
                resetForm();
            });

            $('#myForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('bills.paypal.process') }}',
                    method: 'POST',
                    data: {
                        amount: $('#amount').val(),
                        note: $('#note').val(),
                    },
                    headers: {
                        'Accept': 'application/json' // 👉 ép Laravel hiểu đây là request AJAX
                    },
                    success: function(response) {

                        if (response.approval_url) {
                            window.location.href = response.approval_url
                        } else {
                            notyf.error('Đã có lỗi xảy ra, vui lòng thử lại sau!');
                        }
                    },
                    error: function(xhr) {
                        notyf.error(xhr.responseJSON.message);
                    }
                });
            })

            $('.swiper-slide').on('click', function() {
                var content = $(this).data('content');
                $('#modalTopupForm').find('.topup_modal_guide').html(content);

                $('.swiper-slide').removeClass('active');
                $(this).addClass('active');
            });

            var swiper = new Swiper('.swiper-container', {
                // loop: true,
                autoplay: {
                    delay: 2500, // Thời gian giữa các slide (ms)
                    disableOnInteraction: false, // Không tắt autoplay khi người dùng tương tác
                },
                slidesPerView: 6, // Hiển thị 5 ảnh trên màn hình lớn
                spaceBetween: 10, // Khoảng cách giữa các ảnh
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true, // Cho phép người dùng click vào pagination
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    320: { // Cho mobile, hiển thị 3 ảnh
                        slidesPerView: 3,
                    },
                    768: { // Cho tablet, hiển thị 4 ảnh
                        slidesPerView: 4,
                    },
                    1024: { // Cho desktop, hiển thị 5 ảnh
                        slidesPerView: 6,
                    }
                }
            });
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        .custom-tab {
            border-radius: 20px !important;
            background: #fff !important;
            color: #1a284d !important;
            border: 1px solid #e5eaf1 !important;
            font-weight: 500;
            padding: 6px 22px;
            transition: background 0.2s, color 0.2s;
        }

        .custom-tab.active,
        .custom-tab:active {
            background: #0a2240 !important;
            color: #fff !important;
            border: 1px solid #0a2240 !important;
        }

        .custom-tab:focus {
            box-shadow: none !important;
        }

        .image-wrapper {
            width: 100%;
            align-items: center;
            border: 1px solid #dfe1e6;
            display: flex;
            justify-content: center;
            max-height: 99px;
            padding: 16px;
        }

        .topup_modal_guide {
            background: #43bfe50d;
            border: 1px solid #43bfe5;
            border-radius: 8px;
            color: #000;
            padding: 16px;
        }

        .topup_modal_guide .title {
            color: #091e42;
            font-size: 16px;
            font-weight: 700;
            line-height: 24px;
        }

        .topup_modal_guide ul {
            list-style: disc;
        }

        .topup_modal_guide ul li {
            font-size: 16px;
            font-weight: 400;
            line-height: 24px;
            margin-top: 8px;
        }

        .cursor-copy {
            cursor: copy;
        }

        .text-F06022 {
            color: #F06022
        }

        .swiper-slide.active {
            border: 1px solid #ff5722
        }

        .swiper-container {
            overflow: hidden;
            /* Ẩn các ảnh ngoài vùng hiển thị */
        }

        .wallet-card,
        .pending-card {
            border-radius: 10px;
            padding: 40px 25px;
        }

        .wallet-card {
            border: 1px solid #00c4b4;
        }

        .pending-card {
            border: 1px solid #ff5722;
        }

        .wallet-icon {

            margin-right: 10px;
        }

        .deposit-btn {
            background-color: #00c4b4;
            border: none;
        }

        .deposit-btn:hover {
            background-color: rgb(0, 153, 140);

        }

        .pending-amount {
            color: #ff5722;
            font-weight: bold;
        }

        .upload-hint {
            color: #7a869a !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            line-height: 20px;
        }
    </style>
@endpush
