@extends('frontend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Xin chào, {{ auth()->guard('web')->user()->name }}</h2>
            <div class="input-group w-auto">
                <input type="text" id="date-range" name="date_range" class="form-control" placeholder="Select date range" />
                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
            </div>
        </div>

        <h4 class="mb-3 fw-bold">Tổng quan</h4>

        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Tổng đơn hàng</h6>
                        <h3 class="card-title mb-0 fw-bold order-all">{{ $orderCounts['all'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Chưa thanh toán</h6>
                        <h3 class="card-title mb-0 fw-bold order-unpaid">{{ $orderCounts['unpaid'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Đang sản xuất</h6>
                        <h3 class="card-title mb-0 fw-bold order-in-production">{{ $orderCounts['in_production'] ?? 0 }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Đang vận chuyển</h6>
                        <h3 class="card-title mb-0 fw-bold order-shipping">{{ $orderCounts['shipping'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Đã giao hàng</h6>
                        <h3 class="card-title mb-0 fw-bold order-shipped">{{ $orderCounts['shipped'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 title">Đã hủy</h6>
                        <h3 class="card-title mb-0 fw-bold order-cancelled">{{ $orderCounts['cancelled'] ?? 0 }}</h3>
                    </div>
                </div>
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
                    $('#loading').show();
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
                    $('#loading').hide();
                }
            });
        }
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
@endpush
