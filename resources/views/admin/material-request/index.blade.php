@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'yêu cầu xuất vật tư']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách yêu cầu xuất vật tư</h5>
                <div class="card-tool ">
                    <a href="{{ route('admin.material-requests.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Tạo yêu cầu xuất vật tư </a>
                </div>
            </div>

            <x-data-table file="material-request" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "{{ route('admin.material-requests.index') }}"
            dataTables(api, columns, 'MaterialRequest', {}, false, true, false, true)

            $(document).on('click', '.handle-delete', function() {
                const id = $(this).data('id')

                Swal.fire({
                    title: "Bạn có chắc chắn muốn xóa?",
                    text: "Hành động này sẽ không thể hoàn tác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Đồng ý, xóa!",
                    cancelButtonText: "Hủy",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/material-requests/destroy/${id}`,
                            type: "DELETE",
                            success: function(response) {
                                $('#myTable').DataTable().ajax.reload();
                                Notifications(response.message, "success");
                            },
                            error: function(xhr) {
                                Notifications(xhr.responseJSON.message, "danger");
                            },
                        });
                    }
                });

            })
        })
    </script>
@endpush
