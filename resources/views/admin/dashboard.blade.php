@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="card custom-card mb-3">
            <div class="card-body">
                <form action="" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" id="date-range" name="date_range" class="form-control"
                            placeholder="Chọn thời gian" />
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <!-- Financial Metrics -->
            <div class="row">
                <div class="col-12">
                    <h5 class="fw-semibold text-dark">
                        <i class="fas fa-chart-line text-success me-2"></i>Thống kê tài chính
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tổng doanh số</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-sales">0 USD</h4>
                                </div>
                                <div class="icon-wrapper bg-success">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tổng số đơn hàng</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-orders">0</h4>
                                </div>
                                <div class="icon-wrapper bg-primary">
                                    <i class="fas fa-shopping-cart text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Yêu cầu nạp tiền</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-topup-requests">0</h4>
                                </div>
                                <div class="icon-wrapper bg-info">
                                    <i class="fas fa-credit-card text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Tracking -->
            <div class="row mt-3">
                <div class="col-12">
                    <h5 class="fw-semibold text-dark">
                        <i class="fas fa-boxes text-primary me-2"></i>Trạng thái đơn hàng
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Chờ xác nhận</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="pending-confirmation">0</h4>
                                </div>
                                <div class="icon-wrapper bg-warning">
                                    <i class="fas fa-clock text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Chờ sản xuất</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="pending-production">0</h4>
                                </div>
                                <div class="icon-wrapper bg-info">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Đang sản xuất</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="in-production">0</h4>
                                </div>
                                <div class="icon-wrapper bg-primary">
                                    <i class="fas fa-cogs text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Chờ hoàn thiện</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="pending-completion">0</h4>
                                </div>
                                <div class="icon-wrapper bg-secondary">
                                    <i class="fas fa-cube text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Chờ giao hàng</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="pending-delivery">0</h4>
                                </div>
                                <div class="icon-wrapper bg-success">
                                    <i class="fas fa-shipping-fast text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Đã giao hàng</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="delivered">0</h4>
                                </div>
                                <div class="icon-wrapper bg-success">
                                    <i class="fas fa-check-double text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Đã hủy</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="cancelled-orders">0</h4>
                                </div>
                                <div class="icon-wrapper bg-danger">
                                    <i class="fas fa-times-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Metrics -->
            <div class="row mt-3">
                <div class="col-12">
                    <h5 class="fw-semibold text-dark">
                        <i class="fas fa-users text-info me-2"></i>Thống kê người dùng
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tổng khách hàng</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-customers">0</h4>
                                </div>
                                <div class="icon-wrapper bg-primary">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tổng nhân viên</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-staff">48</h4>
                                </div>
                                <div class="icon-wrapper bg-success">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communication & Support -->
            <div class="row mt-3">
                <div class="col-12">
                    <h5 class="fw-semibold text-dark">
                        <i class="fas fa-headset text-warning me-2"></i>Hỗ trợ & Liên lạc
                    </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tin nhắn chưa đọc</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="unread-messages">0</h4>
                                </div>
                                <div class="icon-wrapper bg-warning">
                                    <i class="fas fa-envelope text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Tổng số ticket</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="total-tickets">0</h4>
                                </div>
                                <div class="icon-wrapper bg-info">
                                    <i class="fas fa-ticket-alt text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 stat-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <p class="text-muted mb-2 small fw-medium">Yêu cầu xuất vật tư</p>
                                    <h4 class="fw-bold mb-0 text-dark" id="material-request">0</h4>
                                </div>
                                <div class="icon-wrapper bg-secondary">
                                    <i class="fas fa-file-export text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let start = moment();
        let end = moment().add(1, 'month');

        $('#date-range').daterangepicker({
            startDate: start,
            endDate: end,
            autoUpdateInput: true,
            locale: {
                format: "DD/MM/YYYY",
                cancelLabel: "Hủy",
                applyLabel: "Áp dụng",
                customRangeLabel: "Tùy chọn",
                daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                monthNames: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
                firstDay: 1,
            },
            ranges: {
                "Hôm nay": [moment(), moment()],
                "Ngày mai": [moment().add(1, "days"), moment().add(1, "days")],
                "Tuần này": [moment().startOf("week"), moment().endOf("week")],
                "Tuần sau": [
                    moment().add(1, "week").startOf("week"),
                    moment().add(1, "week").endOf("week"),
                ],
                "Tháng này": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Tháng sau": [
                    moment().add(1, "month").startOf("month"),
                    moment().add(1, "month").endOf("month"),
                ],
            },
        });

        $('#date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            fetchData();
        });

        $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            fetchData();
        });

        function fetchData() {
            let dateRange = $('#date-range').val();
            let startDate = null;
            let endDate = null;

            if (dateRange) {
                [startDate, endDate] = dateRange.split(' - ');
                // Convert from DD/MM/YYYY to YYYY-MM-DD
                startDate = startDate.split('/').reverse().join('-');
                endDate = endDate.split('/').reverse().join('-');
            }

            $.ajax({
                url: window.location.href,
                type: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
                beforeSend: function() {
                    $("#loadingSpinner").fadeIn();
                },
                success: function(response) {
                    console.log(response);

                    $('#total-sales').text(formatNumber(response.total_sales) + ' USD')
                    $('#total-orders').text(response.total_orders)
                    $('#total-topup-requests').text(response.total_topup_requests)

                    $('#pending-confirmation').text(response.order_status_counts.pending ?? 0);
                    $('#pending-production').text(response.order_status_counts.confirmed_pending_production ??
                        0);
                    $('#in-production').text(response.order_status_counts.in_production ?? 0);
                    $('#pending-completion').text(response.order_status_counts.produced_awaiting_completion ??
                        0);
                    $('#pending-delivery').text(response.order_status_counts.completed_waiting_for_shipment ??
                        0);
                    $('#delivered').text(response.order_status_counts.shipped ?? 0);
                    $('#cancelled-orders').text(response.order_status_counts.cancelled ?? 0);

                    $('#total-customers').text(response.total_users ?? 0);
                    $('#total-staff').text(response.total_employees ?? 0);
                    $('#unread-messages').text(response.total_unread_messages_from_users_to_admin ?? 0);
                    $('#total-tickets').text(response.total_tickets ?? 0);
                    $('#material-request').text(response.total_material_requests ?? 0);
                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error);
                    alert('Có lỗi xảy ra khi tải dữ liệu');
                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                }
            });
        }

        fetchData()
    </script>
@endpush

@push('styles')
    <style>
        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 0.25rem;
        }

        .avatar {
            position: relative;
            height: 2.625rem;
            width: 2.625rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            color: #fff;
            font-weight: 500;
        }

        .stat-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            border-radius: 15px !important;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .stat-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(3deg);
            transition: transform 0.4s ease;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.5);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(0, 123, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }

        .stat-card:hover {
            animation: pulse 1.5s ease-out;
        }

        .action-card {
            transition: all 0.3s ease;
            border-radius: 15px !important;
            cursor: pointer;
        }

        .action-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
        }

        .icon-wrapper {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px !important;
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .summary-card .text-success {
            color: #28a745 !important;
        }

        .summary-card .text-primary {
            color: #007bff !important;
        }

        .summary-card .text-info {
            color: #17a2b8 !important;
        }

        .summary-card .text-warning {
            color: #ffc107 !important;
        }

        .summary-card .text-danger {
            color: #dc3545 !important;
        }

        .summary-card .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .summary-card .border-end {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #1e7e34) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #138496) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #c82333) !important;
        }

        .bg-gradient-secondary {
            background: linear-gradient(45deg, #6c757d, #545b62) !important;
        }

        /* Progress bars */
        .progress {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .summary-card .border-end {
                border-right: none !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
                padding-bottom: 1rem;
                margin-bottom: 1rem;
            }

            .summary-card .col-md-3:last-child .border-end {
                border-bottom: none !important;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .icon-wrapper {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Hover effects for icons */
        .stat-card:hover .icon-wrapper {
            transform: scale(1.15) rotate(5deg);
        }

        .action-card:hover i {
            transform: scale(1.2);
        }

        /* Icon improvements */
        .fas,
        .far {
            font-weight: 900;
        }

        /* Responsive text sizing */
        @media (max-width: 576px) {
            h4.fw-bold {
                font-size: 1.2rem;
            }

            h2.fw-bold {
                font-size: 1.5rem;
            }

            .icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
@endpush
