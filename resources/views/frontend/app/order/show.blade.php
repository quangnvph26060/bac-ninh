@extends('frontend.app')

@section('content')
    <div class="header_steps_create_order position-relative ">
        <div class="header_step_order">
            <div class=" w-100 d-flex align-items-center gap-4 justify-content-between">
                <div class="d-flex gap-2">
                    <a href="#">
                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1429 12L4.85718 12" stroke="#42526E" stroke-width="2" stroke-miterlimit="10">
                            </path>
                            <path d="M9.85718 17L4.85718 12L9.85718 7" stroke="#42526E" stroke-width="2"
                                stroke-miterlimit="10" stroke-linecap="square"></path>
                        </svg>
                    </a>

                    <div>
                        <p class="text-default fs-5 fw-bold">Thông tin đơn # {{ $order->order_code }} |
                            {{ $order->order_name }}</p>
                        <p>{{ $order->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">

                    <div class="{{ $order->payment_status === 'completed' ? 'bg_paid' : 'bg_unpaid' }} status_btn_order">
                        <span class="px-2">{{ $order->payment_status === 'completed' ? 'Đã' : 'Chưa' }} thanh toán</span>
                    </div>

                    @php
                        $btnClass = '';
                        $btnText = '';
                        $btnIcon = '';
                    @endphp

                    @switch($order->status)
                        @case('draft')
                            @if ($order->payment_status === 'pending')
                                <button type="button" id="confirm-paymant"
                                    class="ant-btn ant-btn-{{ $wallet->balance <= 0 || $wallet->balance < $order->total ? 'default' : 'primary' }} py-2 px-4 h-auto d-flex align-items-center gap-1"
                                    @disabled($wallet->balance <= 0 || $wallet->balance < $order->total)>
                                    <span>${{ formatPrice($order->total) }}</span>
                                    <span class="bg-white rounded w-1 h-1"></span>
                                    <span>Thanh toán ngay</span>
                                </button>
                            @endif
                        @break

                        @case('pending')
                            <button type="button" class="ant-btn ant-btn-warning py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-clock me-1"></i>Chờ xử lý</span>
                            </button>
                        @break

                        @case('processing')
                            <button type="button" class="ant-btn ant-btn-info py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-gear me-1"></i>Đang xử lý</span>
                            </button>
                        @break

                        @case('completed')
                            <button type="button" class="ant-btn ant-btn-success py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                            </button>
                        @break

                        @case('cancelled')
                            <button type="button" class="ant-btn ant-btn-danger py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                            </button>
                        @break
                    @endswitch

                </div>

            </div>
        </div>
    </div>

    <div class="my-4 mx-auto processing" style="max-width: 700px;">
        <div class="d-flex justify-content-between progress-step text-center">
            <div class="step-done">
                <i class="bi bi-check-circle"></i>
                <div>Đã đặt hàng</div>
            </div>
            <div class="step-done">
                <i class="bi bi-check-circle"></i>
                <div>Đã xác nhận</div>
            </div>
            <div class="step-pending">
                <i class="bi bi-box"></i>
                <div>Đang đóng gói</div>
            </div>
            <div class="step-pending">
                <i class="bi bi-truck"></i>
                <div>Đang giao</div>
            </div>
            <div class="step-pending">
                <i class="bi bi-emoji-smile"></i>
                <div>Hoàn tất</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product name</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php($total = 0)
                                @foreach ($order->orderItems as $item)
                                    @php($total += $item->price * $item->quantity)
                                    <tr>
                                        <th scope="row">
                                            <p>{{ $item->product_name }}</p>
                                            <small class="fw-medium">
                                                {{ implode(' - ', $item->productVariant?->attributeValues->pluck('value')->toArray() ?? []) }}
                                            </small>
                                        </th>
                                        <td><img src="{{ showImage($item->image) }}" alt="{{ $item->product_name }}"
                                                width="32" height="32">
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ formatPrice($item->price) }}</td>
                                        <td>${{ formatPrice($item->price * $item->quantity) }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th scope="row" colspan="4" class="text-end">Sub Total :</th>
                                    <td>
                                        <div class="fw-bold">${{ formatPrice($total) }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="4" class="text-end">Shipping Charge :</th>
                                    <td>${{ formatPrice($order->shipping_fee) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="4" class="text-end">Discount :</th>
                                    <td>${{ formatPrice($order->discount) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="4" class="text-end">Total :</th>
                                    <td>
                                        <div class="fw-bold">${{ formatPrice($order->total) }}</div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Shipping Information</h4>

                    <h5 class="font-family-primary fw-semibold mb-2">{{ $order->full_name }}</h5>

                    <p class="mb-2"><span class="fw-semibold me-2">Email:</span> {{ $order->email }}</p>
                    <p class="mb-2"><span class="fw-semibold me-2">Mobile:</span> {{ $order->phone_number }}</p>
                    <p class="mb-2"><span class="fw-semibold me-2">Address:</span> {{ $order->shipping_address }}</p>
                    <p class="mb-0"><span class="fw-semibold me-2">Payment method:</span>
                        {{ $order->payment_method === 'bank_transfer' ? 'via wallet' : 'Chưa cập nhật...' }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="header-title mb-3">Note</h4>

                    <textarea class="form-control" disabled rows="4">{{ $order->note }}</textarea>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <script>
        $('#confirm-paymant').on('click', function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Xác nhận thanh toán?",
                text: "Thanh toán ngay để đơn hàng của bạn được xử lý và giao sớm nhất có thể.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Đồng ý",
                cancelButtonText: "Huỷ bỏ",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('orders.payment', '__order_code__') }}".replace(
                            '__order_code__', "{{ $order->order_code }}"),
                        method: "POST",
                        data: {
                            order_code: "{{ $order->order_code }}",
                        },
                        beforeSend: () => {
                            $('#coupon-content').hide();
                            $('#loading').show();
                        },
                        success: (response) => {
                            swalWithBootstrapButtons.fire({
                                title: "Đã thanh toán!",
                                text: "Đơn hàng của bạn đã được thanh toán thành công.",
                                icon: "success"
                            });

                            notyf.success(response.message);

                            $('.money__amount').text(`$${response.data.amount}`)

                            const $statusDiv = $('.status_btn_order');
                            $statusDiv.removeClass('bg_unpaid').addClass('bg_paid');
                            $statusDiv.find('span').text('Đã thanh toán');

                            $('#confirm-paymant')
                                .removeClass('ant-btn-primary')
                                .addClass('ant-btn-warning')
                                .html(
                                    '<i class="bi bi-check-circle me-1"></i> Chờ xử lý')
                            $('#confirm-paymant').off('click');
                        },
                        error: (xhr) => {
                            notyf.error(xhr.responseJSON.message);
                        },
                        complete: () => {
                            $('#loading').hide();
                            $('#coupon-content').show();
                        }
                    })

                    // Gọi API hoặc redirect ở đây nếu cần
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Đã huỷ",
                        text: "Bạn đã huỷ thanh toán. Đơn hàng vẫn chưa được xử lý.",
                        icon: "error"
                    });
                }
            });
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/sweetalert2.min.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        .swal2-cancel.btn.btn-danger {
            margin-right: 5px
        }

        .processing i {
            font-size: 2rem;
        }

        .processing .progress-step {
            position: relative;
        }

        .processing .progress-step::before {
            content: "";
            position: absolute;
            top: 32px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #dee2e6;
            z-index: 0;
        }

        .processing .step-done,
        .processing .step-pending {
            position: relative;
            z-index: 1;
            background-color: white;
            padding: 10px;
            min-width: 80px;
        }

        .processing .step-done i {
            color: #28a745;
        }

        .processing .step-pending i {
            color: #adb5bd;
        }

        .processing .step-done div,
        .processing .step-pending div {
            margin-top: 5px;
            font-size: 14px;
        }

        .header-title {
            font-size: 1rem;
            margin: 0 0 7px 0;
        }
    </style>
@endpush
