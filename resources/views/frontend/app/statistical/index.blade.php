@extends('frontend.app')

@section('content')
    <div class="container">
        <div class="container-wrapper">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4 mb-5">
                <h1 class="billing__title__content display-4 fw-bold text-dark">Spending Statistics</h1>
            </div>
        </div>

        <!-- Date Range Picker -->
        <div class="mb-4 w-100 w-md-25">
            <label class="form-label fw-bold text-dark">Select Date Range</label>
            <input type="text" id="date-range" name="date_range" class="form-control shadow-sm"
                placeholder="Select date range" />
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 h-100 stats-card">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-wallet2 text-primary me-3 fs-2"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Top-up</h6>
                            <h4 class="card-text fw-bold text-dark" id="totalTopup">-</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 h-100 stats-card">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-cart3 text-success me-3 fs-2"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Spent</h6>
                            <h4 class="card-text fw-bold text-dark" id="totalSpent">-</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 h-100 stats-card">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-arrow-return-left text-warning me-3 fs-2"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Refund</h6>
                            <h4 class="card-text fw-bold text-dark" id="totalRefund">-</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow-sm border-0 h-100 stats-card">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-wallet2 text-info me-3 fs-2"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Wallet Balance</h6>
                            <h4 class="card-text fw-bold text-dark" id="walletBalance">-</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark mb-4">Daily Spending</h5>
                <canvas id="spendingChart" height="100"></canvas>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark mb-4">Top Products</h5>
                <canvas id="topProductsChart" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/icomoon/style.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <script>
        let spendingChart, topProductsChart;

        function renderCharts(data) {
            console.log(data);

            $('#totalTopup').text(data.total_topup);
            $('#totalSpent').text(data.total_spent);
            $('#totalRefund').text(Number(data.total_refund).toLocaleString());
            $('#walletBalance').text(data.wallet_balance);

            // Chart 1: Chi tiêu theo ngày
            const labels = data.daily_spent.map(e => e.date);
            const values = data.daily_spent.map(e => e.total);

            console.log(values);


            if (spendingChart) spendingChart.destroy();
            const ctxSpending = document.getElementById('spendingChart').getContext('2d');
            spendingChart = new Chart(ctxSpending, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Chi tiêu theo ngày',
                        data: values,
                        borderColor: 'rgba(75,192,192,1)',
                        fill: false,
                        tension: 0.2
                    }]
                }
            });

            // Chart 2: Top sản phẩm
            const topLabels = data.top_products.map(e => e.product_name);
            const topValues = data.top_products.map(e => e.spent);

            if (topProductsChart) topProductsChart.destroy();
            const ctxTopProducts = document.getElementById('topProductsChart').getContext('2d');
            topProductsChart = new Chart(ctxTopProducts, {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: 'Tổng chi tiêu (USD)',
                        data: topValues,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }]
                }
            });
        }

        function loadStats() {
            const date_range = $('input[name="date_range"]').val();
            let start = '',
                end = '';

            if (date_range) {
                const parts = date_range.split(' - ');
                if (parts.length === 2) {
                    const [day1, month1, year1] = parts[0].split('/');
                    const [day2, month2, year2] = parts[1].split('/');

                    start = `${year1}-${month1}-${day1}`; // YYYY-MM-DD
                    end = `${year2}-${month2}-${day2}`;
                }
            }

            $.ajax({
                url: `/spending-stats`,
                type: 'GET',
                data: {
                    start_date: start,
                    end_date: end
                },
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: function(res) {
                    renderCharts(res);
                },
                error: function(err) {
                    alert('Có lỗi xảy ra khi tải thống kê!');
                    console.error(err);
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
        }

        $(document).ready(function() {
            $('#filterBtn').on('click', loadStats);
            loadStats(); // tự load khi vừa vào trang

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
                loadStats();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                loadStats();
            });
        });
    </script>
@endpush
