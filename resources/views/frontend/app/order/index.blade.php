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
                <option value="confirmed_pending_production">Confirmed Pending Production</option>
                <option value="in_production">In Production</option>
                <option value="produced_awaiting_completion">Produced Awaiting Completion</option>
                <option value="completed_waiting_for_shipment">Completed Waiting For Shipment</option>
                <option value="shipped">Shipped</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Status -->
        <div class="form-group">
            <label class="form-label fw-bold">Payment Status</label>
            <select class="form-select" name="payment_status">
                <option value="">All</option>
                <option value="pending">Unpaid</option>
                <option value="completed">Paid</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>

        <!-- Date range -->
        <div class="form-group">
            <label class="form-label fw-bold">Date</label>
            <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Select date range" />
        </div>
    </form>

    <div id="bulk-action" class="my-3" style="display: none">
        <div class="d-flex align-items-center gap-2">
            <button class="ant-btn ant-btn-primary px-2" id="pay-now">
                <div class="d-flex align-items-center gap-2">
                    <span id="total-amount">0</span>
                    <span class="w-1 h-1 rounded-circle bg-white"></span>
                    <span>Pay now</span>
                </div>
            </button>
            <select id="bulk-select" class="form-select d-inline-block w-auto">
                <option value="">--- Choose action ---</option>
                <option value="cancel">Cancel Order</option>
                <option value="delete">Delete Order</option>
            </select>
        </div>
    </div>


    <div class="table-responsive mt-4">
        <div id="order-content">
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/helper.js') }}"></script>
    <script>
        let lastOrdersUrl = "{{ route('orders.index') }}";

        $(document).on('change', '.order-checkbox', function() {
            let checkedBoxes = $('.order-checkbox:checked');
            let total = 0;


            // Tính tổng tiền
            checkedBoxes.each(function() {
                total += parseFloat($(this).data('total') || 0);
            });

            $('#total-amount').text(`${formatCurrency(total)}`);
        });

        $('#pay-now').click(function() {
            Swal.fire({
                title: "Confirm payment?",
                text: "Are you sure you want to proceed with the payment for the selected orders?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#aaa",
                confirmButtonText: "Yes, proceed",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    let ids = [];
                    let checkedBoxes = $('.order-checkbox:checked');

                    checkedBoxes.each(function() {
                        ids.push($(this).val())
                    });

                    $.ajax({
                        url: '{{ route('orders.payBulk') }}', // Route Laravel xử lý
                        type: 'POST',
                        data: {
                            ids: ids,
                        },
                        success: function(response) {
                            datgin.success(response.message);
                            fetchOrders(lastOrdersUrl)
                        },
                        error: function(xhr) {
                            Swal.fire("Error", xhr.responseJSON.message ||
                                "Something went wrong", "error");
                        }
                    });

                } else {
                    $('input[type="checkbox"]').prop('checked', false);
                    $('#bulk-action').hide()
                }
            });
        })

        // Checkbox "select all"
        $(document).on('change', '#select-all', function() {
            let isChecked = $(this).is(':checked');
            $('.order-checkbox').prop('checked', isChecked).trigger('change');
        });

        function bindEventHandlers() {
            const $selectAll = $('#select-all');
            const $checkboxes = $('.order-checkbox');
            const $bulkAction = $('#bulk-action');

            function updateBulkActionVisibility() {
                const anyChecked = $checkboxes.is(':checked');
                if (anyChecked) {
                    $bulkAction.show();
                } else {
                    $bulkAction.hide();
                }
            }

            $selectAll.on('change', function() {
                $checkboxes.prop('checked', this.checked);
                updateBulkActionVisibility();
            });

            $checkboxes.on('change', function() {
                const allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
                $selectAll.prop('checked', allChecked);
                updateBulkActionVisibility();
            });
        }

        $('#bulk-select').change(function() {
            let selectedValue = $(this).val();

            let ids = [];
            let checkedBoxes = $('.order-checkbox:checked');

            checkedBoxes.each(function() {
                ids.push($(this).val())
            });

            if (selectedValue === "delete") {
                handleDeleteOrder(ids)
            } else if (selectedValue === "cancel") {
                handleCancelOrder(ids)
            }
        })

        function handleDeleteOrder(ids) {
            Swal.fire({
                title: 'Are you sure you want to delete the order(s)?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '{{ route('orders.deleteBulk') }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                        },
                        beforeSend: () => {
                            $('#loading').show();
                        },
                        success: function(response) {
                            datgin.success(response.message);
                            fetchOrders(lastOrdersUrl)
                        },
                        error: function(xhr) {
                            datgin.error(xhr.responseJSON.message || "Something went wrong")
                        },
                        complete: () => {
                            $('#loading').hide();
                        }
                    })

                } else {
                    $('input[type="checkbox"]').prop('checked', false);
                    $('#bulk-action').hide()
                    $('#bulk-select').val('')
                }
            });
        }

        function handleCancelOrder(ids) {
            Swal.fire({
                title: 'Are you sure you want to cancel the order(s)?',
                text: 'The orders will be marked as canceled.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Cancel Order',
                cancelButtonText: 'No',
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: '{{ route('orders.cancelBulk') }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                        },
                        beforeSend: () => {
                            $('#loading').show();
                        },
                        success: function(response) {
                            datgin.success(response.message);
                            fetchOrders(lastOrdersUrl)
                        },
                        error: function(xhr) {
                            datgin.error(xhr.responseJSON.message || "Something went wrong")
                        },
                        complete: () => {
                            $('#loading').hide();
                        }
                    })

                } else {
                    $('input[type="checkbox"]').prop('checked', false);
                    $('#bulk-action').hide()
                    $('#bulk-select').val('')
                }
            });
        }


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
        function fetchOrders(url = lastOrdersUrl, page = 1) {
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
                    lastOrdersUrl = urlWithParams.pathname;

                    $('input[type="checkbox"]').prop('checked', false);
                    $('#bulk-action').hide()

                    $('#order-content').html(response.html).fadeIn(200);
                    $('#loading').hide();
                    bindEventHandlers();
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
