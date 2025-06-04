@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Orders</h1>

            <div class="d-flex gap-2 align-items-center">
                <button class="ant-btn ant-btn-default px-3 text-f06022 border-f06022">Create ticket <i
                        class="bi bi-plus-circle-dotted ms-2"></i></button>
                <button type="submit" class="ant-btn ant-btn-default px-3 " data-bs-toggle="modal"
                    data-bs-target="#exportOrderModal">
                    Export Order
                </button>
                <a href="{{ route('orders.import-order') }}" class="ant-btn ant-btn-default px-3 ">
                    Import Order
                </a>
                <a href="{{ route('orders.create') }}" id="headerOrder__createOrder_btn"
                    class="ant-btn ant-btn-primary px-3 ">
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
                <option value="in_production" @selected(request('status') === 'in_production')>In Production</option>
                <option value="produced_awaiting_completion">Produced Awaiting Completion</option>
                <option value="completed_waiting_for_shipment" @selected(request('status') === 'completed_waiting_for_shipment')>Completed Waiting For Shipment</option>
                <option value="shipped" @selected(request('status') === 'shipped')>Shipped</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            </select>
        </div>
        <!-- Payment Status -->
        <div class="form-group">
            <label class="form-label fw-bold">Payment Status</label>
            <select class="form-select" name="payment_status">
                <option value="">All</option>
                <option value="pending" @selected(request('payment_status') === 'pending')>Unpaid</option>
                <option value="completed" @selected(request('payment_status')  === 'completed')>Paid</option>
                <option value="refunded" @selected(request('payment_status' ) === 'refunded')>Refunded</option>
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

    <div class="modal fade" id="exportOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="exportOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exportOrderLabel">Export Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fw-bold mb-2">Export by filter</div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="exportOption" id="exportSelectedOrders"
                            value="selected">
                        <label class="form-check-label fw-semibold" for="exportSelectedOrders">
                            Export selected order(s).
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportOption" id="exportAll" value="all"
                            checked>
                        <label class="form-check-label fw-semibold" for="exportAll">
                            Export all orders.
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="ant-btn ant-btn-default px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="ant-btn ant-btn-primary px-3" id="export-now-btn">Export Now</button>
                </div>
            </div>
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

        $('#export-now-btn').on('click', function() {
            const exportType = $('input[name="exportOption"]:checked').val();

            let data = {
                type: exportType
            };

            if (exportType === 'selected') {
                const selectedOrderIds = $('.order-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedOrderIds.length === 0) {
                    datgin.error("Please select at least one order.");
                    return;
                }

                data.order_ids = selectedOrderIds;
            }

            $.ajax({
                url: '{{ route('orders.export') }}',
                method: 'POST',
                data: data,
                xhrFields: {
                    responseType: 'blob'
                },
                beforeSend: () => {
                    $('#loading').show();
                },
                success: function(response, status, xhr) {
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    const fileName = disposition ? disposition.split('filename=')[1].replace(/"/g, '') :
                        'orders.xlsx';
                    const blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });

                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;
                    link.click();

                    $('#exportOrderModal').modal('hide');
                },
                error: function() {
                    datgin.error('Export failed!');
                    $('#loading').hide();
                },
                complete: () => {
                    $('#loading').hide();
                }
            });
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

        function updateExportOptionState() {
            const anyChecked = $('.order-checkbox:checked').length > 0;
            $('#exportSelectedOrders').prop('disabled', !anyChecked);

            // Nếu đang chọn "Export selected order(s)" mà không còn đơn nào, chuyển sang export all
            if (!anyChecked && $('#exportSelectedOrders').is(':checked')) {
                $('#exportByFilter').prop('checked', true);
            }
        }

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
                updateExportOptionState();
            });

            updateExportOptionState();
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
            bindEventHandlers();
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
