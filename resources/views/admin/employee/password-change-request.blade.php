@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'yêu cầu đổi mật khẩu']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách yêu cầu đổi mật khẩu</h5>
            </div>

            <x-data-table file="password-change-request" />

        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Nhập lý do từ chối</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="rejectId" name="id">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Lý do từ chối</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger btn-sm" id="submitReject">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.password-change-request.index') }}"
            dataTables(api, columns, 'PasswordChangeRequest', {}, false, false, false)
        })

        $(document).on('click', '.btn-confirm', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: "Bạn có chắc chắn không?",
                text: "Hành động này sẽ không thể hoàn tác!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Đồng ý, xác nhận!",
                cancelButtonText: "Hủy",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.password-change-request.confirm') }}",
                        type: 'POST',
                        data: {
                            id
                        },
                        beforeSend: function() {
                            $("#loadingSpinner").fadeIn();
                        },
                        success: function(response) {
                            $('#myTable').DataTable().ajax.reload();

                            Notifications(response.message, 'success');
                        },
                        error: function(xhr) {
                            if (
                                xhr.status === 403 &&
                                xhr.getResponseHeader("Content-Type").includes(
                                    "text/html")
                            ) {
                                document.open();
                                document.write(xhr.responseText);
                                document.close();
                                return
                            }
                            Notifications(xhr.responseJSON.message, 'danger');
                        },
                        complete: function() {
                            $("#loadingSpinner").fadeOut();
                        }
                    })
                }
            });
        });

        $(document).on('click', '.btn-reject', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#rejectId').val(id);
            $('#rejectModal').modal('show');
        });

        $('#submitReject').on('click', function() {
            const id = $('#rejectId').val();
            const reason = $('#reason').val();

            if (!reason) {
                Notifications('Vui lòng nhập lý do từ chối', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('admin.password-change-request.reject') }}",
                type: 'POST',
                data: {
                    id,
                    reason
                },
                beforeSend: function() {
                    $("#loadingSpinner").fadeIn();
                },
                success: function(response) {
                    $('#rejectModal').modal('hide');
                    $('#myTable').DataTable().ajax.reload();
                    $('#reason').val('');
                    Notifications(response.message, 'success');
                },
                error: function(xhr) {
                    if (
                        xhr.status === 403 &&
                        xhr.getResponseHeader("Content-Type").includes("text/html")
                    ) {
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                        return;
                    }
                    Notifications(xhr.responseJSON.message, 'danger');
                    $('#rejectModal').modal('hide');

                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                }
            });
        });
    </script>
@endpush
