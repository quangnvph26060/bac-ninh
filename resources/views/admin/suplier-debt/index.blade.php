@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'công nợ']]" />
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card card-primary text-white">
                    <div class="card-body">
                        <i class="fas fa-credit-card card-icon"></i>
                        <h6 class="card-title">Tổng công nợ</h6>
                        <h3 class="card-amount animate-number" id="totalDebt">{{ formatPrice($totalDebt) }} USD</h3>
                        <p class="card-subtitle">Tổng số tiền cần thanh toán</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card stats-card card-success text-white">
                    <div class="card-body">
                        <i class="fas fa-check-circle card-icon"></i>
                        <h6 class="card-title">Đã thanh toán</h6>
                        <h3 class="card-amount animate-number" id="totalPaid">{{ formatPrice($totalPaid) }} USD</h3></h3>
                        <p class="card-subtitle">Số tiền đã hoàn thành</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card stats-card card-danger text-white">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle card-icon"></i>
                        <h6 class="card-title">Còn lại</h6>
                        <h3 class="card-amount animate-number" id="totalRemain">{{ formatPrice($totalRemain) }} USD</h3>
                        <p class="card-subtitle">Số tiền cần thanh toán</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách công nợ</h5>
            </div>

            <x-data-table file="supplier-debt" />

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="debtModal" tabindex="-1" aria-labelledby="debtModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="debtModalLabel">Chi tiết công nợ nhà cung cấp</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Đóng"></button>
                </div>

                <div class="modal-body">

                    <!-- Thông tin công nợ -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light fw-bold">
                            Thông tin công nợ <span id="debt-code"
                                class="text-muted fst-italic text-decoration-underline"></span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Thông tin công nợ -->
                                    <p>
                                        <strong>Nhà cung cấp:</strong>
                                        <span id="supplierName"></span>
                                    </p>
                                    <p>
                                        <strong>Ngày phát sinh:</strong>
                                        <span id="debtDate"></span>
                                    </p>
                                    <p>
                                        <strong>Trạng thái:</strong>
                                        <span id="debtStatus" class="badge bg-warning text-dark rounded-pill"></span>
                                    </p>

                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>Tổng tiền:</strong>
                                        <span id="debtTotal" class="text-danger fw-semibold"></span>
                                    </p>
                                    <p>
                                        <strong>Đã thanh toán:</strong>
                                        <span id="debtPaid" class="text-success fw-semibold"></span>
                                    </p>
                                    <p>
                                        <strong>Còn nợ:</strong>
                                        <span id="debtRemain" class="text-warning fw-semibold"></span>
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin phiếu nhập -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light fw-bold">
                            Thông tin phiếu nhập <span id="entry-code"
                                class="text-muted fst-italic text-decoration-underline"></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã vật tư</th>
                                            <th>Tên vật tư</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá (USD)</th>
                                            <th>Thành tiền (USD)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="importTable" class="">
                                        <!-- Render bằng JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch sử giao dịch -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light fw-bold">
                            Lịch sử giao dịch
                        </div>
                        <div class="card-body">
                            <ul id="transactionHistory" class="list-unstyled mb-0">
                                <!-- Render bằng JS -->
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thanh Toán -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="paymentForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thanh toán công nợ - <span id="supplier_debt_code"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="debt_id" id="debt_id">

                        <!-- Thông tin công nợ -->
                        <div class="mb-3">
                            <p><strong>Tổng tiền:</strong> <span id="total_amount_text">0 USD</span></p>
                            <p><strong>Đã thanh toán:</strong> <span id="paid_amount_text">0 USD</span></p>
                            <p><strong>Còn nợ:</strong> <span id="remaining_amount_text">0 USD</span></p>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Số tiền thanh toán (USD)</label>
                            <input type="text" name="amount" id="amount" value="0"
                                class="form-control usd-price-format">
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Ngày thanh toán</label>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea name="note" id="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Lưu thanh toán</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <iframe id="print-frame" style="display:none;"></iframe>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.suppliers-debts.index') }}"

            dataTables(api, columns, 'SupplierDebt', {
                supplier_id: {
                    title: 'Lọc nhà cung cấp',
                    data: @json($suppliers)
                },
            }, false, true, false, true)


            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                fetchStatistics(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                fetchStatistics()
            });

            function fetchStatistics(startDate = null, endDate = null) {
                $.ajax({
                    url: "/admin/suppliers-debts/statistics",
                    type: "GET",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#totalDebt').text(formatNumber(response.data.totalDebt) + " USD");
                            $('#totalPaid').text(formatNumber(response.data.totalPaid) + " USD");
                            $('#totalRemain').text(formatNumber(response.data.totalRemain) + " USD");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Có lỗi xảy ra khi lấy dữ liệu thống kê.');
                    }
                });
            }

            $(document).on('click', '.show-modal', function(e) {
                e.preventDefault();

                let supplierDebtId = $(this).data('id');

                $.get('/admin/suppliers-debts/show/' + supplierDebtId, function(response) {

                    const details = response.data.import.details;
                    const payments = response.data.import.debt.payments;

                    console.log(response);


                    // render thông tin ra các tab
                    $('#debt-code').text('#' + response.data.code);
                    $('#entry-code').text('#' + response.data.import.code);
                    $('#supplierName').text(response.data.supplier.company_name);
                    $('#debtDate').text(dayjs(response.data.created_at).format('DD/MM/YYYY'));
                    $('#debtTotal').text(formatNumber(response.data.total_amount) + ' USD');
                    $('#debtPaid').text(formatNumber(response.data.paid_amount) + ' USD');
                    $('#debtRemain').text(formatNumber(response.data.total_amount - response.data
                            .paid_amount) +
                        ' USD');

                    const statusMap = {
                        unpaid: {
                            text: 'Chưa thanh toán',
                            badge: 'danger'
                        },
                        partial: {
                            text: 'Chưa thanh toán hết',
                            badge: 'warning'
                        },
                        paid: {
                            text: 'Đã thanh toán',
                            badge: 'success'
                        }
                    };

                    const statusInfo = statusMap[response.data.status]

                    $('#debtStatus')
                        .removeClass()
                        .addClass('badge bg-' + statusInfo.badge + ' text-white rounded-pill')
                        .text(statusInfo.text);

                    // // Bảng chi tiết phiếu nhập
                    let tableHtml = '';
                    details.forEach((item, index) => {
                        tableHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.material.code}</td>
                            <td>${item.material.name}</td>
                            <td>${item.quantity}</td>
                            <td>${formatNumber(item.unit_price)}</td>
                            <td>${formatNumber(item.total_price)}</td>
                        </tr>
                    `;
                    });
                    $('#importTable').html(tableHtml);

                    // // Lịch sử giao dịch
                    let historyHtml = '';
                    payments.forEach(payment => {
                        historyHtml += `
                        <li class="d-flex align-items-start mb-4">
                            <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${ dayjs(payment.date).format('DD/MM/YYYY')}</div>
                                <div>Thanh toán <span class="text-success fw-bold">${formatNumber(payment.amount)} USD</span></div>
                                <div class="text-muted small">Ghi chú: ${payment.note}</div>
                                <div class="text-muted small">Người tạo: <strong>${payment.employee.full_name}</strong></div>
                            </div>
                        </li>
                    `;
                    });
                    $('#transactionHistory').html(historyHtml);

                    $('#debtModal').modal('show');
                });

            })

            $(document).on('click', '.print-debt', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let url = `/admin/suppliers-debts/show/${id}?print=1`;

                $.get(url, function(html) {
                    const iframe = document.getElementById('print-frame');
                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(html);
                    doc.close();

                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                });
            });

            $(document).on('click', '.download-debt', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let url = `/admin/suppliers-debts/pdf/${id}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data, status, xhr) {
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let fileName = 'phieu_cong_no.pdf';

                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            fileName = disposition
                                .split('filename=')[1]
                                .replace(/['"]/g, '')
                                .trim();
                        }

                        const url = window.URL.createObjectURL(data);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    },
                    error: function() {
                        alert('Không thể tải PDF');
                    }
                });
            });

            $(document).on('click', '.make-payment', function(e) {
                e.preventDefault();
                const debtId = $(this).data('id');

                $('#paymentForm')[0].reset();
                $('#debt_id').val(debtId);

                // Gọi AJAX để lấy dữ liệu tổng tiền, đã thanh toán, còn nợ
                $.ajax({
                    url: `/admin/suppliers-debts/show/${debtId}`,
                    type: 'GET',
                    success: function(res) {

                        $('#supplier_debt_code').html(res.data.code)

                        $('#total_amount_text').text(formatNumber(res.data.total_amount) +
                            ' USD');
                        $('#paid_amount_text').text(formatNumber(res.data.paid_amount) +
                            ' USD');
                        $('#remaining_amount_text').text(formatNumber(res.data.total_amount -
                            res.data
                            .paid_amount) + ' USD');
                        $('#paymentModal').modal('show');
                    },
                    error: function() {
                        alert('Không thể lấy thông tin công nợ.');
                    }
                });
            });

            submitForm('#paymentForm', function(response) {

                console.log(response);

                $('#paymentModal').modal('hide');

                $('#myTable').DataTable().ajax.reload(null, false);

                Notifications(response.message, "success");

            }, '/admin/suppliers-debts/pay');
        })
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            margin-bottom: 2rem;
        }

        .stats-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%);
        }

        .card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }

        .card-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }

        .card-body {
            padding: 2rem;
            position: relative;
        }

        .card-icon {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 2.5rem;
            opacity: 0.3;
        }

       .stats-card .card-title {
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .card-amount {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-subtitle {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 0.5rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .card-amount {
                font-size: 1.8rem;
            }

            .card-icon {
                font-size: 2rem;
            }
        }

        .animate-number {
            animation: countUp 2s ease-out;
            color: #ffffff
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush
