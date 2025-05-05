@extends('frontend.app')

@section('content')
    <div class="header_steps_create_order position-relative ">
        <div class="header_step_order">
            <div class=" w-100 d-flex align-items-center gap-4 justify-content-between">
                <div class="d-flex gap-2">
                    <a href="{{ route('orders.index') }}">
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
                    @php
                        switch ($order->payment_status) {
                            case 'completed':
                                $bgClass = 'bg_paid';
                                $text = 'Đã thanh toán';
                                break;
                            case 'refunded':
                                $bgClass = 'bg_refunded';
                                $text = 'Đã hoàn tiền';
                                break;
                            default:
                                $bgClass = 'bg_unpaid';
                                $text = 'Chưa thanh toán';
                                break;
                        }
                    @endphp

                    <div class="{{ $bgClass }} status_btn_order">
                        <span class="px-2">{{ $text }}</span>
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
                            <button type="button" class="ant-btn ant-btn-warning py-2 px-4 h-auto d-flex align-items-center gap-1"
                                id="btn-status">
                                <span><i class="bi bi-clock me-1"></i>Chờ xử lý</span>
                            </button>
                        @break

                        @case('processing')
                            <button type="button" class="ant-btn ant-btn-info py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-gear me-1"></i>Đã xác nhận</span>
                            </button>
                        @break

                        @case('completed')
                            <button type="button" class="ant-btn ant-btn-success py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                            </button>
                        @break

                        @case('cancelled')
                            <button type="button" class="ant-btn ant-btn-danger py-2 px-4 h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-x-circle me-2"></i>Đã hủy</span>
                            </button>
                        @break
                    @endswitch

                    <button class="ant-btn ant-btn-danger py-2 px-4 h-auto" data-bs-toggle="modal"
                        data-bs-target="#cancelOrder" id="btn-cansel-order"
                        style="{{ $order->status === 'pending' && $order->status !== 'cancelled' ? '' : 'display: none' }}">Hủy
                        đơn</button>
                </div>

            </div>
        </div>
    </div>

    @php
        $steps = [
            'pending' => 'Đã đặt hàng',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn tất',
        ];

        $statusOrder = array_keys($steps);
        $currentStatus = $order->status;

        // Nếu status không hợp lệ, set về 'unknown' để xử lý riêng
        $validStatus = in_array($currentStatus, $statusOrder);
    @endphp

    <div class="my-4 mx-auto processing" style="max-width: 700px;">
        <div class="d-flex justify-content-between progress-step text-center">
            @foreach ($steps as $status => $label)
                @php
                    $iconMap = [
                        'pending' => 'bi-check-circle',
                        'confirmed' => 'bi-clock',
                        'shipping' => 'bi-truck',
                        'completed' => 'bi-emoji-smile',
                    ];
                    $icon = $iconMap[$status];

                    if (!$validStatus || $currentStatus === 'cancelled') {
                        $stepClass = 'step-pending';
                    } else {
                        $currentIndex = array_search($currentStatus, $statusOrder);
                        $stepIndex = array_search($status, $statusOrder);
                        $stepClass = $stepIndex <= $currentIndex ? 'step-done' : 'step-pending';
                    }
                @endphp

                <div class="{{ $stepClass }}">
                    <i class="bi {{ $icon }}"></i>
                    <div>{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="row">
        <div class="col-lg-9">
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
                                    <td>- ${{ formatPrice($order->discount) }}</td>
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

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Shipping Information</h4>
                    <hr class="mt-2 mb-3">
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

    <div class="modal fade" id="cancelOrder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="cancellation-form">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="titleCancelOrder">Lý do hủy đơn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="4"
                                placeholder="Vui lòng nhập lý do..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Bỏ qua</button>
                        <button type="submit" class="btn btn-primary btn-sm">Xác nhận hủy</button>
                    </div>
                </form>
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

                            $('.money__amount.balance').text(`$${response.data.amount}`)

                            const $statusDiv = $('.status_btn_order');
                            $statusDiv.removeClass('bg_unpaid').addClass('bg_paid');
                            $statusDiv.find('span').text('Đã thanh toán');

                            $('#confirm-paymant')
                                .removeClass('ant-btn-primary')
                                .addClass('ant-btn-warning')
                                .html(
                                    '<i class="bi bi-check-circle me-1"></i> Chờ xử lý')
                            $('#confirm-paymant').off('click');

                            $('.progress-step > div').eq(0).removeClass().addClass('step-done')
                            $('#btn-cansel-order').show()
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

        $("#cancellation-form").on('submit', function(e) {
            e.preventDefault()

            let code = "{{ $order->order_code }}"

            $.ajax({
                url: "{{ route('orders.cancel', '__code__') }}".replace('__code__', code),
                method: "POST",
                data: {
                    code,
                    reason: $('#cancel_reason').val()
                },
                beforeSend: () => {
                    $('#coupon-content').hide();
                    $('#loading').show();
                },
                success: (response) => {
                    notyf.success(response.message);

                    $('#btn-cansel-order').hide()

                    $('#btn-status, #confirm-paymant')
                        .removeClass('ant-btn-warning')
                        .addClass('ant-btn-danger')
                        .html(
                            '<i class="bi bi-x-circle me-1"></i> Đã hủy')

                    $('.status_btn_order').removeClass('bg_paid').removeClass('bg_unpaid').addClass(
                        'bg_refunded').find('span').text('Đã hoàn tiền')

                    $(".money__amount.balance").text(`$${response.data.wallet}`)

                    $('#cancelOrder').modal('hide');
                },
                error: (xhr) => {
                    notyf.error(xhr.responseJSON.message);
                },
                complete: () => {
                    $('#loading').hide();
                    $('#coupon-content').show();
                }
            })

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
