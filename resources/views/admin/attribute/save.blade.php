@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'thương hiệu', 'url' => route('admin.attributes.index')],
                ['name' => $attribute ? $title . ' - ' . $attribute->name : $title],
            ]" />
        </div>


        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if ($attribute)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="name" class="form-label required">Tên thuộc tính</label>
                                        <input type="text" placeholder="Tên thuộc tính" aria-required="true" required
                                            class="form-control" name="name" id="name"
                                            value="{{ optional($attribute)->name }}">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Danh sách giá trị</h4>
                            <button type="button" class="btn btn-outline-light btn-sm text-dark border">Thêm giá
                                trị</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-stripe table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Giá trị</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Xóa</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($attribute->values as $value)
                                        <tr>
                                            <td width="5%">{{ $loop->iteration }}</td>
                                            <td><input type="text" name="values[{{ $value->id }}][value]"
                                                    id="value" class="form-control" value="{{ $value->value }}"></td>
                                            <td class="text-center">
                                                <label class="switch">
                                                    <input name="values[{{ $value->id }}][status]" type="checkbox"
                                                        @checked($value->status == 1)>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <a href="javascript:(0)"
                                                    class="remove-item text-decoration-none text-danger">
                                                    <svg class="icon svg-icon-ti-ti-trash"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M4 7l16 0"></path>
                                                        <path d="M10 11l0 6"></path>
                                                        <path d="M14 11l0 6"></path>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.attributes.index')])

                    <x-status :status="optional($attribute)->status" />
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#toggle-seo-fields').click(function() {
            $('.seo-edit-section').toggle(); // Ẩn/hiện các trường SEO
        });

        const inputIds = [{
            id: 'name',
            maxLength: 250
        }, ];

        $.each(inputIds, function(index, value) {
            updateCharCount(`#${value.id}`, value.maxLength);
        });

        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.attributes.index') }}"
            })

            let counter = 0; // Đếm số dòng hiện tại

            // Khi nhấn nút "Thêm giá trị"
            $('.btn-outline-light').click(function() {
                // Tạo một hàng mới
                let newRow = `
                    <tr>
                        <td width="5%">${counter + 1}</td>
                        <td><input type="text" name="values[${counter}][value]" id="value" class="form-control"></td>
                        <td class="text-center">
                            <label class="switch">
                                <input name="values[${counter}][status]" type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <a href="javascript:(0)" class="remove-item text-decoration-none text-danger">
                                <svg class="icon svg-icon-ti-ti-trash" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 7l16 0"></path>
                                    <path d="M10 11l0 6"></path>
                                    <path d="M14 11l0 6"></path>
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                `;

                // Thêm hàng mới vào bảng
                $('table tbody').append(newRow);
                counter++; // Tăng biến đếm để không bị trùng chỉ số
            });

            // Xóa hàng khi nhấn vào nút xóa
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                // Cập nhật lại số thứ tự cho các hàng còn lại
                $('table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });
        })
    </script>
@endpush
