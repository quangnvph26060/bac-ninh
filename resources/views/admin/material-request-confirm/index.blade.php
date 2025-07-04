@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'yêu cầu xuất vật tư']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách yêu cầu xuất vật tư</h5>

            </div>

            <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                <form id="formCancelModal">
                    {{-- @csrf --}}
                    <div class="modal-dialog  modal-dialog-centered">
                        <div class="modal-content">
                            <input type="hidden" name="order_id" id="order_id">
                            <div class="modal-header ">
                                <h5 class="modal-title" id="cancelModalLabel">Hủy yêu cầu</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="cancelNote" class="form-label">Lý do hủy yêu cầu:</label>
                                    <textarea name="note" id="cancelNote" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-sm"> Xác nhận hủy</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                                    Thoát</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <x-data-table file="material-request" />

        </div>
    </div>

    <div class="modal fade" id="requestDetailModal" tabindex="-1" aria-labelledby="requestDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-medium">Chi tiết yêu cầu vật tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <!-- Nội dung chi tiết sẽ được load ở đây -->
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div>Đang tải dữ liệu...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 CDN nếu chưa có -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(function() {
            const api = "{{ route('admin.material-requests.indexConfirm') }}"
            dataTables(api, columns, 'MaterialRequest', {}, false, true, false, true)

            $('#formCancelModal').on('submit', function(e) {
                e.preventDefault();
                var order_id = $('#order_id').val();
                var form = $(this);
                var formData = form.serialize();
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: '{{ route('admin.material-requests.cancelConfirm', ':id') }}'.replace(
                        ':id', order_id),
                    data: formData,
                    success: function(res) {
                        $('#cancelModal').modal('hide');

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Đã hủy đơn!',
                                text: res.message,
                            }).then(() => {
                                $('#myTable').DataTable().ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: res.message || 'Không thể hủy đơn hàng.'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi server',
                            text: xhr.responseJSON?.message || 'Đã xảy ra lỗi.'
                        });
                    }
                });
            });


        })

        function openCancelModal(orderId) {
            const order_id = document.getElementById('order_id');
            order_id.value = orderId;
            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }

        function viewRequestDetails(id) {
            // Sử dụng AJAX hoặc Alpine/Livewire/Vue để load chi tiết
            // Hoặc đơn giản:
            $('#requestDetailModal').modal('show');

            // Load dữ liệu:
            $.ajax({
                url: `/admin/material-requests/show-detail/${id}`,
                method: 'GET',
                success: function(response) {
                    const data = response.data;
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Mã vật tư</th>
                                        <th>Tên vật tư</th>
                                        <th>Đơn vị</th>
                                        <th>Số lượng yêu cầu</th>
                                        <th>Số lượng tồn</th>
                                        <th>Thông báo</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.forEach((item, index) => {
                        const requiredQty = parseFloat(item.quantity);
                        const availableQty = item.material.inventory ? parseFloat(item.material
                            .inventory.quantity) : 0;

                        let notice = '';
                        if (availableQty < requiredQty) {
                            notice = `<span class="text-danger">không hợp lệ</span>`;
                        } else {
                            notice = `<span class="text-success">hợp lệ</span>`;
                        }

                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.material.code}</td>
                                <td>${item.material.name}</td>
                                <td>${item.material.unit}</td>
                                <td>${requiredQty.toLocaleString()}</td>
                                <td>${availableQty.toLocaleString()}</td>
                                <td>${notice}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    $('#requestDetailModal .modal-body').html(html);
                },

                error: function() {
                    alert('Không thể tải chi tiết yêu cầu.');
                }
            });
        }

        function confirmOrder(orderId) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Xác nhận đơn hàng này?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: ' Xác nhận',
                cancelButtonText: ' Hủy bỏ',
                customClass: {
                    confirmButton: 'swal-confirm-sm',
                    cancelButton: 'swal-cancel-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.material-requests.approvedConfirm', ':id') }}'.replace(':id',
                            orderId),
                        type: 'POST',

                        success: function(data) {
                            if (data.success) {
                                Swal.fire('Thành công!', data.message, 'success').then(
                                    () => {

                                        $('#myTable').DataTable().ajax.reload(null, false);

                                    });
                            } else {
                                Swal.fire('Lỗi!', data.message || 'Không thể xác nhận.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
