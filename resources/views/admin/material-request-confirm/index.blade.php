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
                                <button type="submit" class="btn btn-danger btn-sm">✅ Xác nhận hủy</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">❌
                                    Thoát</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <x-data-table file="material-request" />

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

        function confirmOrder(orderId) {
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Xác nhận đơn hàng này?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '✅ Xác nhận',
                    cancelButtonText: '❌ Hủy bỏ',
                    customClass: {
                        confirmButton: 'swal-confirm-sm',
                        cancelButton: 'swal-cancel-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.material-requests.approvedConfirm', ':id') }}'.replace(':id', orderId),
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
