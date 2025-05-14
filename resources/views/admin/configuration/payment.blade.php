@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'cấu hình thanh toán']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách thương hiệu</h5>
                <div class="card-tool">
                    <button class="btn btn-primary btn-sm fs-6"><i class="ti ti-circle-plus"></i> Thêm mới </button>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%">ID</th>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th class="text-center" style="width: 10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($configPayments as $configPayment)
                            <tr>
                                <td>{{ $configPayment->id }}</td>
                                <td><img src="{{ showImage($configPayment->image) }}" alt=""
                                        style="object-fit: cover;"></td>
                                <td>{{ $configPayment->title }}</td>
                                <td>
                                    <button type="button" data-content="{{ $configPayment->content }}"
                                        class="btn btn-primary btn-sm table-actions btn-operation-show">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input name="status" type="checkbox" value="1" @checked($configPayment->status == 1)
                                            data-id="{{ $configPayment->id }}">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <button type="button" data-id="{{ $configPayment->id }}"
                                        class="btn btn-primary btn-sm table-actions btn-operation-edit">
                                        <i class="ti ti-edit"></i>
                                    </button>

                                    <button type="button" data-id="{{ $configPayment->id }}"
                                        class="btn btn-danger btn-sm table-actions btn-operation-destroy">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm mới cấu hình thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="myForm">
                    <input type="hidden" name="id" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control ckeditor" name="content" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ảnh</label>
                            <img class="img-thumbnail" id="show_image"
                                style="cursor: pointer; width: 25%; height: auto; object-fit: cover;"
                                src="{{ showImage('') }}" alt=""
                                onclick="document.getElementById('image').click();">

                            <input type="file" name="image" id="image" class="form-control d-none" accept="image/*"
                                onchange="previewImage(event, 'show_image')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal View Content -->
    <div class="modal fade" id="viewContentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Nội dung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre><code id="content" class="language-json"></code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        $(document).on('click', '.btn-operation-destroy', function() {
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
                        url: "{{ route('admin.configurations.destroy.config.payment') }}",
                        type: "DELETE",
                        data: {
                            id: $(this).data('id')
                        },
                        beforeSend: function() {
                            $("#loadingSpinner").fadeIn();
                        },
                        success: function(response) {
                            loadAjaxTable(response.data);
                            Notifications(response.message, "success");
                        },
                        error: function(response) {
                            Notifications(response.message, "error");
                        },
                        complete: function() {
                            $("#loadingSpinner").fadeOut();
                        }
                    })
                }
            });
        })

        $(document).on('click', '.btn-operation-show', function() {
            const content = $(this).data('content');
            $('#content').html(content);
            $('#viewContentModal').modal('show');
        })

        $(document).on('change', '.switch', function() {
            const $checkbox = $(this);
            const originalState = $checkbox.find('input').prop('checked');
            const id = $checkbox.find('input').data('id');

            const status = originalState ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.configurations.update.config.payment.status') }}",
                type: "PUT",
                data: {
                    id: id,
                    status: status
                },
                beforeSend: function() {
                    $("#loadingSpinner").fadeIn();
                },
                success: function(response) {
                    Notifications(response.message, "success");
                },
                error: function(response) {
                    $checkbox.prop('checked', !originalState);
                    Notifications(response.message, "error");
                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                }
            })
        })

        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                Notifications(response.message, "success");
                $('#createModal').modal('hide');
                loadAjaxTable(response.data);
            })
        })

        $(document).on('click', '.btn-operation-edit', function() {
            const id = $(this).data('id');
            $('#myForm').find('input[name="id"]').val(id);

            $.ajax({
                url: "{{ route('admin.configurations.get.config.payment', ['id' => ':id']) }}".replace(
                    ':id', id),
                type: "GET",
                beforeSend: function() {
                    $("#loadingSpinner").fadeIn();
                },
                success: function(response) {
                    const {
                        id,
                        image,
                        title,
                        content
                    } = response.data;

                    $('#myForm').find('input[name="title"]').val(title);
                    CKEDITOR.instances.content.setData(content);
                    $('#myForm').find('img#show_image').attr('src', image);

                    $('#createModal').modal('show');
                },
                error: function(response) {
                    Notifications(response.message, "error");
                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                }
            })

            $('#createModal').modal('show');
        })

        function loadAjaxTable(data) {
            let _html = '';

            if (data.length === 0) {
                _html = `<tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>`;
            } else {
                $.each(data, function(key, value) {

                    _html += `
                    <tr>
                        <td>${value.id}</td>
                        <td><img src="${value.image}" alt="" style=" object-fit: cover;"></td>
                        <td>${value.title}</td>
                        <td>
                            <button type="button" data-content='${value.content}'
                                class="btn btn-primary btn-sm table-actions btn-operation-show">
                                <i class="ti ti-eye"></i>
                            </button>
                        </td>
                        <td>
                            <label class="switch">
                                <input name="status" type="checkbox" value="1" ${value.status == 1 ? 'checked' : ''} data-id="${value.id}">
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <button type="button" data-id="${value.id}" class="btn btn-primary btn-sm table-actions btn-operation-edit">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button type="button" data-id="${value.id}" class="btn btn-danger btn-sm table-actions btn-operation-destroy">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }

            $('tbody').html(_html);
        }

        $(document).on('click', '.btn-primary.btn-sm.fs-6', function() {
            $('#myForm').find('input[name="id"]').val('');
            $('#myForm')[0].reset();
            $('#createModal').modal('show');
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
    <style>
        pre {
            max-height: 400px;
            overflow-y: auto;
            background-color: #2d2d2d !important;
            color: #ccc;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
@endpush
