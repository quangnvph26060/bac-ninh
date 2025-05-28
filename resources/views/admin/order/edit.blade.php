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

        {{-- <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($order->barcode, 'C128') }}" alt="barcode" /> --}}

        <div class="sa-page-meta">
            <div class="sa-page-meta__body">
                <div class="sa-page-meta__list">
                    <div class="sa-page-meta__item">
                        {{-- {{ $order->created_at->format('F j, Y \a\t g:i a') }} --}}
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="sa-page-meta__item">{{ $order->orderItems->count() }} mặt hàng</div>
                    <div class="sa-page-meta__item">Tổng {{ formatPrice($order->total) }} USD</div>
                    <div class="sa-page-meta__item d-flex align-items-center fs-6">

                        {{-- Payment Status --}}
                        @if ($order->payment_status == 'pending')
                            <span class="badge bg-secondary me-2" id="payment-status">Chờ thanh toán</span>
                        @elseif($order->payment_status == 'completed')
                            <span class="badge bg-success me-2" id="payment-status">Đã thanh toán</span>
                        @elseif($order->payment_status == 'refunded')
                            <span class="badge bg-danger me-2">Đã hoàn tiền</span>
                        @endif

                        {{-- Order Status --}}
                        @if ($order->status == 'pending')
                            <span class="badge bg-warning" id="order-status">
                                <i class="fas fa-hourglass-half me-1"></i> Chờ xác nhận
                            </span>
                        @elseif($order->status == 'confirmed_pending_production')
                            <span class="badge bg-primary" id="order-status">
                                <i class="fas fa-check-circle me-1"></i> Đã xác nhận, chờ sản xuất
                            </span>
                        @elseif($order->status == 'in_production')
                            <span class="badge bg-info text-dark" id="order-status">
                                <i class="fas fa-industry me-1"></i> Đang sản xuất
                            </span>
                        @elseif($order->status == 'produced_awaiting_completion')
                            <span class="badge bg-secondary" id="order-status">
                                <i class="fas fa-box-open me-1"></i> Đã sản xuất xong, chờ hoàn thiện
                            </span>
                        @elseif($order->status == 'completed_waiting_for_shipment')
                            <span class="badge bg-dark" id="order-status">
                                <i class="fas fa-truck-loading me-1"></i> Đã hoàn thiện, chờ giao hàng
                            </span>
                        @elseif($order->status == 'shipped')
                            <span class="badge bg-success" id="order-status">
                                <i class="fas fa-truck me-1"></i> Đã giao hàng
                            </span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger" id="order-status">
                                <i class="fas fa-times-circle me-1"></i> Đã hủy
                            </span>
                        @endif


                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mt-5">
                    <div class="card-body px-5 py-4 d-flex align-items-center justify-content-between">
                        <h2 class="mb-0 fs-exact-18 me-4 fw-bold">Sản phẩm</h2>
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
                                        <td class="text-center">
                                            <img src="{{ showImage($item->model_image) }}" width="40" height="40"
                                                alt="">
                                        </td>
                                        <td class="text-center">
                                            <img src="{{ showImage($item->design_image) }}" width="40" height="40"
                                                alt="">
                                        </td>
                                        <td class="text-center">
                                            <div class="sa-price">
                                                <span class="sa-price__symbol">$</span><span
                                                    class="sa-price__integer">{{ formatPrice($item->price) }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center"><small>x</small>{{ $item->quantity }}</td>
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
                                    <td colspan="5" class="fw-semibold">Tổng phụ</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatPrice($sum) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="fw-semibold">Phí vận chuyển</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span
                                                class="sa-price__integer">${{ formatPrice($order->shipping_fee) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="fw-semibold">
                                        Giảm giá
                                    </td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">-</span>
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatPrice($order->discount) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="fw-semibold">
                                        Thuế
                                    </td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatPrice($order->tax) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="fw-semibold">Tổng cộng</td>
                                    <td class="text-end">
                                        <div class="sa-price">
                                            <span class="sa-price__symbol">$</span><span
                                                class="sa-price__integer">{{ formatPrice($order->total) }}</span>
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
                                <i class="fas fa-print me-1"></i> In hóa đơn
                            </a>

                            <a href="#" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> Xuất PDF
                            </a>
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
                                Khách hàng từ {{ $order->user->created_at->format('Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body d-flex align-items-center justify-content-between pb-0 pt-4">
                        <h2 class="fs-exact-18 mb-0 fw-bold">Thông Tin Liên Hệ</h2>
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
                    <div class="card-header">
                        <h2 class="fs-exact-18 mb-0 fw-bold">Tracking</h2>
                    </div>
                    <div class="card-body pt-4 fs-exact-16">
                        <div class="input-group-custom">
                            <input type="text" class="form-control input-custom" name="tracking" id="tracking"
                                value="{{ $order->tracking }}">
                            <button class="btn btn-primary btn-sm button-custom">Lưu</button>
                        </div>
                    </div>

                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Cập nhật trạng thái</h5>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <form id="status-form" method="POST" class="d-flex align-items-center gap-2 w-100">
                            @csrf
                            @php
                                $statusOptions = [
                                    'pending' => 0,
                                    'confirmed_pending_production' => 1,
                                    'in_production' => 2,
                                    'produced_awaiting_completion' => 3,
                                    'completed_waiting_for_shipment' => 4,
                                    'shipped' => 5,
                                    'cancelled' => 6,
                                ];

                                // Ánh xạ trạng thái sang tiếng Việt
                                $statusLabels = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed_pending_production' => 'Đã xác nhận, chờ sản xuất',
                                    'in_production' => 'Đang sản xuất',
                                    'produced_awaiting_completion' => 'Đã sản xuất xong, chờ hoàn thiện',
                                    'completed_waiting_for_shipment' => 'Đã hoàn thiện, chờ giao hàng',
                                    'shipped' => 'Đã giao hàng',
                                    'cancelled' => 'Hủy đơn',
                                ];

                                $currentStatusIndex = $statusOptions[$order->status] ?? -1;
                            @endphp

                            <select id="status-select" name="status" class="form-select form-select-sm w-100"
                                @disabled($order->status === 'cancelled')>
                                @foreach ($statusOptions as $status => $index)
                                    @php
                                        $disableCancelled = $status === 'cancelled' && $order->status !== 'pending';
                                    @endphp

                                    <option value="{{ $status }}" data-index="{{ $index }}"
                                        @selected($order->status == $status) @disabled($index < $currentStatusIndex || $disableCancelled)>
                                        {{ $statusLabels[$status] }}
                                    </option>
                                @endforeach

                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"
                            id="cancelModal">Bỏ qua</button>
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

        $('.button-custom').click(function() {
            let tracking = $('#tracking').val();
            let orderId = "{{ $order->id }}"

            $.ajax({
                url: "{{ route('admin.orders.change-tracking') }}",
                method: "POST",
                data: {
                    tracking,
                    orderId
                },
                beforeSend: () => {
                    $("#loadingSpinner").fadeIn();
                },
                success: (response) => {
                    datgin.success(response.message)
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message)
                    $('#tracking').val('{{ $order->tracking }}')
                },
                complete: () => {
                    $("#loadingSpinner").fadeOut();
                }

            })
        })

        $('#status-select').on('focus', function() {
            originalStatus = $(this).val();
        });

        $('#status-select').on('change', function() {

            if ($(this).val() === "cancelled") {
                $('#cancelOrder').modal('show')
            } else {
                let selectedStatus = $(this).val();

                Swal.fire({
                    title: "Bạn có chắc chắn muốn cập nhật trạng thái?",
                    text: "Hành động này sẽ không thể hoàn tác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Đồng ý, cập nhật!",
                    cancelButtonText: "Hủy",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Nếu người dùng đồng ý, submit form
                        $('#status-form').trigger('submit');
                        // Cập nhật lại trạng thái gốc
                        originalStatus = selectedStatus;
                    } else {
                        // Nếu người dùng hủy, quay lại trạng thái ban đầu
                        $('#status-select').val(originalStatus);
                    }
                });
            }
        });

        $('#cancelModal').click(function() {
            $('#status-select').val(originalStatus);
        })

        $('#status-form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = form.serialize();
            let url = "{{ route('admin.orders.update.status', $order->id) }}";

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function(response) {

                    let selectedStatus = $('#status-select').val();

                    // Danh sách trạng thái và HTML tương ứng
                    const statusMapping = {
                        'pending': `<span class="badge bg-warning" id="order-status">
                                    <i class="fas fa-hourglass-half me-1"></i> Chờ xác nhận
                                </span>`,
                        'confirmed_pending_production': `<span class="badge bg-primary" id="order-status">
                                    <i class="fas fa-check-circle me-1"></i> Đã xác nhận, chờ sản xuất
                                </span>`,
                        'in_production': `<span class="badge bg-info text-dark" id="order-status">
                                    <i class="fas fa-industry me-1"></i> Đang sản xuất
                                </span>`,
                        'produced_awaiting_completion': `<span class="badge bg-secondary" id="order-status">
                                    <i class="fas fa-box-open me-1"></i> Đã sản xuất xong, chờ hoàn thiện
                                </span>`,
                        'completed_waiting_for_shipment': `<span class="badge bg-dark" id="order-status">
                                    <i class="fas fa-truck-loading me-1"></i> Đã hoàn thiện, chờ giao hàng
                                </span>`,
                        'shipped': `<span class="badge bg-success" id="order-status">
                                    <i class="fas fa-truck me-1"></i> Đã giao hàng
                                </span>`,
                        'cancelled': `<span class="badge bg-danger" id="order-status">
                                    <i class="fas fa-times-circle me-1"></i> Đã hủy
                                </span>`
                    };

                    // Thay thế HTML của badge
                    $('#order-status').replaceWith(statusMapping[selectedStatus]);

                    // Lấy index hiện tại
                    const statusOptions = [
                        'pending',
                        'confirmed_pending_production',
                        'in_production',
                        'produced_awaiting_completion',
                        'completed_waiting_for_shipment',
                        'shipped',
                        'cancelled'
                    ];
                    const currentIndex = statusOptions.indexOf(selectedStatus);

                    // Vô hiệu hóa tất cả các trạng thái trước đó
                    $('#status-select option').each(function() {
                        const optionValue = $(this).val();
                        const optionIndex = statusOptions.indexOf(optionValue);

                        if (optionIndex < currentIndex) {
                            $(this).prop('disabled', true);
                        }
                    });

                    // Hiển thị thông báo thành công
                    Notifications(response.message, "success");
                },
                error: function(xhr) {
                    if (
                        xhr.status === 403 &&
                        xhr.getResponseHeader("Content-Type").includes("text/html")
                    ) {
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                        return;
                    }

                    $('#status-select').val(originalStatus);
                    Notifications(xhr.responseJSON.message, "danger");
                }
            });
        });

        $("#cancellation-form").on('submit', function(e) {
            e.preventDefault();

            let code = "{{ $order->order_code }}";
            let user_id = "{{ $order->user->id }}";

            $.ajax({
                url: "{{ route('admin.orders.cancel') }}",
                method: "POST",
                data: {
                    code,
                    user_id,
                    reason: $('#cancel_reason').val()
                },
                beforeSend: () => {
                    $("#loadingSpinner").fadeIn();
                },
                success: (response) => {
                    Notifications(response.message, "success");

                    // Thay đổi trạng thái nút
                    $('#btn-status, #confirm-paymant')
                        .removeClass('ant-btn-warning')
                        .addClass('ant-btn-danger')
                        .html('<i class="bi bi-x-circle me-1"></i> Đã hủy');

                    // Thay đổi màu trạng thái thanh toán
                    $('.status_btn_order').removeClass('bg_paid')
                        .removeClass('bg_unpaid')
                        .addClass('bg_refunded')
                        .find('span').text('Đã hoàn tiền');

                    // Cập nhật số dư
                    $(".money__amount.balance").text(`$${response.data.wallet}`);

                    // Cập nhật dropdown status
                    const $statusSelect = $('select[name="status"]');

                    // Thêm option "Đã hủy đơn" nếu chưa tồn tại
                    if ($statusSelect.find('option[value="cancelled"]').length === 0) {
                        $statusSelect.append('<option value="cancelled">Đã hủy đơn</option>');
                    }

                    // Cập nhật trạng thái thanh toán
                    $('#payment-status').removeClass().addClass('badge bg-danger me-2').text(
                        'Đã hoàn tiền');

                    $('#status-select').prop('disabled', true);

                    // Cập nhật badge trạng thái đơn hàng
                    $("#order-status").replaceWith(`
                        <span class="badge bg-danger" id="order-status">
                            <i class="fas fa-times-circle me-1"></i> Đã hủy đơn
                        </span>
                    `);

                    // Đóng modal hủy đơn
                    $('#cancelOrder').modal('hide');
                },
                error: (xhr) => {
                    if (
                        xhr.status === 403 &&
                        xhr.getResponseHeader("Content-Type").includes("text/html")
                    ) {
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                        return;
                    }
                    Notifications(xhr.responseJSON.message, "danger");
                },
                complete: () => {
                    $("#loadingSpinner").fadeOut();
                }
            });
        });
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

        .input-custom {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
        }

        .button-custom {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .input-group-custom {
            display: flex;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .input-group-custom input:focus {
            box-shadow: none;
        }
    </style>
@endpush
