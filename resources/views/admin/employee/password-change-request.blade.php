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

            Swal.fire({
                title: "Bạn có chắc chắn không?",
                text: "Hành động này sẽ không thể hoàn tác!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Đồng ý, từ chối!",
                cancelButtonText: "Hủy",
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        });
    </script>
@endpush
