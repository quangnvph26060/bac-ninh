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
            <a class="btn deposit-btn text-white" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Nạp tiền
            </a>
        </div>

        <!-- Card Chưa thanh toán -->
        <div class="pending-card w-50">
            <div>
                <h6 class="mb-0">Chưa thanh toán</h6>
                <h5 class="pending-amount mb-0 fs-3">$0.00</h5>
            </div>
        </div>
    </div>

    <form action="" method="post" id="myForm">
        @csrf
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="exampleModalLabel">Nạp tiền qua Paypal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label required">Nhập số tiền</label>
                            <input type="text" placeholder="Nhập số tiền" name="amount" id="amount"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea placeholder="Ghi chú (tùy chọn)" name="note" id="note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="ant-btn ant-btn-primary px-2">Thực hiện</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        $(function() {
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
                        console.log(response);

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
