@extends('admin.layout.index')

@section('content')
    <div class="page-inner container container--max--xl">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'đơn hàng', 'url' => route('admin.orders.index')],
                    ['name' => "Thông tin đơn #$order->order_code"],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>

        <div class="sa-page-meta">
            <div class="sa-page-meta__body">
                <div class="sa-page-meta__list">
                    <div class="sa-page-meta__item">
                        {{ $order->created_at->format('F j, Y \a\t g:i a') }}
                    </div>
                    <div class="sa-page-meta__item">{{ $order->orderItems->count() }} items</div>
                    <div class="sa-page-meta__item">Total ${{ formatPrice($order->total) }}</div>
                    <div class="sa-page-meta__item d-flex align-items-center fs-6">
                        {{-- Payment Status --}}
                        @if ($order->payment_status == 'pending')
                            <span class="badge bg-secondary me-2" id="payment-status">Payment Pending</span>
                        @elseif($order->payment_status == 'completed')
                            <span class="badge bg-success me-2" id="payment-status">Paid</span>
                        @elseif($order->payment_status == 'refunded')
                            <span class="badge bg-danger me-2">Refunded</span>
                        @endif

                        {{-- Order Status --}}
                        @if ($order->status == 'pending')
                            <span class="badge bg-secondary" id="order-status">Pending</span>
                        @elseif($order->status == 'confirmed')
                            <span class="badge bg-primary" id="order-status">Confirmed</span>
                        @elseif($order->status == 'shipping')
                            <span class="badge bg-warning text-dark" id="order-status">Shipping</span>
                        @elseif($order->status == 'completed')
                            <span class="badge bg-success" id="order-status">Completed</span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mt-5">
                    <div class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                        <h2 class="mb-0 fs-exact-18 me-4 fw-bold">Items</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="sa-table">
                            <tbody>
                                @php $sum = 0 @endphp
                                @foreach ($order->orderItems as $item)
                                    @php $sum += $item->price * $item->quantity @endphp
                                    <tr>
                                        <td class="min-w-20x">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ showImage($item->image) }}" class="me-4" width="40"
                                                    height="40" alt="">
                                                <a href="#" class="text-reset">
                                                    <p class="mb-0 fw-bold">{{ $item->product_name }}</p>
                                                    <small>
                                                        {{ implode(' - ', $item->productVariant?->attributeValues->pluck('value')->toArray() ?? []) }}
                                                    </small>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="sa-price">
                                                <span class="sa-price__symbol">$</span><span
                                                    class="sa-price__integer">{{ formatPrice($item->price) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end"><small>x</small>{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            <div class="sa-price">
                                                <span class="sa-price__symbol">$</span><span
                                                    class="sa-price__integer">{{ formatPrice($item->price * $item->quantity) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody class="sa-table__group">
                                <tr>
                                    <td colspan="3">Subtotal</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatPrice($sum) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Shipping</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span
                                                class="sa-price__integer">${{ formatNumber($order->shipping_fee) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Discount
                                    </td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">-</span>
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatNumber($order->discount) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatNumber($order->total) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a href="{{ route('admin.orders.invoice.print', $order->id) }}" target="_blank"
                                class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-print me-1"></i> Print Invoice
                            </a>

                            <a href="#" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>

                            @if ($order->status === 'pending')
                                <button id="btn-cansel-order" data-bs-toggle="modal" data-bs-target="#cancelOrder"
                                    id="btn-cansel-order" class="btn btn-outline-warning btn-sm text-dark" type="submit">
                                    <i class="fas fa-times-circle me-1"></i> Cancel Order
                                </button>
                            @endif

                            {{-- @if ($order->payment_status === 'completed' && $order->status !== 'refunded')
                                <form action="#" method="POST" onsubmit="return confirm('Refund this order?');">
                                    @csrf
                                    @method('POST')
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                                        <i class="fas fa-undo-alt me-1"></i> Refund
                                    </button>
                                </form>
                            @endif --}}
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card mt-5">
                    <div class="card-body d-flex align-items-center pt-4">
                        <div class="sa-symbol sa-symbol--shape--circle sa-symbol--size--lg">
                            <img src="{{ showImage($order->user->img_url) }}" width="40" height="40"
                                alt="" />
                        </div>
                        <div class="ms-3 ps-2">
                            <div class="fs-exact-16 fw-bold ">{{ $order->user->name }}</div>
                            <div class="fs-exact-13 text-muted">
                                Valued customer since {{ $order->user->created_at->format('Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body d-flex align-items-center justify-content-between pb-0 pt-4">
                        <h2 class="fs-exact-18 mb-0 fw-bold">Contact person</h2>
                        {{-- <a href="#" class="fs-exact-14">Edit</a> --}}
                    </div>
                    <div class="card-body pt-4 fs-exact-16">
                        <div><strong>{{ $order->full_name }}</strong></div>
                        <div class="mt-1">
                            <a href="#"><strong>Email</strong>: {{ $order->email }}</a>
                        </div>
                        <div class="text-muted mt-1"><strong>Phone: </strong>{{ $order->phone_number }}</div>
                        <div class="text-muted mt-1"><strong>Address: </strong> {{ $order->shipping_address }}</div>
                        <div class="text-muted mt-1"><strong>Payment method:
                            </strong>{{ $order->payment_method === 'bank_transfer' ? 'Via wallet' : '' }}</div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        {{-- Select để đổi trạng thái đơn hàng --}}
                        <form action="#" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PUT')

                            @php
                                $statusOptions = [
                                    'pending' => 0,
                                    'confirmed' => 1,
                                    'shipping' => 2,
                                    'completed' => 3,
                                    'cancelled' => 4,
                                ];
                                $currentStatusIndex = $statusOptions[$order->status];
                            @endphp

                            <select name="status" class="form-select form-select-sm w-auto">
                                @foreach ($statusOptions as $status => $index)
                                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}
                                        {{ $index < $currentStatusIndex ? 'disabled' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-sm btn-primary" type="submit">
                                Update Status
                            </button>
                        </form>
                    </div>
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

    <script>
        async function printInvoice() {
            const {
                jsPDF
            } = window.jspdf;
            const element = document.getElementById("invoiceContent");

            html2canvas(element, {
                scale: 2,
                useCORS: true
            }).then(canvas => {
                const imgData = canvas.toDataURL("image/png");
                const pdf = new jsPDF("p", "mm", "a4");

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
                pdf.autoPrint();
                window.open(pdf.output("bloburl"), "_blank");
            });
        }

        $("#cancellation-form").on('submit', function(e) {
            e.preventDefault()

            let code = "{{ $order->order_code }}"
            let user_id = "{{ $order->user->id }}"

            $.ajax({
                url: "{{ route('admin.orders.cancel') }}",
                method: "POST",
                data: {
                    code,
                    user_id,
                    reason: $('#cancel_reason').val()
                },
                beforeSend: () => {
                    $('#coupon-content').hide();
                    $('#loading').show();
                },
                success: (response) => {
                    Notifications(response.message, "success");

                    $('#btn-cansel-order').hide()

                    $('#btn-status, #confirm-paymant')
                        .removeClass('ant-btn-warning')
                        .addClass('ant-btn-danger')
                        .html(
                            '<i class="bi bi-x-circle me-1"></i> Đã hủy')

                    $('.status_btn_order').removeClass('bg_paid').removeClass('bg_unpaid').addClass(
                        'bg_refunded').find('span').text('Đã hoàn tiền')

                    $(".money__amount.balance").text(`$${response.data.wallet}`)

                    // Cập nhật dropdown status
                    const $statusSelect = $('select[name="status"]');
                    $statusSelect.val('cancelled');

                    $statusSelect.find('option').each(function() {
                        const value = $(this).val();
                        if (value !== 'cancelled') {
                            $(this).prop('disabled', true);
                        }
                    });

                    $('#payment-status').removeClass().addClass(
                        'badge bg-danger me-2').text('Refunded')

                    $("#order-status").removeClass().addClass('badge bg-danger').text('Cancelled')

                    $('#cancelOrder').modal('hide');
                },
                error: (xhr) => {
                    Notifications(xhr.responseJSON.message, "danger");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        .sa-page-meta {
            border-bottom: 1px solid #2125291a;
            border-top: 1px solid #2125291a;
            font-size: 0.875rem;
            line-height: 1.25rem;
            padding: 0.5625rem 1rem;
        }

        .sa-page-meta__body {
            overflow: hidden;
        }

        .sa-page-meta__list {
            margin-left: -1.5625rem;
        }

        .sa-page-meta__list {
            display: flex;
            flex-wrap: wrap;
            margin-top: -0.25rem;
        }

        .sa-page-meta__item {
            margin-left: 1.5625rem;
        }

        .sa-page-meta__item {
            margin-top: 0.25rem;
            position: relative;
        }

        .sa-page-meta__item:before {
            left: -0.8125rem;
        }

        .sa-page-meta__item:before {
            background: #21252933;
            content: "";
            display: block;
            height: calc(100% - 0.375rem);
            position: absolute;
            top: 0.1875rem;
            width: 0.0625rem;
        }

        .badge-sa-success {
            background: #def2d0;
            color: #245900;
        }

        .badge-sa-warning {
            background: #f9f1c8;
            color: #5e4f00;
        }

        .container--max--xl {
            max-width: 1140px;
        }

        .fs-exact-18 {
            font-size: 1.125rem !important;
        }

        .fs-exact-14 {
            font-size: 0.875rem !important;
        }

        .fs-exact-16 {
            font-size: 1rem !important;
        }

        .sa-table {
            --sa-table--padding-x: 1.5rem;
            --sa-table__row--padding-x: 0.5rem;
            --sa-table__row--padding-y: 0.75rem;
            --sa-table__header-row--padding-y: 0.625rem;
            --sa-table__group-row--padding-y: 0.5rem;
            width: 100%;
        }

        .sa-table td:first-child,
        .sa-table th:first-child {
            padding-left: var(--sa-table--padding-x);
        }

        .sa-table td,
        .sa-table th {
            padding: var(--sa-table__row--padding-y) var(--sa-table__row--padding-x);
        }

        .min-w-20x {
            min-width: 20rem !important;
        }

        .sa-table td:last-child,
        .sa-table th:last-child,
        .sa-table td:first-child,
        .sa-table th:first-child {
            padding-right: var(--sa-table--padding-x);
        }

        .sa-table tbody tr>* {
            border-top: 1px solid #2125291a;
        }

        tbody tr:last-child td {
            border-bottom: 1px solid #dee2e6;
            /* hoặc màu sắc khác phù hợp */
        }

        .sa-table__group tr td {
            border: none
        }
    </style>
@endpush
