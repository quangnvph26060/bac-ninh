@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'Phát sinh đối ứng']]" />
        </div>

        <div class="card p-3 mb-3 shadow-sm">
            <div class="d-flex flex-wrap gap-2 justify-content-end align-items-end">
                <div>
                    <input type="text" class="form-control" name="date_range" id="dateFilter">
                </div>
                <div>
                    <input type="text" class="form-control" name="account" placeholder="Tên tài khoản">
                </div>
                <div>
                    <button type="button" class="btn btn-success btn-sm" id="filter">
                        <i class="bi bi-funnel-fill"></i> Lọc
                    </button>
                </div>
            </div>
        </div>

        <div class="alert alert-warning" role="alert">
            Vui lòng nhập tài khoản
        </div>

        <div class="card p-3 shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Tài khoản đối ứng</th>
                            <th>Tên tài khoản</th>
                            <th>Phát sinh nợ</th>   
                            <th>Phát sinh có</th>
                        </tr>
                    </thead>
                    <tbody id="ledger-body">
                        <tr id="no-data">
                            <td colspan="4" class="text-center">Vui lòng chọn tài khoản và lọc để xem dữ liệu.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function() {
            let start = moment().subtract(1, 'month'); // 15/06/2025
            let end = moment();

            $('#dateFilter').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'DD/MM/YYYY',
                    cancelLabel: 'Hủy',
                    applyLabel: 'Áp dụng',
                    customRangeLabel: 'Tùy chọn',
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    monthNames: [
                        'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                    ],
                    firstDay: 1
                },
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Ngày mai': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Tuần này': [moment().startOf('week'), moment().endOf('week')],
                    'Tuần sau': [moment().add(1, 'week').startOf('week'), moment().add(1, 'week').endOf(
                        'week')],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng sau': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf(
                        'month')]
                }
            });

            // Hiển thị mặc định trên input khi load
            $('#dateFilter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            $('#dateFilter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            });

            function getLedgerTransactions() {

                let date_range = $('input[name="date_range"]').val()
                let account = $('input[name="account"]').val()
                let alert = $('.alert.alert-warning')

                if (account) {
                    alert.addClass('d-none')
                } else {
                    alert.removeClass('d-none')
                }

                $.ajax({
                    url: '',
                    method: 'GET',
                    data: {
                        date_range,
                        account
                    },
                    beforeSend: function() {
                        $("#loadingSpinner").fadeIn();
                    },
                    success: function(response) {
                        generateTbody(response, account);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            alert.text(xhr.responseJSON.message).removeClass('d-none');
                        } else {
                            alert.text('Đã xảy ra lỗi, vui lòng thử lại.').removeClass('d-none');
                        }
                    },
                    complete: function() {
                        $("#loadingSpinner").fadeOut();
                    }
                })

            }

            function generateTbody(data, account) {
                let tbody = '';
                if (data.entries.length === 0) {
                    tbody = `
                        <tr id="no-data">
                            <td colspan="4" class="text-center">Không có dữ liệu trong khoảng thời gian này.</td>
                        </tr>
                    `;
                                } else {
                                    tbody += `
                        <tr>
                            <td colspan="2" class="text-end">Dư đầu kỳ</td>
                            <td class="text-end">${data.du_dau_ky != 0 ? Number(data.du_dau_ky).toLocaleString() : ''}</td>
                            <td></td>
                        </tr>
                    `;

                    data.entries.forEach(entry => {
                        tbody += `
                            <tr>
                                <td class="text-start">${entry.doi_ung ?? ''}</td>
                                <td class="text-start">${account}</td>
                                <td class="text-end">${entry.phat_sinh_no != 0 ? formatNumber(entry.phat_sinh_no) : ''}</td>
                                <td class="text-end">${entry.phat_sinh_co != 0 ? formatNumber(entry.phat_sinh_co) : ''}</td>
                            </tr>
                        `;
                    });

                    tbody += `
                        <tr>
                            <td colspan="2" class="text-end">Tổng</td>
                            <td class="text-end fw-bold">${data.tong_phat_sinh_no != 0 ? formatNumber(data.tong_phat_sinh_no) : ''}</td>
                            <td class="text-end fw-bold">${data.tong_phat_sinh_co != 0 ? formatNumber(data.tong_phat_sinh_co) : ''}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end">Dư cuối kỳ</td>
                            <td class="text-end fw-bold">${data.du_cuoi_ky != 0 ? formatNumber(data.du_cuoi_ky) : ''}</td>
                            <td></td>
                        </tr>
                    `;
                }
                $('#ledger-body').html(tbody);
            }


            $('#filter').on('click', function() {
                getLedgerTransactions()
            })

            getLedgerTransactions()


        })
    </script>
@endpush
