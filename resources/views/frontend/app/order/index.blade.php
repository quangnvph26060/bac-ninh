@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Đơn hàng</h1>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('orders.create') }}" id="headerOrder__createOrder_btn" class="ant-btn ant-btn-primary px-2 ">
                    Tạo đơn hàng <i class="bi bi-plus-circle ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="mb-2 d-flex flex-row justify-content-between">
        <div class="align-items-center d-flex flex-row position-relative order__filterBar__statusTabs" id="list_tab_orders">
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
            <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Chọn khoảng ngày" />
        </div>
    </form>

    <div class="table-responsive mt-4">
        <div id="order-content">
            @include('frontend.app.order.order-table', ['orders' => $orders])
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
                    fetchOrders(url);
                }
            });

            $(document).on('change', '.per-page-selector', function() {
                fetchOrders();
            });
        });

        // Gửi AJAX để lọc đơn hàng
        function fetchOrders(url = "{{ route('orders.index') }}", page = 1) {
            const status = $('.order-tab.active').data('status') || 'all';
            const search = $('input[name="search"]').val();
            const date_range = $('input[name="date_range"]').val();
            const per_page = $('.per-page-selector').val() || 10;

            $('#order-content').hide();
            $('#loading').show();

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    status,
                    search,
                    date_range,
                    per_page,
                    page
                },
                success: function(response) {
                    $('#order-content').html(response.html).fadeIn(200);
                    $('#loading').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi load đơn hàng:", xhr);
                    $('#loading').hide();
                    $('#order-content').show();
                }
            });
        }
    </script>
@endpush
