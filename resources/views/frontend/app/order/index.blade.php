@extends('frontend.app')

@section('content')
    <div class="order">
        <div class="order_container">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
                <h1 class="billing__title__content">Đơn hàng</h1>

                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('app.orders.create') }}" id="headerOrder__createOrder_btn" class="ant-btn ant-btn-primary px-2 ">
                        Tạo đơn hàng <i class="bi bi-plus-circle ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-2 d-flex flex-row justify-content-between">
            <div class="align-items-center d-flex flex-row position-relative order__filterBar__statusTabs"
                id="list_tab_orders">
                <div class="d-flex gap-2">
                    <div class="order-tab active" data-status="all">Tổng đơn hàng ({{ $orders->total() }})</div>
                    <div class="order-tab inactive" data-status="pending">Chờ xử lý ({{ $totalPendingOrders }})</div>
                </div>
            </div>
        </div>

        <form id="order-filter-form" class="d-flex flex-wrap gap-3 mt-4">
            <!-- Ô tìm kiếm -->
            <div class="form-group position-relative">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <div class="form-group input-icon-right">
                    <input type="search" class="form-control" name="search" placeholder="Tìm kiếm theo mã đơn hàng">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <!-- Date range -->
            <div class="form-group">
                <label class="form-label fw-bold">Ngày</label>
                <input type="text" id="date-range" name="date_range" class="form-control"
                    placeholder="Chọn khoảng ngày" />
            </div>
        </form>

        <div class="table-responsive mt-4">
            <div id="order-content">
                @include('frontend.components.order-table', ['orders' => $orders])
            </div>

            <div id="order-loading" style="display: none; text-align: center; padding: 50px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
@endpush


@push('scripts')
    <script>
        $(function() {
            // Khởi tạo date range picker
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
                fetchOrders();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchOrders();
            });

            // Click tab lọc theo trạng thái
            $(document).on('click', '.order-tab', function() {
                $('.order-tab').removeClass('active').addClass('inactive');
                $(this).addClass('active').removeClass('inactive');
                fetchOrders();
            });

            // Gõ tìm kiếm (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchOrders();
                }, 500); // 500ms chờ sau khi ngừng gõ
            });


            // Phân trang
            $(document).on('click', '.page-url-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    fetchOrdersByUrl(url);
                }
            });
        });

        // Gửi AJAX để lọc đơn hàng
        function fetchOrders(page = 1) {
            const status = $('.order-tab.active').data('status') || 'all';
            const search = $('input[name="search"]').val();
            const date_range = $('input[name="date_range"]').val();

            $('#order-content').hide(); // Ẩn content cũ
            $('#order-loading').show(); // Hiện loading

            $.ajax({
                url: "{{ route('orders.index') }}",
                method: 'GET',
                data: {
                    status: status,
                    search: search,
                    date_range: date_range,
                    page: page
                },
                success: function(response) {
                    $('#order-content').html(response.html).fadeIn(200);
                    $('#order-loading').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi lọc:", xhr);
                    $('#order-loading').hide();
                    $('#order-content').show();
                }
            });
        }


        function fetchOrdersByUrl(url) {
            const status = $('.order-tab.active').data('status') || 'all';
            const search = $('input[name="search"]').val();
            const date_range = $('input[name="date_range"]').val();

            $('#order-content').hide();
            $('#order-loading').show();

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    status: status,
                    search: search,
                    date_range: date_range
                },
                success: function(response) {
                    $('#order-content').html(response.html).fadeIn(200);
                    $('#order-loading').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi phân trang:", xhr);
                    $('#order-loading').hide();
                    $('#order-content').show();
                }
            });
        }
    </script>
@endpush
