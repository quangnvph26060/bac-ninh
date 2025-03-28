@extends('admin.layout.index')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>


    <style>
        .row {
            display: flex;
        }

        .col-md-8,
        .col-md-4 {
            display: flex;
            flex-direction: column;
        }

        .card,
        .card-custom {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex-grow: 1;
        }

        /* Thiết kế chung cho thẻ card */
        #income-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Đổ bóng mềm */
            background-color: #fff;
            /* Nền trắng */
            transition: all 0.3s ease;
            /* Hiệu ứng mượt */
            margin: 10px 0;
            /* Khoảng cách trên dưới */
            border: 1px solid #e0e0e0;
            /* Đường viền mỏng */
        }

        #income-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            /* Đổ bóng đậm hơn khi hover */
            transform: translateY(-4px);
            /* Nâng nhẹ khi hover */
        }

        /* Phần đầu của card */
        #income-card-header {
            background: linear-gradient(135deg, #177dff, #6ea0ff);
            /* Gradient màu xanh */
            color: #fff;
            /* Màu chữ trắng */
            padding: 15px;
            /* Khoảng đệm */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #income-card-header .card-title {
            font-size: 18px;
            /* Kích thước chữ */
            font-weight: 600;
            /* Độ đậm của chữ */
        }

        /* Nút dropdown */
        #income-card .btn-label-light {
            background-color: rgba(255, 255, 255, 0.15);
            /* Màu nền trong suốt nhẹ */
            border: none;
            /* Bỏ đường viền */
            color: #fff;
            /* Màu chữ trắng */
            transition: background-color 0.3s ease;
            /* Hiệu ứng chuyển màu */
            padding: 5px 12px;
            /* Khoảng đệm */
        }

        #income-card .btn-label-light:hover {
            background-color: rgba(255, 255, 255, 0.3);
            /* Màu nền sáng hơn khi hover */
        }

        /* Nội dung của card */
        #income-card-body {
            padding: 20px;
            /* Khoảng đệm rộng */
            color: #333;
            /* Màu chữ tối hơn */
        }

        /* Định dạng thông tin hiển thị */
        #income-card-body .info p {
            margin: 5px 0;
            /* Khoảng cách giữa các đoạn */
            font-size: 16px;
            /* Kích thước chữ */
            line-height: 1.5;
            /* Dòng cách */
            font-weight: 500;
            /* Độ đậm của chữ */
        }

        #income {
            font-size: 24px;
            /* Kích thước lớn hơn cho thu nhập */
            font-weight: bold;
            /* Chữ đậm */
            color: #177dff;
            /* Màu xanh nổi bật */
        }

        #day_month_year {
            font-size: 14px;
            /* Kích thước chữ nhỏ */
            color: rgba(255, 255, 255, 0.8);
            /* Màu trắng mờ */
        }

        /* Định dạng các mục tương tác trong dropdown */
        #income-card .dropdown-menu .dropdown-item {
            transition: background-color 0.3s ease;
            /* Hiệu ứng chuyển màu */
            font-weight: 500;
            /* Độ đậm của chữ */
        }

        #income-card .dropdown-menu .dropdown-item:hover {
            background-color: rgba(23, 125, 255, 0.1);
            /* Màu nền khi hover */
            color: #177dff;
            /* Màu chữ xanh khi hover */
        }

        /* Định dạng lợi nhuận */
        #income-card-body .profit p {
            margin: 5px 0;
            /* Khoảng cách giữa các đoạn */
            font-size: 15px;
            /* Kích thước chữ */
            color: #555;
            /* Màu chữ xám đậm */
        }


        #dropdownMenuButton:hover {
            color: black !important;
            background: rgb(170, 169, 169);
        }

        .slider-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            margin: auto;
            max-height: 180px;
        }

        .slider {
            display: flex;
            transition: transform 0.5s ease;
        }

        .slide {
            flex: 0 0 100%;
            /* Đảm bảo mỗi slide chiếm full width */
        }

        .prev-btn,
        .next-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(65, 64, 64, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
            z-index: 1;
        }

        .prev-btn {
            left: 0;
        }

        .next-btn {
            right: 0;
        }

        .chart-container {
            position: relative;
            width: 100%;
            min-height: 375px;
        }

        #statisticsChart {
            display: block;
            width: 100%;
            height: auto;
        }

        #myChartLegend {
            margin-top: 10px;
        }

        .chart-legend {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .chart-legend li {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .legend-color {
            display: inline-block;
            width: 20px;
            height: 10px;
            margin-right: 5px;
            border-radius: 2px;
        }

        #myChartLegend .legend-color {
            vertical-align: middle;
        }
    </style>

    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Thống kê </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Khách hàng mới</p>
                                    <h4 class="card-title">{{ $clientnumber }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                    <i class="far fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Đơn hàng năm nay</p>
                                    <h4 class="card-title">{{ $ordernumber }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="fas fa-luggage-cart"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Thu nhập năm nay</p>
                                    <h4 class="card-title">{{ number_format($amount) }} VND</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 d-flex flex-column">
                <div class="card card-round flex-grow-1">
                    <div class="card-header" style="padding: 10px">
                        <div class="card-head-row">
                            <div class="card-title">Thống kê doanh thu</div>
                            <div class="card-tools">
                                <a href="{{ route('admin.report.orders.getDailyOrder') }}"
                                    class="btn btn-label-info btn-round btn-sm">
                                    <span class="btn-label">
                                        <i class="fa fa-solid fa-chart-bar"></i>
                                    </span>
                                    Báo cáo bán hàng
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0px">
                        <div class="chart-container">
                            <canvas id="columnChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 d-flex flex-column mb-4">
                <div class="card-custom" id="income-card" style="margin-bottom: 10px">
                    <div class="card-header" id="income-card-header">
                        <div class="card-head-row">
                            <div class="card-title" id="daily" style="color:white">Thu nhập ngày</div>
                            <div class="card-tools">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-label-light dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Theo ngày
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" id="day">Theo ngày</a>
                                        <a class="dropdown-item" id="month">Theo tháng</a>
                                        <a class="dropdown-item" id="year">Theo năm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-category" id="day_month_year">{{ date('d/m/Y') }}</div>
                    </div>
                    <div class="card-body" id="income-card-body">
                        <div class="info">
                            <p id="income">{{ $daily['income'] }}</p>
                            <p><u id="amount">{{ $daily['amount'] }} Đơn</u></p>
                        </div>
                        <div class="profit">
                            <p id="interest">Lợi nhuận: {{ $daily['interest'] }}</p>
                            <p id="moneyinterest">{{ $daily['moneyinterest'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="your-slider" style="margin: 0px">
                    <!-- Thêm nhiều slide theo nhu cầu -->
                </div>
                <div class="slider-container">
                    <div class="slider">
                        <div class="slide">
                            <img style="width: 100%; height: auto;"
                                src="https://intphcm.com/data/upload/poster-quang-cao-dep-mat.jpg" alt="">
                        </div>
                        <div class="slide">
                            <img style="width: 100%; height: auto;"
                                src="https://images2.thanhnien.vn/528068263637045248/2024/1/25/e093e9cfc9027d6a142358d24d2ee350-65a11ac2af785880-17061562929701875684912.jpg"
                                alt="">
                        </div>
                        <div class="slide">
                            <img style="width: 100%; height: auto;"
                                src="https://hoanghamobile.com/tin-tuc/wp-content/uploads/2023/07/anh-dep-thien-nhien-2-1.jpg"
                                alt="">
                        </div>
                    </div>
                    <button class="prev-btn">&#10094;</button>
                    <button class="next-btn">&#10095;</button>
                </div>
            </div>
        </div>

        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Sản phẩm bán chạy nhất</div>
                    <div class="card-tools">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" scope="col">Mã sản phẩm</th>
                                <th class="text-center" scope="col">Tên sản phẩm</th>
                                <th class="text-center" scope="col">Giá nhập</th>
                                <th class="text-center" scope="col">Giá bán</th>
                                <th class="text-center" scope="col">Số lượng đã bán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topProducts as $item)
                                <tr>
                                    <td class="text-center"><a
                                            href="{{ route('admin.product.edit', ['id' => $item->product_id]) }}">{{ $item->code }}</a>
                                    </td>
                                    <td class="text-center">{{ $item->name ?? '' }}</td>
                                    <td class="text-center">{{ number_format($item->price ?? '') }}</td>
                                    <td class="text-center">{{ number_format($item->priceBuy ?? '') }}</td>
                                    <td class="text-center">{{ $item->total_quantity ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="card card-round">
        <div class="card-header">
            <div class="card-head-row card-tools-still-right">
                <div class="card-title">Đơn hàng gần đây</div>
                <div class="card-tools">
                    <div class="dropdown">
                        <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" scope="col">Mã đơn hàng</th>
                            <th class="text-center" scope="col">Ngày tạo</th>
                            <th class="text-center" scope="col">Khách hàng</th>
                            <th class="text-center" scope="col">Tổng tiền (VND)</th>
                            <th class="text-center" scope="col">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($newOrder as $item)
                            <tr>
                                <td class="text-center"><a
                                        href="{{ route('admin.order.detail', ['id' => $item->id]) }}">{{ $item->id }}</a>
                                </td>
                                <td class="text-center">{{ $item->created_at->format('H:i d/m/Y') }}</td>
                                <td class="text-center">{{ $item->client->name }}</td>
                                <td class="text-center">{{ number_format($item->total_money) }}</td>
                                @if ($item->status !== 4)
                                    <td class="text-center">
                                        <span class="badge badge-success">Hoàn thành</span>
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span style="background-color: red" class="badge badge-success">Công nợ</span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>

    </div>

    <script>
        $(document).ready(function() {
            $('#day').click(function(e) {

                $.ajax({
                    url: '{{ route('admin.dashboard.day') }}',
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        let today = new Date();
                        let formattedDate = today.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                        $('#day_month_year').text(formattedDate);
                        $('#daily').text('Thu nhập ngày');
                        $('#dropdownMenuButton').text('Theo ngày');
                        $('#income').text(response.daily['income']);
                        $('#amount').text(response.daily['amount'] + ' Đơn');
                        $('#interest').text('Lợi nhuận : ' + response.daily['interest']);
                        $('#moneyinterest').text(response.daily['moneyinterest']);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error);
                    }
                });
            });

            $('#month').click(function(e) {
                $.ajax({
                    url: '{{ route('admin.dashboard.month') }}',
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        let today = new Date();
                        let month = today.getMonth() + 1;
                        let year = today.getFullYear();
                        let formattedMonthYear = `${month}/${year}`;
                        $('#day_month_year').text(formattedMonthYear);
                        $('#daily').text('Thu nhập tháng');
                        $('#dropdownMenuButton').text('Theo tháng');
                        $('#income').text(response.daily['income']);
                        $('#amount').text(response.daily['amount'] + ' Đơn');
                        $('#interest').text('Lãi xuất : ' + response.daily['interest']);
                        $('#moneyinterest').text(response.daily['moneyinterest']);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error);
                    }
                });
            })

            $('#year').click(function(e) {
                $.ajax({
                    url: '{{ route('admin.dashboard.year') }}',
                    method: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        let today = new Date();
                        let year = today.getFullYear();
                        let formattedYear = `${year}`;
                        $('#day_month_year').text(formattedYear);
                        $('#daily').text('Thu nhập năm');
                        $('#dropdownMenuButton').text('Theo năm');
                        $('#income').text(response.daily['income']);
                        $('#amount').text(response.daily['amount'] + ' Đơn');
                        $('#interest').text('Lợi nhuận : ' + response.daily['interest']);
                        $('#moneyinterest').text(response.daily['moneyinterest']);
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error);
                    }
                });
            })
        });

        function formatCurrency(number) {
            // return Number(number).toLocaleString('vi-VN') + ' VND';
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(number).replace('₫', 'VND');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const slider = document.querySelector(".slider");
            const slides = document.querySelectorAll(".slide");
            const prevBtn = document.querySelector(".prev-btn");
            const nextBtn = document.querySelector(".next-btn");

            let slideIndex = 0;
            const totalSlides = slides.length;
            const autoSlideInterval = 3000;

            showSlide(slideIndex);

            prevBtn.addEventListener("click", function() {
                slideIndex = (slideIndex - 1 + totalSlides) % totalSlides;
                showSlide(slideIndex);
                resetAutoSlide();
            });


            nextBtn.addEventListener("click", function() {
                slideIndex = (slideIndex + 1) % totalSlides;
                showSlide(slideIndex);
                resetAutoSlide();
            });

            let autoSlideTimer = setInterval(function() {
                slideIndex = (slideIndex + 1) % totalSlides;
                showSlide(slideIndex);
            }, autoSlideInterval);

            function resetAutoSlide() {
                clearInterval(autoSlideTimer);
                autoSlideTimer = setInterval(function() {
                    slideIndex = (slideIndex + 1) % totalSlides;
                    showSlide(slideIndex);
                }, autoSlideInterval);
            }

            function showSlide(index) {
                slider.style.transform = `translateX(${-index * 100}%)`;
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('columnChart').getContext('2d');
            var months = {!! json_encode(range(1, 12)) !!};
            var monthlyRevenue = {!! json_encode($getMonthlyRevenue) !!};

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months.map(function(month) {
                        return 'Tháng ' + month;
                    }),
                    datasets: [{
                        label: 'Doanh thu hàng tháng',
                        data: monthlyRevenue,
                        backgroundColor: Array(12).fill('#177dff'),
                        borderColor: Array(12).fill('#177dff'),
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    return value.toLocaleString(
                                        'vi-VN'); // Thêm dấu phẩy vào giá trị trục Y
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.datasets[tooltipItem.datasetIndex].label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += tooltipItem.yLabel.toLocaleString(
                                    'vi-VN'); // Định dạng giá trị khi hover
                                return label;
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
