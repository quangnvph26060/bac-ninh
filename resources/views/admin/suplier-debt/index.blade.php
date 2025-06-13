@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'công nợ']]" />
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Tổng công nợ</h6>
                        <h3 class="fw-bold text-white">{{ formatPrice($totalDebt) }} USD</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Đã thanh toán</h6>
                        <h3 class="fw-bold text-white">{{ formatPrice($totalPaid) }} USD</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Còn lại</h6>
                        <h3 class="fw-bold text-white">{{ formatPrice($totalRemain) }} USD</h3>
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
                            Thông tin công nợ
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
                            Thông tin phiếu nhập
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
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
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

            $(document).on('click', '.show-modal', function(e) {
                e.preventDefault();

                let supplierDebtId = $(this).data('id');

                $.get('/admin/suppliers-debts/show/' + supplierDebtId, function(response) {

                    const details = response.data.import.details;
                    const payments = response.data.import.debt.payments;

                    // render thông tin ra các tab
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
