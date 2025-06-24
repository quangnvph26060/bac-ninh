@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'Vật thể trong sản phẩm']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách vật thể trong sản phẩm</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.boms.create') }}" class="btn btn-primary btn-sm fs-6"><i class="ti ti-circle-plus"></i> Thêm mới </a>
                </div>
            </div>

            <x-data-table file="bom" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.boms.index') }}"
            dataTables(api, columns, 'Bom')
            $(document).on('click', '.btn-boms-destroy', function(e) {
                e.preventDefault();
                const productableId = $(this).data('id');
                Swal.fire({
                    title: 'Bạn có chắc muốn xoá?',
                    text: "Dữ liệu này sẽ không thể khôi phục!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Đồng ý',
                    cancelButtonText: 'Huỷ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gửi AJAX để xoá
                        $.ajax({
                            url: '/admin/boms/' + productableId,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                $.notify({
                                    icon: "icon-bell",
                                    title: "Thông báo",
                                    message: "Xóa thành công.",
                                }, {
                                    type: "success",
                                    placement: {
                                        from: "bottom",
                                        align: "right",
                                    },
                                    time: 1000,
                                });
                                $('#myTable').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Lỗi!',
                                    'Không thể xoá. Vui lòng thử lại.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        })
    </script>
@endpush
