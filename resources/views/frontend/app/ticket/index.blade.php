@extends('frontend.app')

@section('content')
    <div class="order">
        <div class="order_container">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
                <h1 class="billing__title__content">Tickets</h1>
                <button class="ant-btn ant-btn-default px-3 text-f06022 border-f06022" data-bs-toggle="modal"
                    data-bs-target="#createTicketModal">
                    Create ticket <i class="bi bi-plus-circle-dotted ms-2"></i>
                </button>

            </div>
        </div>

        <form id="order-filter-form" class="row flex-wrap mt-4">
            <!-- Ô tìm kiếm -->
            <div class="form-group position-relative col-5">
                <label class="form-label fw-bold">Search</label>
                <div class="form-group input-icon-right">
                    <input type="search" class="form-control" name="search" placeholder="Search by Ticket ID, Order ID">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <div class="form-group col-2">
                <label class="form-label fw-bold">Date</label>
                <input type="text" id="date-range" name="date_range" class="form-control"
                    placeholder="Select date range" />
            </div>

            <div class="form-group col-2">
                <label class="form-label fw-bold">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    @foreach ($subjects as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>

            </div>
        </form>

        <div class="d-flex gap-2 flex-wrap mt-4">
            <button class="filter-btn active" data-status="all">
                All ({{ $totalCount }})
            </button>
            <button class="filter-btn" data-status="open">
                Open ({{ $statusCounts['open'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="resolving">
                Resolving ({{ $statusCounts['resolving'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="resolved">
                Resolved ({{ $statusCounts['resolved'] ?? 0 }})
            </button>
            <button class="filter-btn" data-status="closed">
                Closed ({{ $statusCounts['closed'] ?? 0 }})
            </button>
        </div>


        <div class="table-responsive mt-4">
            <div id="ticket-content">
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
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select" required>
                                <option value="">Select subject</option>
                                @foreach ($subjects as $id => $title)
                                    <option value="{{ $id }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="order_id" class="form-label">Order</label>
                            <select name="order_id" id="order_id" class="form-select">
                                <option value="">Select order</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">{{ $order->order_code }} -
                                        {{ $order->order_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ant-btn ant-btn-default px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="ant-btn ant-btn-primary px-3">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/summernote/summernote-lite.min.js') }}"></script>
    <script>
        $(function() {

            $('#subject_id').select2({
                placeholder: "Chọn chủ thể",
                allowClear: true,
                width: '100%' // đảm bảo không bị vỡ layout
            });

            $('#date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#date-range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                fetchTicket();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchTicket();
            });

            // Gõ tìm kiếm (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchTicket();
                }, 500); // 500ms chờ sau khi ngừng gõ
            });

            $(document).on('click', '.filter-btn', function(e) {
                $('.filter-btn').removeClass('active')
                $(this).addClass('active')
                fetchTicket()
            });

            // Phân trang
            $(document).on('click', '.page-url-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    fetchTicket(url);
                }
            });

            $(document).on('change', '.per-page-selector', function() {
                fetchTicket();
            });
        });

        // Gửi AJAX để lọc đơn hàng
        function fetchTicket(url = "{{ route('tickets.index') }}", page = 1) {
            const search = $('input[name="search"]').val();
            const status = $('.filter-btn.active').data('status') || 'all'; // <-- thêm dòng này

            const urlWithParams = new URL(url, window.location.href);
            const searchParams = new URLSearchParams(urlWithParams.search);
            const pageParam = searchParams.get('page') || page;

            $.ajax({
                url: urlWithParams.pathname,
                method: 'GET',
                data: {
                    search: search,
                    status: status, // <-- thêm vào đây
                    page: pageParam
                },
                beforeSend: () => {
                    $('#ticket-content').hide();
                    $('#loading').show();
                },
                success: function(response) {
                    $('#ticket-content').html(response.html).fadeIn(200);
                    $('#loading').hide();
                },
                error: function(xhr) {
                    datgin.error('Đã có lỗi xảy ra. Vui lòng thử lại sau!');
                },
                complete: () => {
                    $('#loading').hide();
                    $('#ticket-content').show();
                }
            });
        }


        fetchTicket()
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/select2.min.css') }}">
@endpush
