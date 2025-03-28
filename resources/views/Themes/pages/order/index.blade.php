@extends('Themes.layout_staff.app')
@section('content')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>


<style>
    body {
        background-color: #f8f9fa;
        padding-top: 20px;
    }

    .container {
        background-color: #fff;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .order-table {
        width: 100%;
        margin-top: 20px;
    }

    .order-table th,
    .order-table td {
        padding: 10px;
        text-align: center;
        vertical-align: middle;
    }

    .order-table thead {
        background-color: #007bff;
        color: #fff;
    }

    .order-table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .order-table tbody tr:hover {
        background-color: #e9ecef;
    }

    .pagination {
        justify-content: center;
    }

    #tieude {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #tieude div {
        text-align: left;
    }

    #tieude h2 {
        display: block;
        margin: 0px auto;
    }
</style>
<div class="container">

    <div id="tieude">
        <div><a href="{{ route('staff.index') }}">Quay lại</a></div>
        <h2 class="text-center">Lịch sử đơn hàng</h2>
    </div>
    <table class="table table-bordered order-table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Mã đơn hàng</th>
                <th scope="col">Tên khách hàng</th>
                <th scope="col">Tổng tiền</th>
                <th scope="col">Người tạo</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">Trạng thái</th>
            </tr>
        </thead>
        <tbody id="order-data">
            <!-- Dữ liệu đơn hàng sẽ được thêm vào đây từ JavaScript -->
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center" id="pagination">

        </ul>
    </nav>
</div>

<script>
    var j = jQuery.noConflict();

    j.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': j('meta[name="csrf-token"]').attr('content')
        }
    });

    j(document).ready(function() {
        fetch_orders(1);

        function fetch_orders(page) {
            j.ajax({
                url: "{{ route('staff.orderFetch') }}?page=" + page,
                method: 'GET',
                success: function(data) {
                    let html = '';
                    var start_index = (page - 1) * data.pageOrder;
                    j.each(data.data, function(index, order) {
                        let formattedDate = moment(order.created_at).format('DD/MM/YYYY');
                        html += `
                            <tr>
                                <td>${start_index + index + 1}</td>
                                <td>${order.id}</td>
                                <td>${order.client_name}</td>
                                <td>${(Math.ceil(order.total_money / 500) * 500).toLocaleString('en-US')}</td>
                                <td>${order.user_name}</td>
                                <td>${formattedDate}</td>
                                <td>${order.status === 1 ? '<span class="badge badge-success p-2">Đã thanh toán</span>' : '<span class="badge badge-success p-2">Chưa thanh toán</span>'}</td>
                            </tr>
                        `;
                    });
                    j('#order-data').html(html);
                    let pagination = createPagination(data.current_page, data.last_page);
                    j('#pagination').html(pagination);
                },
                error: function(xhr, status, error) {
                    console.error('Lỗi khi lấy danh sách đơn hàng:', error);
                }
            });
        }

        function createPagination(current, last) {
            if (last == 1) {
                return '';
            }

            let pagination = '';

            if (current > 1) {
                pagination += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${current - 1}"><i class="fas fa-backward"></i></a>
                </li>`;
            }

            pagination += `<li class="page-item ${1 === current ? 'active' : ''}">
                <a class="page-link" href="#" data-page="1">1</a>
            </li>`;

            if (current > 3) {
                pagination += `<li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>`;
            }

            let start = Math.max(2, current - 1);
            let end = Math.min(current + 1, last - 1);

            for (let i = start; i <= end; i++) {
                pagination += `<li class="page-item ${i === current ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }

            if (current < last - 2) {
                pagination += `<li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>`;
            }

            pagination += `<li class="page-item ${last === current ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${last}">${last}</a>
            </li>`;

            if (current < last) {
                pagination += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${current + 1}"><i class="fas fa-forward"></i></a>
                </li>`;
            }

            return pagination;
        }


        j(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            let page = j(this).data('page');
            fetch_orders(page);
        });
    });
</script>

@endsection
