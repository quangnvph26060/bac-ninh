@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'quyền hạn']]" />
        </div>

        <div class="row">
            <div class="col-lg-5">
                <form action="" method="post" id="myForm">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="text-uppercase card-title fw-bold" id="title-change">Thêm mới quyền</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="vi_name" class="form-label">Tên quyền tiếng việt</label>
                                    <input type="text" placeholder="Tên quyền tiếng việt" class="form-control"
                                        name="vi_name" id="vi_name">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Tên quyền</label>
                                    <input type="text" placeholder="Tên quyền" class="form-control" name="name"
                                        id="name">
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="group_name" class="form-label">Nhóm quyền</label>
                                    <input type="text" placeholder="Nhóm quyền" class="form-control" name="group_name"
                                        id="group_name" list="group_name_list">
                                    <datalist id="group_name_list">
                                        @foreach ($groupNames as $groupName)
                                            <option value="{{ $groupName }}">{{ $groupName }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary">Lưu thay đổi</button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="resetForm()">Clean</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-uppercase card-title fw-bold">danh sách quyền</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên quyền tiếng việt</th>
                                        <th>Tên quyền</th>
                                        <th>Nhóm quyền</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script>
        function resetForm() {
            $('#myForm')[0].reset();
            $('#myForm').removeAttr('data-product-id');
            $('#title-change').text('Thêm mới quyền')
        }

        $(document).ready(function() {
            let table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.permissions.index') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        width: '5%'
                    },
                    {
                        data: 'vi_name',
                        name: 'vi_name',

                    },
                    {
                        data: 'name',
                        name: 'name',
                        className: 'target-name'
                    },
                    {
                        data: 'group_name',
                        name: 'group_name',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false,
                        width: '25%'
                    },
                    {
                        data: 'operations',
                        name: 'operations',
                        orderable: false,
                        searchable: false,
                        width: '12%'
                    }
                ],
                order: [],
                language: {
                    url: '/backend/assets/js/plugin/datatables/vi.json'
                }
            });

            $(document).on("click", ".btn-operation-destroy", function() {
                let id = $(this).data("id");
                let pageInfo = table.page.info();

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
                            url: '{{ route('admin.permissions.destroy', '__id__') }}'
                                .replace(
                                    '__id__', id),
                            type: "DELETE",
                            data: {
                                id
                            },
                            success: function(response) {
                                table.ajax.reload(function() {
                                    let newPageInfo = table.page.info();

                                    // Nếu trang hiện tại vẫn còn dữ liệu, giữ nguyên
                                    if (pageInfo.page < newPageInfo.pages) {
                                        table.page(pageInfo.page).draw(false);
                                    } else {
                                        // Nếu không còn dữ liệu ở trang hiện tại, quay về trang trước đó
                                        table
                                            .page(Math.max(pageInfo.page - 1,
                                                0))
                                            .draw(false);
                                    }
                                }, false);

                                Notifications(response.message, "success");
                                resetForm()
                            },
                            error: function(xhr) {
                                console.log(xhr);
                                Notifications(xhr.responseJSON.message, "danger");
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.btn-operation-edit', function() {
                let $button = $(this);
                let $record = $button.data('record');

                let $id = $record.id;

                // Lấy thẻ <tr> cha gần nhất
                let $tr = $button.closest('tr');

                // Lấy text của phần tử có class .target-name trong <tr> đó
                let targetName = $tr.find('.target-name').text().trim();

                $('#myForm').attr('data-product-id', $id);

                $('#title-change').text(`Cập nhật quyền - ${targetName}`)

                $(`input[name="name"]`).val(targetName)
                $(`input[name="group_name"]`).val($record.group_name)
                $(`input[name="vi_name"]`).val($record.vi_name)

            });


            $('#myForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serializeArray()
                let id = $(this).attr('data-product-id') || null;

                $.ajax({
                    url: id ?
                        '{{ route('admin.permissions.save', '__id__') }}'.replace('__id__', id) :
                        '{{ route('admin.permissions.save') }}',
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $("#loadingSpinner").fadeIn();
                    },
                    success: function(response) {
                        table.ajax.reload();
                        Notifications(response.message, "success");
                        resetForm()
                    },
                    error: function(xhr) {
                        Notifications(xhr.responseJSON.message, "danger");
                    },
                    complete: function() {
                        $("#loadingSpinner").fadeOut();
                    },
                })
            })

            // $('.btn-reset').click(function() {
            //     $('#myForm')[0].reset();
            // })

        })
    </script>
@endpush


@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/dataTables.min.css') }}">

    <style>
        #dt-length-0 {
            margin-right: 10px
        }
    </style>
@endpush
