@extends('frontend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Hello, {{ auth()->guard('web')->user()->name }}</h2>
            <div class="input-group w-auto">
                <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Select date range" />
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
            </div>
        </div>

        {{-- @dd($orderCounts) --}}
        <h4 class="mb-3 fw-bold">Overview</h4>

        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index') }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">Total order</h6>
                            <h3 class="card-title mb-0 fw-bold order-all">{{ $orderCounts['all'] ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index', ['payment_status' => 'pending']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">Not yet paid</h6>
                            <h3 class="card-title mb-0 fw-bold order-unpaid">{{ $orderCounts['unpaid'] ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index', ['status' => 'in_production']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">In production</h6>
                            <h3 class="card-title mb-0 fw-bold order-in-production">{{ $orderCounts['in_production'] ?? 0 }}
                            </h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index', ['status' => 'completed_waiting_for_shipment']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">Shipping</h6>
                            <h3 class="card-title mb-0 fw-bold order-shipping">{{ $orderCounts['shipping'] ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index', ['status' => 'shipped']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">Delivered</h6>
                            <h3 class="card-title mb-0 fw-bold order-shipped">{{ $orderCounts['delivered'] ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 title">Canceled</h6>
                            <h3 class="card-title mb-0 fw-bold order-cancelled">{{ $orderCounts['cancelled'] ?? 0 }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

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
                fetchApi();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchApi();
            });
        })

        function fetchApi() {
            let dateRange = $('#date-range').val();

            $.ajax({
                url: window.location.href,
                type: 'GET',
                data: {
                    date_range: dateRange
                },
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: function(res) {
                    // Gán lại dữ liệu vào các ô thống kê
                    $('.order-all').text(res.all);
                    $('.order-in-production').text(res.in_production);
                    $('.order-shipping').text(res.shipping);
                    $('.order-shipped').text(res.shipped);
                    $('.order-cancelled').text(res.cancelled);
                    $('.order-unpaid').text(res.unpaid);
                },
                error: function() {
                    alert('Có lỗi xảy ra khi tải dữ liệu.');
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
        }
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">

    <style>
        /* Thiết lập mặc định cho thẻ card */
        .row a .card {
            transition: transform .3s ease, box-shadow .3s ease;
            /* mượt hơn khi hover ra vào */
        }

        /* Hiệu ứng khi hover */
        .row a:hover .card {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 12px 24px rgba(0, 0, 0, .12);
        }

        /* Nhấn mạnh tiêu đề khi hover */
        .row a:hover .card .title {
            color: #f06022;
            /* màu chính Bootstrap */
            transition: color .3s ease;
        }

        /* Nhấn mạnh số lượng khi hover */
        .row a:hover .card .card-title {
            animation: pulse 0.6s forwards;
        }

        /* Keyframes cho hiệu ứng pulse nhẹ */
        @keyframes pulse {
            0% {
                letter-spacing: normal;
            }

            50% {
                letter-spacing: 1px;
            }

            100% {
                letter-spacing: normal;
            }
        }
    </style>
@endpush
