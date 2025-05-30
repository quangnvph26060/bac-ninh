@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'chủ thể']]" />
        </div>

        <div class="row">
            <div class="col-lg-5">
                <form action="" method="post" id="myForm">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="text-uppercase card-title fw-bold" id="title-change">Thêm mới chủ thể</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <label for="title" class="form-label">Tên chủ thể</label>
                                    <input type="text" placeholder="Tên chủ thể" class="form-control" name="title"
                                        id="title">
                                </div>
                                <div class="mb-3 col-md-12">
                                    <div class="d-flex align-items-center gap-3">
                                        <label for="status" class="form-label mb-0">Trạng thái</label>
                                        <label class="switch">
                                            <input name="status" type="checkbox" checked value="1">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary">Lưu thay đổi</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="resetForm()">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="text-uppercase card-title fw-bold">danh sách chủ thể</h5>
                        <div class="card-tool">
                            <a href="#" class="btn btn-primary btn-sm fs-6"><i class="ti ti-circle-plus"></i> Thêm mới
                            </a>
                        </div>
                    </div>

                    <x-data-table file="subject" />
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
            $('input[name="status"]').prop('checked', true)
            $('#title-change').text('Thêm mới chủ thể')
            $('#myForm input[name="_method"]').remove();
            $('#myForm input[name="id"]').remove();
        }

        $(document).ready(function() {

            const api = "{{ route('admin.subjects.index') }}"
            dataTables(api, columns, 'Subject', {}, false, false)

            $(document).on('click', '.btn-operation-edit', function() {
                let $button = $(this);
                let $record = $button.data('record');
                console.log($record);

                let $id = $record.id;

                // Lấy thẻ <tr> cha gần nhất
                let $tr = $button.closest('tr');

                $('#myForm').attr('data-product-id', $id);

                $('#title-change').text(`Cập nhật chủ thể - ${$record.title}`)

                $(`input[name="title"]`).val($record.title)
                $(`input[name="status"]`).prop('checked', $record.status === 1)

                if ($('#myForm input[name="_method"]').length === 0) {
                    $('#myForm').append('<input type="hidden" name="_method" value="PUT">');
                }

                let $idInput = $('#myForm input[name="id"]');
                if ($idInput.length === 0) {
                    $('#myForm').append(`<input type="hidden" name="id" value="${$id}">`);
                } else {
                    $idInput.val($id);
                }

            });

            submitForm('#myForm', function(response) {

                resetForm()

                $('#myTable').DataTable().ajax.reload();

                Notifications(response.message, "success");

            })

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
