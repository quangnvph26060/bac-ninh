@extends('frontend.app')

@section('content')
    <div class="header_steps_create_order position-relative container">
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
                        @case('pending')
                            @if ($order->payment_status === 'pending' && $order->status === 'pending')
                                <button type="button" id="confirm-paymant"
                                    class="ant-btn ant-btn-{{ $wallet->balance <= 0 || $wallet->balance < $order->total ? 'default' : 'primary' }} h-auto d-flex align-items-center gap-1"
                                    @disabled($wallet->balance <= 0 || $wallet->balance < $order->total)>
                                    <span>${{ formatPrice($order->total) }}</span>
                                    <span class="bg-white rounded w-1 h-1"></span>
                                    <span>Pay Now</span>
                                </button>
                            @else
                                <button type="button"
                                    class="ant-btn rounded-pill ant-btn-warning h-auto d-flex align-items-center gap-1"
                                    id="btn-status">
                                    <span><i class="bi bi-clock me-1"></i>Pending</span>
                                </button>
                            @endif
                        @break

                        @case('processing')
                            <button type="button" class="ant-btn rounded-pill ant-btn-info h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-gear me-1"></i>Confirmed</span>
                            </button>
                        @break

                        @case('completed')
                            <button type="button"
                                class="ant-btn rounded-pill ant-btn-success h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-check-circle me-1"></i>Completed</span>
                            </button>
                        @break

                        @case('cancelled')
                            <button type="button"
                                class="ant-btn rounded-pill ant-btn-danger h-auto d-flex align-items-center gap-1">
                                <span><i class="bi bi-x-circle me-2"></i>Cancelled</span>
                            </button>
                        @break
                    @endswitch

                    <button class="ant-btn rounded-pill ant-btn-danger h-auto" data-bs-toggle="modal"
                        data-bs-target="#cancelOrder" id="btn-cansel-order"
                        style="{{ $order->status === 'pending' && $order->status !== 'cancelled' ? '' : 'display: none' }}">Cancel
                        Order</button>
                </div>

            </div>
        </div>
    </div>

    @php
        $valid = $order->status === 'pending';

        $steps = [
            'pending',
            'confirmed_pending_production',
            'in_production',
            'produced_awaiting_completion',
            'completed_waiting_for_shipment',
            'shipped',
        ];

        $statusIndex = array_search($order->status, $steps);
    @endphp

    <ul id="progressbar" class="my-5 container">
        @foreach ($steps as $index => $stepKey)
            <li id="step{{ $index + 1 }}" class="{{ $index <= $statusIndex ? 'active' : '' }}">
                <div class="icon-wrapper">
                    @switch($stepKey)
                        @case('pending')
                            <i class="bi bi-clock"></i>
                        @break

                        @case('confirmed_pending_production')
                            <i class="bi bi-check2-circle"></i>
                        @break

                        @case('in_production')
                            <i class="bi bi-gear"></i>
                        @break

                        @case('produced_awaiting_completion')
                            <i class="bi bi-eye"></i>
                        @break

                        @case('completed_waiting_for_shipment')
                            <i class="bi bi-box-seam"></i>
                        @break

                        @case('shipped')
                            <i class="bi bi-truck"></i>
                        @break
                    @endswitch
                </div>
                <strong>
                    @switch($stepKey)
                        @case('pending')
                            Pending
                        @break

                        @case('confirmed_pending_production')
                            Confirmed
                        @break

                        @case('in_production')
                            Production
                        @break

                        @case('produced_awaiting_completion')
                            Awaiting Check
                        @break

                        @case('completed_waiting_for_shipment')
                            Ready to Ship
                        @break

                        @case('shipped')
                            Shipped
                        @break

                        @case('cancelled')
                            Cancelled
                        @break
                    @endswitch
                </strong>
            </li>
        @endforeach
    </ul>

    <div class="container">
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
                                            <td style="width: 8%; text-align: center;"><img
                                                    src="{{ showImage($item->image) }}" alt="{{ $item->product_name }}">
                                            </td>
                                            <td style="width: 8%; text-align: center;">
                                                <div class="image-wrapper position-relative d-inline-block">
                                                    <img src="{{ showImage($item->model_image) }}" alt=""
                                                        class="img-fluid rounded image-preview model-image-{{ $item->id }}"
                                                        style="width: 100%; object-fit: cover;">

                                                    <div
                                                        class="image-overlay d-flex justify-content-center align-items-center gap-2">
                                                        @if ($valid)
                                                            <!-- Button đổi ảnh -->
                                                            <i class="fas fa-sync-alt icon-btn"
                                                                onclick="document.getElementById('modelFileInput-{{ $item->id }}').click()"></i>
                                                        @endif


                                                        <!-- Button xem ảnh -->
                                                        <i class="fas fa-eye icon-btn"
                                                            onclick="zoomImage('model-image-{{ $item->id }}')"></i>
                                                    </div>
                                                </div>

                                                <!-- Ẩn input file -->
                                                <input type="file" id="modelFileInput-{{ $item->id }}"
                                                    accept="image/*" class="d-none"
                                                    onchange="handleImageChange(event, 'model-image-{{ $item->id }}', '{{ $item->id }}', 'model_image')">
                                            </td>
                                            <td style="width: 8%; text-align: center;">
                                                <div class="image-wrapper position-relative d-inline-block">
                                                    <img src="{{ showImage($item->design_image) }}" alt=""
                                                        class="img-fluid rounded image-preview design-image-{{ $item->id }}"
                                                        style="width: 100%; object-fit: cover;">

                                                    <div
                                                        class="image-overlay d-flex justify-content-center align-items-center gap-2">
                                                        @if ($valid)
                                                            <!-- Button đổi ảnh -->
                                                            <i class="fas fa-sync-alt icon-btn"
                                                                onclick="document.getElementById('designFileInput-{{ $item->id }}').click()"></i>
                                                        @endif
                                                        <!-- Button xem ảnh -->
                                                        <i class="fas fa-eye icon-btn"
                                                            onclick="zoomImage('design-image-{{ $item->id }}')"></i>
                                                    </div>
                                                </div>

                                                <!-- Ẩn input file -->
                                                <input type="file" id="designFileInput-{{ $item->id }}"
                                                    accept="image/*" class="d-none"
                                                    onchange="handleImageChange(event, 'design-image-{{ $item->id }}', '{{ $item->id }}', 'design_image')">
                                            </td>
                                            <td style="width: 5%; text-align: center;">
                                                <small>x</small>{{ $item->quantity }}
                                            </td>
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
                                        <th scope="row" colspan="6" class="text-end">Tax :</th>
                                        <td> ${{ formatPrice($order->tax) }}</td>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title fw-bold">Shipping Information</h4>

                            @if ($valid)
                                <div id="toggle-info">
                                    <i class="bi bi-pencil-square cursor" id="btn-pencil-info"></i>
                                </div>
                            @endif
                        </div>
                        <hr class="mt-2 mb-3">
                        <div id="preview-info">
                            <h5 class="font-family-primary fw-semibold mb-2">{{ "$order->first_name $order->last_name" }}
                            </h5>
                            <p class="mb-2"><span class="fw-semibold me-2">Email:</span> {{ $order->email }}</p>
                            <p class="mb-2"><span class="fw-semibold me-2">Mobile:</span> {{ $order->phone_number }}
                            </p>
                            <p class="mb-2"><span class="fw-semibold me-2">Address:</span>
                                {{ $order->shipping_address }}</p>
                        </div>
                        <div id="edit-info" style="display: none">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name"
                                    value="{{ $order->first_name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name"
                                    value="{{ $order->last_name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="{{ $order->email }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number"
                                    value="{{ $order->phone_number }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="shipping_address" rows="2">{{ $order->shipping_address }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title fw-bold">Ticket</h4>
                            <button class="cursor" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                <i class="bi bi-plus-lg icon-custom"></i>
                            </button>

                        </div>
                        <div id="ticketList">
                            @foreach ($order->tickets ?? [] as $ticket)
                                <div class="border rounded p-2 {{ !$loop->last ? 'mb-2' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <a class="fw-bold btn-view-ticket" title="Xem chi tiết"
                                            data-id="{{ $ticket->id }}">{{ $ticket->code }}</a>
                                        @switch($ticket->status)
                                            @case('open')
                                                <span class="badge-soft badge-soft-primary">Open</span>
                                            @break

                                            @case('resolving')
                                                <span class="badge-soft badge-soft-warning">Resolving</span>
                                            @break

                                            @case('resolved')
                                                <span class="badge-soft badge-soft-success">Resolved</span>
                                            @break

                                            @case('closed')
                                                <span class="badge-soft badge-soft-secondary">Closed</span>
                                            @break

                                            @default
                                                <span class="badge-soft badge-soft-dark">Unknown</span>
                                        @endswitch
                                    </div>
                                    <p class="mt-1 text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="mt-1">{{ $ticket->subject->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-3 fw-bold">Note</h4>
                            @if ($valid)
                                <div id="toggle-note">
                                    <i class="bi bi-pencil-square cursor" id="btn-pencil-note"></i>
                                </div>
                            @endif
                        </div>

                        <span id="preview-note">{{ $order->note }}</span>
                        <textarea id="text-note" class="form-control" style="display: none" @disabled(!$valid)>{{ $order->note }}</textarea>
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

    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid rounded" style="max-height: 80vh;" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="myForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select">
                                <option value="">Select subject</option>
                                @foreach ($subjects as $id => $title)
                                    <option value="{{ $id }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ant-btn ant-btn-default px-3"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="ant-btn ant-btn-primary px-3">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="ticket-detail-content">
                <!-- Nội dung chi tiết sẽ được load bằng JS -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="closeTicketModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-close-ticket">
                @csrf
                <input type="hidden" name="ticket_id" id="close-ticket-id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Close ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="close_reason" class="form-label fw-bold">Reason <span
                                    class="text-danger">*</span></label>
                            <textarea name="reason" id="close_reason" class="form-control" rows="4" placeholder="Reason" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="ant-btn ant-btn-default px-3"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="ant-btn ant-btn-primary px-3">Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <script>
        $(function() {
            ClassicEditor
                .create(document.querySelector('#content'), {
                    // Không cần uploadUrl khi dùng base64
                })
                .then(editor => {
                    // Kích hoạt base64 upload
                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new Base64UploadAdapter(loader);
                    };

                    // Nếu cần lấy nội dung khi submit: lưu editor trong window
                    window.editorInstance = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        })

        $('#myForm').on('submit', function(e) {
            e.preventDefault();

            const content = window.editorInstance.getData();

            let formData = new FormData(this);

            formData.set('content', content);
            formData.set('order_id', "{{ $order->id }}");

            $.ajax({
                url: "/tickets",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    $('#createTicketModal').modal('hide');
                    datgin.success(response.message)
                    $('#myForm')[0].reset();
                    window.editorInstance.setData('');

                    const tickets = response.data.tickets;
                    loopTickets(tickets)
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message || 'something went wrong!')
                    $('#loadingOverlay').hide();
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            })
        })

        function loopTickets(tickets) {
            let html = '';

            tickets.forEach((ticket, index) => {
                const isLast = index === tickets.length - 1;

                // Badge theo status
                let badgeClass = 'badge-soft-dark';
                let statusLabel = 'Unknown';

                switch (ticket.status) {
                    case 'open':
                        badgeClass = 'badge-soft-primary';
                        statusLabel = 'Open';
                        break;
                    case 'resolving':
                        badgeClass = 'badge-soft-warning';
                        statusLabel = 'Resolving';
                        break;
                    case 'resolved':
                        badgeClass = 'badge-soft-success';
                        statusLabel = 'Resolved';
                        break;
                    case 'closed':
                        badgeClass = 'badge-soft-secondary';
                        statusLabel = 'Closed';
                        break;
                }

                html += `
                    <div class="border rounded p-2 ${!isLast ? 'mb-2' : ''}">
                        <div class="d-flex align-items-center gap-2">
                            <a class="fw-bold btn-view-ticket" title="Xem chi tiết" data-id="${ticket.id}">
                                ${ticket.code}
                            </a>
                            <span class="badge-soft ${badgeClass}">${statusLabel}</span>
                        </div>
                        <p class="mt-1 text-muted">${new Date(ticket.created_at).toLocaleString('vi-VN')}</p>
                        <p class="mt-1">${ticket.subject?.title || ''}</p>
                    </div>
                `;
            });

            $('#ticketList').html(html);
        }

        let lastOpenedTicketId = null;

        $(document).on('click', '.btn-view-ticket', function() {
            const ticketId = $(this).data('id');

            // Nếu ticket hiện tại trùng với lần trước → chỉ show modal
            if (ticketId === lastOpenedTicketId) {
                $('#ticketDetailModal').modal('show');
                return;
            }

            // Nếu khác ID → gọi AJAX để load nội dung mới
            $.ajax({
                url: `/tickets/${ticketId}`,
                method: 'GET',
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: function(response) {
                    $('#ticket-detail-content').html(response.html);
                    $('#ticketDetailModal').modal('show');

                    // Cập nhật ID ticket đã mở
                    lastOpenedTicketId = ticketId;
                },
                error: function() {
                    datgin.error('Không lấy được dữ liệu ticket');
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
        });

        $(document).on('click', '.reply', function() {
            const $container = $('#replyFormContainer');

            // Ẩn nút trả lời (hoặc có thể toggle tùy ý)
            $('.reply').hide();

            // Tránh thêm nhiều form
            if ($container.find('.reply-form-block').length > 0) return;

            // Append template
            const $template = $($('#replyFormTemplate').html());
            $container.append($template);

            // Khởi tạo CKEditor sau khi form đã append vào DOM
            ClassicEditor
                .create(document.querySelector('#replyEditor'), {})
                .then(editor => {
                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new Base64UploadAdapter(loader);
                    };

                    // Gắn global để submit hoặc debug nếu cần
                    window.editorInstance = editor;

                    editor.editing.view.focus();
                })
                .catch(error => {
                    console.error(error);
                });
        });

        $(document).on('submit', '#form-send', function(e) {
            e.preventDefault();

            const message = window.editorInstance.getData();

            let formData = new FormData(this);

            formData.set('message', message);

            $.ajax({
                url: "/tickets/send",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    const html = `
                        <div class="d-flex mb-4 justify-content-end">
                            <div class="d-flex flex-row-reverse align-items-start" style="max-width: 100%;">
                                <div class="ms-2" style="flex: 0 0 48px;">
                                    <img src="{{ showImage(auth('web')->user()->img_url) }}" class="rounded-circle border"
                                        style="width: 48px; height: 48px; object-fit: cover; object-position: center;" />
                                </div>
                                <div class="flex-grow-1">
                                    <strong class="d-block text-end">{{ auth('web')->user()->name }}</strong>
                                    <small class="text-muted d-block text-end">
                                        ${new Date().toLocaleString('vi-VN')}
                                    </small>
                                    <div class="border rounded p-2 message-content bg-light mt-1">
                                        ${message}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#messagesContainer').append(html);

                    $('#form-send')[0].reset();

                    window.editorInstance.setData('');

                    $('#form-send').closest('.reply-form-block').remove();

                    $('.reply').show();

                    datgin.success(response.message)

                    $('#messagesContainer').scrollTop($('#messagesContainer')[0].scrollHeight);
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message || 'something went wrong!')
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            })
        })

        $(document).on('click', '#close-form-reply', function() {
            $('#replyFormContainer').empty();

            $('.reply').show()
        })

        $(document).on('click', '#closed', function() {
            let ticketId = $(this).closest('[data-ticket-id]').data('ticket-id'); // bạn cần thêm attr này
            $('#close-ticket-id').val(ticketId);
            $('#close_reason').val('');
        });

        $(document).on('submit', '#form-close-ticket', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: '/tickets/close',
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#loadingOverlay').show();
                },
                success: function(response) {
                    datgin.success(response.message);
                    lastOpenedTicketId = null
                    $('#closeTicketModal').modal('hide');
                    $('#ticketDetailModal').modal('hide');

                    const tickets = response.data.tickets;
                    loopTickets(tickets)
                },
                error: function(xhr) {
                    datgin.error(xhr.responseJSON.message || 'Lỗi khi đóng ticket!');
                },
                complete: function() {
                    $('#loadingOverlay').hide();
                }
            });
        });

        function zoomImage(imageId) {
            const src = document.querySelector(`.${imageId}`).getAttribute("src");
            document.getElementById("modalImage").setAttribute("src", src);
            const modal = new bootstrap.Modal(document.getElementById("imageModal"));
            modal.show();
        }

        function handleImageChange(event, imageId, orderItemId, type) {
            const file = event.target.files[0];

            if (!file) return;

            // Lưu lại ảnh cũ để có thể khôi phục nếu có lỗi
            const oldImageSrc = document.querySelector(`.${imageId}`).src;

            // Tạo FormData để gửi file
            const formData = new FormData();
            formData.append('image', file);
            formData.append('order_item_id', orderItemId);
            formData.append('type', type);

            // Hiển thị loading
            const $image = document.querySelector(`.${imageId}`);
            $image.style.opacity = '0.5';

            // Gọi API để cập nhật ảnh
            $.ajax({
                url: '/orders/handle-change-image',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    datgin.success(response.message);
                    // Cập nhật ảnh với URL mới từ server
                    $image.src = response.data.image_url;
                    $image.style.opacity = '1';
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message || "Update image failed");
                    // Khôi phục lại ảnh cũ nếu có lỗi
                    $image.src = oldImageSrc;
                    $image.style.opacity = '1';
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
        }

        $(document).on('click', '#btn-pencil-note', function() {
            $('#preview-note').hide()
            $('#text-note').show()

            $('#toggle-note').html(
                "<i class='bi bi-x-square cursor me-2' id='btn-x-note'></i><i class='bi bi-floppy cursor' id='btn-save-note'></i>"
            )
        })

        $(document).on('click', '#btn-x-note', function() {
            $('#preview-note').show()
            $('#text-note').hide()

            $('#toggle-note').html(
                " <i class='bi bi-pencil-square cursor' id='btn-pencil-note'></i>"
            )
        })

        $(document).on('click', '#btn-save-note', function() {
            let note = $('#text-note').val();

            $.ajax({
                url: '/orders/handle-change-note',
                method: "POST",
                data: {
                    note,
                    orderId: "{{ $order->id }}"
                },
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    datgin.success(response.message);
                    $('#preview-note').text(note)

                    $('#preview-note').show()
                    $('#text-note').hide()
                    $('#toggle-note').html(
                        " <i class='bi bi-pencil-square cursor' id='btn-pencil-note'></i>"
                    )
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message || "update note failed")
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            })
        })

        $(document).on('click', '#btn-pencil-info', function() {
            $('#preview-info').hide()
            $('#edit-info').show()

            $('#toggle-info').html(
                "<i class='bi bi-x-square cursor me-2' id='btn-x-info'></i><i class='bi bi-floppy cursor' id='btn-save-info'></i>"
            )
        })

        $(document).on('click', '#btn-x-info', function() {
            $('#preview-info').show()
            $('#edit-info').hide()

            $('#toggle-info').html(
                "<i class='bi bi-pencil-square cursor' id='btn-pencil-info'></i>"
            )
        })

        $(document).on('click', '#btn-save-info', function() {
            let data = {
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val(),
                phone_number: $('#phone_number').val(),
                shipping_address: $('#shipping_address').val(),
                orderId: "{{ $order->id }}"
            }

            $.ajax({
                url: '/orders/handle-change-info',
                method: "POST",
                data: data,
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    datgin.success(response.message);

                    // Update preview info
                    $('#preview-info').html(`
                        <h5 class="font-family-primary fw-semibold mb-2">${data.first_name} ${data.last_name}</h5>
                        <p class="mb-2"><span class="fw-semibold me-2">Email:</span> ${data.email}</p>
                        <p class="mb-2"><span class="fw-semibold me-2">Mobile:</span> ${data.phone_number}</p>
                        <p class="mb-2"><span class="fw-semibold me-2">Address:</span> ${data.shipping_address}</p>
                    `)

                    $('#preview-info').show()
                    $('#edit-info').hide()
                    $('#toggle-info').html(
                        "<i class='bi bi-pencil-square cursor' id='btn-pencil-info'></i>"
                    )
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.message || "Update failed")
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            })
        })

        $('#confirm-paymant').on('click', function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "ant-btn ant-btn-primary px-3",
                    cancelButton: "ant-btn ant-btn-default px-3 me-2"
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
                            $('#loadingOverlay').show();
                        },
                        success: (response) => {

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
                            $('#loadingOverlay').hide();
                            $('#coupon-content').show();
                        }
                    })

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // swalWithBootstrapButtons.fire({
                    //     title: "Cancelled",
                    //     text: "Payment has been cancelled. Your order is still pending.",
                    //     icon: "error"
                    // });
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
                    $('#loadingOverlay').show();
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
                    $('#loadingOverlay').hide();
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
            width: 17%;
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

        .icon-custom {
            font-size: 1.5rem;
            /* làm icon to hơn */
            color: #f06022;
            /* màu cam theo mã bạn đưa */
        }
    </style>
@endpush
