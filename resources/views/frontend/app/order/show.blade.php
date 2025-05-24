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
                        <p class="text-default fs-5 fw-bold">Order Information # {{ $order->order_code }} |
                            {{ $order->order_name }}</p>
                        <p>{{ $order->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @php
                        switch ($order->payment_status) {
                            case 'completed':
                                $bgClass = 'bg_paid';
                                $text = 'Paid';
                                break;
                            case 'refunded':
                                $bgClass = 'bg_refunded';
                                $text = 'Refunded';
                                break;
                            default:
                                $bgClass = 'bg_unpaid';
                                $text = 'Unpaid';
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
                                    class="ant-btn ant-btn-{{ $wallet->balance <= 0 || $wallet->balance < $order->total ? 'default' : 'primary' }} h-auto d-flex align-items-center gap-1"
                                    @disabled($wallet->balance <= 0 || $wallet->balance < $order->total)>
                                    <span>${{ formatPrice($order->total) }}</span>
                                    <span class="bg-white rounded w-1 h-1"></span>
                                    <span>Pay Now</span>
                                </button>
                            @endif
                        @break

                        @case('pending')
                            <button type="button" class="ant-btn ant-btn-warning h-auto d-flex align-items-center gap-1"
                                id="btn-status">
                                <span><i class="bi bi-clock me-1"></i>Pending</span>
                            </button>
                        @break

                        @case('processing')
                            <button type="button" class="ant-btn ant-btn-info h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-gear me-1"></i>Confirmed</span>
                            </button>
                        @break

                        @case('completed')
                            <button type="button" class="ant-btn ant-btn-success h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-check-circle me-1"></i>Completed</span>
                            </button>
                        @break

                        @case('cancelled')
                            <button type="button" class="ant-btn ant-btn-danger h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-x-circle me-2"></i>Cancelled</span>
                            </button>
                        @break
                    @endswitch

                    <button class="ant-btn ant-btn-danger h-auto" data-bs-toggle="modal" data-bs-target="#cancelOrder"
                        id="btn-cansel-order"
                        style="{{ $order->status === 'pending' && $order->status !== 'cancelled' ? '' : 'display: none' }}">Cancel
                        Order</button>
                </div>

            </div>
        </div>
    </div>

    <ul id="progressbar" class="my-5">
        <li class="active" id="step1">
            <div class="icon-wrapper"><i class="bi bi-calendar"></i></div>
            <strong>Choose Date</strong>
        </li>
        <li id="step2">
            <div class="icon-wrapper"><i class="bi bi-tree"></i></div>
            <strong>Choose Campsite</strong>
        </li>
        <li id="step3">
            <div class="icon-wrapper"><i class="bi bi-truck-front"></i></div>
            <strong>Choose RV</strong>
        </li>
        <li id="step4">
            <div class="icon-wrapper"><i class="bi bi-geo-alt"></i></div>
            <strong>Booking Check</strong>
        </li>
        <li id="step5">
            <div class="icon-wrapper"><i class="bi bi-geo-alt"></i></div>
            <strong>Booking Check</strong>
        </li>
        <li id="step6">
            <div class="icon-wrapper"><i class="bi bi-geo-alt"></i></div>
            <strong>Booking Check</strong>
        </li>
    </ul>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product name</th>
                                    <th>Image</th>
                                    <th>Mockup</th>
                                    <th>Design</th>
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
                                        <td style="width: 5%; text-align: center;"><img src="{{ showImage($item->image) }}"
                                                alt="{{ $item->product_name }}" width="32" height="32">
                                        </td>
                                        <td style="width: 5%; text-align: center;"><img
                                                src="{{ showImage($item->model_image) }}" alt="{{ $item->product_name }}"
                                                width="32" height="32">
                                        </td>
                                        <td style="width: 5%; text-align: center;"><img
                                                src="{{ showImage($item->design_image) }}" alt="{{ $item->product_name }}"
                                                width="32" height="32">
                                        </td>
                                        <td><small>x</small>{{ $item->quantity }}</td>
                                        <td>${{ formatPrice($item->price) }}</td>
                                        <td>${{ formatPrice($item->price * $item->quantity) }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th scope="row" colspan="6" class="text-end">Sub Total :</th>
                                    <td>
                                        <div class="fw-bold">${{ formatPrice($total) }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6" class="text-end">Shipping Charge :</th>
                                    <td>${{ formatPrice($order->shipping_fee) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6" class="text-end">Discount :</th>
                                    <td>- ${{ formatPrice($order->discount) }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="6" class="text-end">Total :</th>
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
                    <h4 class="header-title fw-bold">Shipping Information</h4>
                    <hr class="mt-2 mb-3">
                    <h5 class="font-family-primary fw-semibold mb-2">{{ $order->full_name }}</h5>

                    <p class="mb-2"><span class="fw-semibold me-2">Email:</span> {{ $order->email }}</p>
                    <p class="mb-2"><span class="fw-semibold me-2">Mobile:</span> {{ $order->phone_number }}</p>
                    <p class="mb-2"><span class="fw-semibold me-2">Address:</span> {{ $order->shipping_address }}</p>
                    <p class="mb-0"><span class="fw-semibold me-2">Payment method:</span>
                        {{ $order->payment_method === 'bank_transfer' ? 'via wallet' : 'Not updated...' }}
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
                        <h5 class="modal-title fw-bold" id="titleCancelOrder">Cancellation Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="4"
                                placeholder="Please enter reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Confirm Cancellation</button>
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
                title: "Confirm Payment?",
                text: "Pay now to have your order processed and delivered as soon as possible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirm",
                cancelButtonText: "Cancel",
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
                                title: "Payment Successful!",
                                text: "Your order has been paid successfully.",
                                icon: "success"
                            });

                            datgin.success(response.message);

                            $('.money__amount.balance').text(`$${response.data.amount}`)

                            const $statusDiv = $('.status_btn_order');
                            $statusDiv.removeClass('bg_unpaid').addClass('bg_paid');
                            $statusDiv.find('span').text('Paid');

                            $('#confirm-paymant')
                                .removeClass('ant-btn-primary')
                                .addClass('ant-btn-warning')
                                .html(
                                    '<i class="bi bi-check-circle me-1"></i> Pending')
                            $('#confirm-paymant').off('click');

                            $('.progress-step > div').eq(0).removeClass().addClass('step-done')
                            $('#btn-cansel-order').show()
                        },
                        error: (xhr) => {
                            datgin.error(xhr.responseJSON.message);
                        },
                        complete: () => {
                            $('#loading').hide();
                            $('#coupon-content').show();
                        }
                    })

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Payment has been cancelled. Your order is still pending.",
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
                    datgin.success(response.message);

                    $('#btn-cansel-order').hide()

                    $('#btn-status, #confirm-paymant')
                        .removeClass('ant-btn-warning')
                        .addClass('ant-btn-danger')
                        .html(
                            '<i class="bi bi-x-circle me-1"></i> Cancelled')

                    $('.status_btn_order').removeClass('bg_paid').removeClass('bg_unpaid').addClass(
                        'bg_refunded').find('span').text('Refunded')

                    $(".money__amount.balance").text(`$${response.data.wallet}`)

                    $('#cancelOrder').modal('hide');
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message);
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
        .ant-btn {
            font-size: 11px !important;
        }

        .ant-btn {
            border-radius: 100px !important;
        }

        .swal2-cancel.btn.btn-danger {
            margin-right: 5px
        }

        #progressbar {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            padding: 0;
            position: relative;
        }

        #progressbar li {
            list-style-type: none;
            width: 25%;
            position: relative;
            text-align: center;
            font-weight: 400;
            color: #ccc;
            display: flex;
            flex-direction: column;
            align-items: center;
        }


        #progressbar li .icon-wrapper {
            width: 50px;
            height: 50px;
            line-height: 50px;
            background: #d8d8d8;
            border-radius: 50%;
            margin: 0 auto 10px;
            color: white;
            font-size: 22px;
            position: relative;
            z-index: 1;
        }

        #progressbar li::after {
            content: "";
            width: 100%;
            height: 2px;
            background: #d8d8d8;
            position: absolute;
            left: -50%;
            top: 25px;
        }

        #progressbar li:first-child::after {
            content: none;
        }

        #progressbar li.active .icon-wrapper {
            background: #2f8d46;
        }

        #progressbar li.completed .icon-wrapper {
            background: #d4f8d4;
            color: #34c759;
        }

        #progressbar li.active,
        #progressbar li.completed {
            color: #2f8d46;
        }

        #progressbar li.active::after,
        #progressbar li.completed::after {
            background: #2f8d46;
        }

        .header-title {
            font-size: 1rem;
            margin: 0 0 7px 0;
        }
    </style>
@endpush
