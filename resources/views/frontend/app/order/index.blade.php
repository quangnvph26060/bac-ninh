@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Orders</h1>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('orders.create') }}" id="headerOrder__createOrder_btn" class="ant-btn ant-btn-primary px-2 ">
                    Create Order <i class="bi bi-plus-circle ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <form id="order-filter-form" class="d-flex flex-wrap gap-3 mt-4">
        <!-- Search box -->
        <div class="form-group position-relative">
            <label class="form-label fw-bold">Search</label>
            <div class="form-group input-icon-right">
                <input type="search" class="form-control" name="search" placeholder="Search by order code">
                <i class="bi bi-search"></i>
            </div>
        </div>

        <!-- Order Status -->
        <div class="form-group">
            <label class="form-label fw-bold">Order Status</label>
            <select class="form-select" name="status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Status -->
        <div class="form-group">
            <label class="form-label fw-bold">Payment Status</label>
            <select class="form-select" name="payment_status">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>

        <!-- Date range -->
        <div class="form-group">
            <label class="form-label fw-bold">Date</label>
            <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Select date range" />
        </div>
    </form>

    <div class="table-responsive mt-4">
        <div id="order-content">
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
@endpush

@push('scripts')
    <script>
        $(function() {
            // Initialize date range picker
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
                fetchOrders();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchOrders();
            });

            // Change event for select filters
            $(document).on('change', 'select[name="status"], select[name="payment_status"]', function() {
                fetchOrders();
            });

            // Search input (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchOrders();
                }, 500);
            });

            // Pagination
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

        // Send AJAX to filter orders
        function fetchOrders(url = "{{ route('orders.index') }}", page = 1) {
            const status = $('select[name="status"]').val();
            const payment_status = $('select[name="payment_status"]').val();
            const search = $('input[name="search"]').val();
            const date_range = $('input[name="date_range"]').val();
            const per_page = $('.per-page-selector').val() || 10;

            $('#order-content').hide();
            $('#loading').show();

            const urlWithParams = new URL(url, window.location.href);
            const searchParams = new URLSearchParams(urlWithParams.search);
            const pageParam = searchParams.get('page') || page;

            $.ajax({
                url: urlWithParams.pathname,
                method: 'GET',
                data: {
                    status,
                    payment_status,
                    search,
                    date_range,
                    per_page,
                    page: pageParam
                },
                beforeSend: () => {
                    $('#coupon-content').hide();
                    $('#loading').show();
                },
                success: function(response) {
                    $('#order-content').html(response.html).fadeIn(200);
                    $('#loading').hide();
                },
                error: function(xhr) {
                    console.error("Error loading orders:", xhr);
                    $('#loading').hide();
                    $('#order-content').show();
                },
                complete: () => {
                    $('#loading').hide();
                    $('#coupon-content').show();
                }
            });
        }
        fetchOrders()
    </script>
@endpush
