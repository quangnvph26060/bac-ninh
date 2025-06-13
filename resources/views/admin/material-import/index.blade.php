@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'phiếu nhập']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách phiếu nhập</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.material-imports.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Tạo phiếu nhập </a>
                </div>
            </div>

            <x-data-table file="material-import" />

        </div>
    </div>

    <div class="modal fade" id="modalMaterialImportDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Chi tiết nhập hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="materialImportContent">
                    <!-- Nội dung sẽ được render bằng JS sau khi call API -->
                    <div class="text-center">Đang tải...</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.material-imports.index') }}"
            dataTables(api, columns, 'MaterialImport', {}, false, true, true, true)

            $(document).on('click', '.download-debt', function(e) {
                e.preventDefault();

                let id = $(this).data('id');
                let url = `/admin/material-imports/pdf/${id}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data, status, xhr) {
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let fileName = 'phieu_nhap_kho.pdf';

                        // Nếu có Content-Disposition và chứa filename=
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            fileName = disposition
                                .split('filename=')[1]
                                .replace(/['"]/g, '')
                                .trim();
                        }

                        const blobUrl = window.URL.createObjectURL(data);
                        const a = document.createElement('a');
                        a.href = blobUrl;
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

            let lastMaterialImportId = null; // Biến lưu id lần trước

            $(document).on('click', '.btn-operation-show', function() {
                let id = $(this).data('id');

                // Nếu id giống id trước => chỉ cần hiển thị modal
                if (lastMaterialImportId === id) {
                    $('#modalMaterialImportDetail').modal('show');
                    return;
                }

                // Cập nhật id hiện tại
                lastMaterialImportId = id;

                // Hiển thị modal với trạng thái đang tải
                $('#modalMaterialImportDetail').modal('show');
                $('#materialImportContent').html(`<div class="text-center">Đang tải dữ liệu...</div>`);

                // Gọi API
                $.ajax({
                    url: `/admin/material-imports/show/${id}`,
                    method: 'GET',
                    success: function(res) {
                        $('#materialImportContent').html(renderMaterialImportDetail(res.data));
                    },
                    error: function() {
                        $('#materialImportContent').html(
                            `<div class="text-danger">Lỗi tải dữ liệu</div>`);
                    }
                });
            });

            function renderMaterialImportDetail(data) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Mã phiếu nhập:</strong> ${data.code}</p>
                            <p><strong>Ngày giao dịch:</strong> ${dayjs(data.date).format('DD/MM/YYYY')}</p>
                            <p><strong>Nhà cung cấp:</strong> ${data.supplier.company_name}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Người tạo:</strong> ${data.employee?.full_name ?? '---'}</p>
                            <p><strong>Ghi chú:</strong> ${data.note ?? '---'}</p>
                        </div>
                    </div>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã vật tư</th>
                                <th>Tên vật tư</th>
                                <th>Số lượng</th>
                                <th>Đơn vị</th>
                                <th>Đơn giá (USD)</th>
                                <th>Tổng tiền (USD)</th>
                            </tr>
                        </thead>
                        <tbody>`;

                data.details.forEach((item, index) => {
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.material.code}</td>
                            <td>${item.material.name}</td>
                            <td>${formatQuantity(item.quantity)}</td>
                            <td>${item.material.unit}</td>
                            <td>${formatNumber(item.unit_price)}</td>
                            <td>${formatNumber(item.total_price)}</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;

                return html;
            }
        })
    </script>
@endpush
