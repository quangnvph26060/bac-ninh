@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'Nguyên vật liệu', 'url' => route('admin.materials.index')],
                    ['name' => !empty($material) ? "Cập nhật nguyên vật liệu - $material->name" : 'Nhập vật liệu'],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>

        <form action="{{ route('admin.materials.store') }}" method="post" id="myForm">

            <div class="row">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">

                            {{-- Chọn nguyên vật liệu --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên nguyên vật liệu</label>
                                <select name="name" id="material" class="form-control select2" required>
                                    <option value="">Vật liệu</option>
                                    @foreach ($names as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chọn loại --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Loại</label>
                                <select id="type" class="form-control select2" multiple="multiple" required>
                                    @foreach ($types as $typeName)
                                        <option value="{{ $typeName }}">{{ $typeName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="import-table" style="display:none">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Loại</th>
                                        <th>Đơn giá (USD)</th>
                                        <th style="width: 10%">Số lượng</th>
                                        <th style="width: 15%">Đơn vị</th>
                                        <th>Nhà cung cấp</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="import-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    @include('admin.components.button', ['redirect' => route('admin.materials.index')])

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Mã nhập</h4>
                        </div>
                        <div class="card-body">
                            <input class="form-control" type="text" name="import_code" value="">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thời gian</h4>
                        </div>
                        <div class="card-body">
                            <input class="form-control" type="text" readonly
                                value="{{ \Carbon\Carbon::now()->translatedFormat('l, \N\g\à\y d \T\h\á\n\g m \N\ă\m Y') }}">
                        </div>
                    </div>


                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        const suppliers = @json($suppliers);

        $(document).ready(function() {
            // Khởi tạo Select2 cho material
            $('#material').select2({
                placeholder: "Chọn nguyên vật liệu",
                allowClear: true,
                tags: true,
            });

            // Khởi tạo Select2 cho type với tags
            $('#type').select2({
                placeholder: "Chọn hoặc nhập loại",
                tags: true,
                allowClear: true,
                multiple: true,
                createTag: function(params) {
                    let term = $.trim(params.term);
                    if (term === '') return null;
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('#type').on('change', function() {
                const selectedTypes = $(this).val() || [];
                const $tbody = $('#import-table-body');

                // 1. Lưu dữ liệu hiện tại theo type_name
                let oldData = {};
                $tbody.find('tr').each(function() {
                    const typeName = $(this).data('type');

                    oldData[typeName] = {
                        price: $(this).find('input[name*="[price]"]').val(),
                        quantity: $(this).find('input[name*="[quantity]"]').val(),
                        unit: $(this).find('input[name*="[unit]"]').val(),
                    };
                });

                $tbody.empty();

                if (selectedTypes.length === 0) {
                    $('#import-table').hide();
                    return;
                }

                const supplierOptions = Object.values(suppliers).map(supplier =>
                    `<option value="${supplier}">${supplier}</option>`
                ).join('');


                selectedTypes.forEach((typeId, index) => {
                    const stt = index + 1;

                    // Lấy text tương ứng với ID từ option
                    const typeName = $('#type option[value="' + typeId + '"]').text();

                    const data = oldData[typeName] || {
                        price: '',
                        quantity: '',
                        unit: ''
                    };

                    const row = `
                        <tr data-type="${typeName}">
                            <td>${stt}</td>
                            <td><input type="hidden" name="data[${stt}][type_name]" value="${typeName}">${typeName}</td>
                            <td><input type="number" name="data[${stt}][price]" class="form-control" value="${data.price}" required></td>
                            <td><input type="number" name="data[${stt}][quantity]" class="form-control" value="${data.quantity}" required></td>
                            <td><input type="text" name="data[${stt}][unit]" class="form-control" value="${data.unit}"></td>
                            <td>
                                <select name="data[${stt}][supplier_name]" class="form-select supplier-select" required>
                                    <option value="">Chọn</option>
                                    ${supplierOptions}
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-row" title="Xóa">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;


                    $tbody.append(row);

                    $tbody.find('.supplier-select').select2({
                        placeholder: 'Chọn nhà cung cấp',
                        width: '100%'
                    });
                });

                $('#import-table').show();
            });

            $(document).on('click', '.btn-remove-row', function() {
                const $tr = $(this).closest('tr');
                const typeToRemove = $tr.data('type'); // text hiển thị của loại (VD: "Gạch")

                $tr.remove();

                let selected = $('#type').val() || [];

                // Xoá phần tử có text trùng với typeToRemove
                selected = selected.filter(function(val) {
                    const optionText = $('#type option[value="' + val + '"]').text() || val;
                    return optionText !== typeToRemove;
                });

                $('#type').val(selected).trigger('change');

                if ($('#import-table-body tr').length === 0) {
                    $('#import-table').hide();
                }
            });


            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.materials.index') }}"
            })
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
