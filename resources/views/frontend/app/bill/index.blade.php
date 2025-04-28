@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Hóa đơn</h1>
        </div>
    </div>

    <div class="d-flex gap-3">
        <!-- Card Số dư ví -->
        <div class="wallet-card w-50 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('frontend/assets/img/balance.png') }}" alt="Wallet Icon" class="wallet-icon">
                <div>
                    <h6 class="mb-0">Số dư ví</h6>
                    <h5 class="mb-0 fw-bold fs-3">${{ formatPrice($wallet->balance) }}</h5>
                </div>
            </div>
            <button type="button" class="btn deposit-btn text-white" data-bs-toggle="modal" href="#modalTopupForm"
                role="button">
                Nạp tiền
            </button>
        </div>

        <!-- Card Chưa thanh toán -->
        <div class="pending-card w-50">
            <div>
                <h6 class="mb-0">Chưa thanh toán</h6>
                <h5 class="pending-amount mb-0  fs-3">$0.00</h5>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTopupForm" aria-hidden="true" aria-labelledby="modalTopupFormLabel" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTopupFormLabel">Nạp tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="bank" class="form-label">Chọn ngân hàng</label>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($configPayments as $configPayment)
                                <div data-payment="{{ $configPayment }}" data-bank="{{ $configPayment->bank }}"
                                    data-bin="{{ $configPayment->bank->bin }}"
                                    class="swiper-slide {{ $loop->first ? 'active' : '' }} cursor" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="{{ $configPayment->bank->shortName }}">
                                    <img src="{{ showImage('') }}" alt="{{ $configPayment->bank->name }}" class="img-fluid">
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="topup_modal_guide" style="margin-top: 1rem;"><span class="title">Để hoàn tất giao dịch mua
                            hàng của bạn, vui lòng nhập đúng thông tin dưới đây: </span>
                        <ul>
                            <li id="bank-details">
                                Bank Transfer:
                                <div class="text-F06022 d-flex align-items-center gap-3">
                                    <span id="account-number">Số tài khoản:
                                        {{ $configPayments->first()->account_number }}</span>
                                    <span class="cursor-copy align-items-center account-number-copy"
                                        data-clipboard-text="{{ $configPayments->first()->account_number }}">
                                        <span role="img" aria-label="copy" class="anticon anticon-copy"
                                            style="font-size: 16px; color: rgb(240, 96, 34); padding-bottom: 8px;">
                                            <svg viewBox="64 64 896 896" focusable="false" data-icon="copy" width="1em"
                                                height="1em" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M832 64H296c-4.4 0-8 3.6-8 8v56c0 4.4 3.6 8 8 8h496v688c0 4.4 3.6 8 8 8h56c4.4 0 8-3.6 8-8V96c0-17.7-14.3-32-32-32zM704 192H192c-17.7 0-32 14.3-32 32v530.7c0 8.5 3.4 16.6 9.4 22.6l173.3 173.3c2.2 2.2 4.7 4 7.4 5.5v1.9h4.2c3.5 1.3 7.2 2 11 2H704c17.7 0 32-14.3 32-32V224c0-17.7-14.3-32-32-32zM350 856.2L263.9 770H350v86.2zM664 888H414V746c0-22.1-17.9-40-40-40H232V264h432v624z">
                                                </path>
                                            </svg>
                                        </span>
                                    </span>
                                </div>
                                <div class="text-F06022 enjoyer">Chủ tài khoản: {{ $configPayments->first()->enjoyer }}
                                </div>
                                <div class="text-F06022 bank-name">Tên ngân hàng:
                                    {{ $configPayments->first()->bank->name }}</div>
                                <div class="text-F06022 d-flex align-items-center gap-3">
                                    <span class="transaction-content">Nội dung chuyển khoản:
                                        {{ generateTransactionCode() }}</span>
                                    <span class="cursor-copy align-items-center content-bank"
                                        data-clipboard-text="{{ generateTransactionCode() }}">
                                        <span role="img" aria-label="copy"
                                            class="anticon anticon-copy text-f06022 pb-2">
                                            <svg viewBox="64 64 896 896" focusable="false" data-icon="copy" width="1em"
                                                height="1em" fill="currentColor" aria-hidden="true">
                                                <path
                                                    d="M832 64H296c-4.4 0-8 3.6-8 8v56c0 4.4 3.6 8 8 8h496v688c0 4.4 3.6 8 8 8h56c4.4 0 8-3.6 8-8V96c0-17.7-14.3-32-32-32zM704 192H192c-17.7 0-32 14.3-32 32v530.7c0 8.5 3.4 16.6 9.4 22.6l173.3 173.3c2.2 2.2 4.7 4 7.4 5.5v1.9h4.2c3.5 1.3 7.2 2 11 2H704c17.7 0 32-14.3 32-32V224c0-17.7-14.3-32-32-32zM350 856.2L263.9 770H350v86.2zM664 888H414V746c0-22.1-17.9-40-40-40H232V264h432v624z">
                                                </path>
                                            </svg>
                                        </span>
                                    </span>
                                </div>
                            </li>

                            <li>Điền vào biểu mẫu dưới đây với các chi tiết gửi của bạn. Chúng tôi sẽ xử lý yêu cầu nạp tiền
                                của bạn sớm nhất có thể.</li>
                        </ul>
                    </div>

                    <hr class="my-3">

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="" class="form-label required">Số tiền</label>
                                <input type="text" name="amount" id="amount" class="form-control"
                                    placeholder="Số tiền">
                            </div>
                            <div class="col-lg-12">
                                <label for="" class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="note" id="note" rows="3" placeholder="Ghi chú"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="ant-btn ant-btn-primary px-2" id="confirmTopupBtn">Xác
                        nhận</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalQRCode" aria-hidden="true" aria-labelledby="modalQRCodeLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalQRCodeLabel">Mã Qr Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Bộ đếm thời gian -->
                    <p id="countdown" class="text-danger fw-bold"></p>

                    <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" />
                </div>
                <div class="modal-footer">
                    <button class="ant-btn ant-btn-primary px-2" id="confirmTransferBtn">Xác nhận đã chuyển khoản</button>
                    <button class="btn btn-outline-danger btn-sm" data-bs-target="#modalTopupForm" data-bs-toggle="modal"
                        data-bs-dismiss="modal">Quay lại</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        $(function() {
            $('#confirmTopupBtn').on('click', function() {
                var amount = $('#amount').val();
                var note = $('#note').val();
                var accountNumber = $('.account-number-copy').attr('data-clipboard-text');
                var bin = $('.swiper-slide.active').attr('data-bin');
                var contentBank = $('.content-bank').attr('data-clipboard-text');

                if (!amount.trim()) {
                    notyf.error('Vui lòng nhập số tiền muốn nạp!');
                    return false; // Ngừng hành động chuyển modal nếu không có số tiền
                }

                // Gửi yêu cầu đến API để lấy ảnh QR
                $.ajax({
                    url: '{{ route('bills.generate.qr') }}', // Địa chỉ API của bạn
                    method: 'POST',
                    data: {
                        amount,
                        note,
                        accountNumber,
                        bin,
                        contentBank
                        // Bạn có thể gửi thêm các dữ liệu khác cần thiết
                    },
                    success: function(response) {
                        $('#qrCodeImage').attr('src', response.data.qrUrl);
                        $('#modalQRCode').modal('show');
                        $('#modalTopupForm').modal('hide');

                        // Cài đặt thời gian countdown (5 phút)
                        var countdownTime = 5 * 60; // 5 phút tính bằng giây
                        var countdownElement = document.getElementById('countdown');
                        var interval; // Để có thể dừng lại sau khi giao dịch thành công

                        // Nếu có bộ đếm đang chạy, dừng nó
                        if (window.interval) {
                            clearInterval(window.interval);
                        }

                        // Cập nhật bộ đếm thời gian mỗi giây
                        window.interval = setInterval(function() {
                            var minutes = Math.floor(countdownTime / 60);
                            var seconds = countdownTime % 60;

                            // Hiển thị đếm ngược
                            countdownElement.textContent =
                                `Thời gian còn lại: ${minutes} phút ${seconds} giây`;

                            // Nếu hết thời gian, reload trang
                            if (countdownTime <= 0) {
                                clearInterval(window.interval); // Dừng bộ đếm
                                location.reload(); // Reload trang
                            }

                            countdownTime--;
                        }, 1000); // Cập nhật mỗi giây
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra khi gọi API.');
                    }
                });
            });

            $('#confirmTransferBtn').on('click', function() {

                var amount = $('#amount').val();
                var note = $('#note').val();
                var accountNumber = $('.account-number-copy').attr('data-clipboard-text');
                var bin = $('.swiper-slide.active').attr('data-bin');
                var contentBank = $('.content-bank').attr('data-clipboard-text');

                // Gửi yêu cầu API
                $.ajax({
                    url: '{{ route('bills.confirm.transfer') }}', // Địa chỉ API của bạn
                    type: 'POST',
                    data: {
                        amount,
                        note,
                        accountNumber,
                        bin,
                        contentBank
                        // Bạn có thể gửi thêm các dữ liệu khác cần thiết
                    },
                    success: function(response) {
                        $('#amount').val('');
                        $('#note').val('');
                        $('#modalQRCode').modal('hide');
                        $('.transaction-content').text(
                            "Nội dung chuyển khoản: " +
                            "{{ generateTransactionCode() }}");

                        $('.content-bank').attr("data-clipboard-text",
                            "{{ generateTransactionCode() }}");
                        clearInterval(window.interval); // Dừng bộ đếm khi giao dịch thành công
                        $('#countdown').text('Giao dịch thành công!'); // Thông báo thành công
                        notyf.success(response.message);
                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi trong quá trình gọi API
                        alert('Đã xảy ra lỗi khi gọi API: ' + error);
                    }
                });
            });


            $('.cursor-copy').on('click', function() {
                var textToCopy = $(this).attr('data-clipboard-text');

                navigator.clipboard.writeText(textToCopy).then(function() {
                    notyf.success(`Copied to Clipboard: ${textToCopy}`);
                }).catch(function(err) {
                    notyf.error('Failed to copy to clipboard');
                });
            });

            $('.swiper-slide').on('click', function() {

                var bankData = $(this).data('bank');
                var payment = $(this).data('payment');

                $('#account-number').text('Số tài khoản: ' + payment
                    .account_number).attr('data-account-number', payment
                    .account_number);

                $('.enjoyer').text('Chủ tài khoản: ' + payment
                    .enjoyer);

                $('.bank-name').text('Tên ngân hàng: ' + bankData
                    .name);

                $('.account-number-copy').attr('data-clipboard-text', payment
                    .account_number);

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
    </style>
@endpush
