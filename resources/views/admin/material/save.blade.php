@extends('admin.layout.index')

@section('content')
    @if (empty($material))
        @php
            $material = null;
        @endphp
    @endif

    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'vật liệu', 'url' => route('admin.materials.index')],
                ['name' => $material ? $title . ' - ' . $material->name : $title],
            ]" />
        </div>

        <form action="" method="post" enctype="multipart/form-data" id="myForm">
            @csrf

            @if ($material)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-8">
                                        <label for="name" class="form-label ">Tên vật liệu</label>
                                        <input type="text" placeholder="Tên vật liệu" class="form-control" name="name"
                                            id="name" aria-="true" value="{{ optional($material)->name }}">
                                    </div>
                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="type" class="form-label">Loại vật liệu</label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="normal"
                                                {{ optional($material)->type == 'normal' ? 'selected' : '' }}>Loại thường
                                            </option>
                                            <option value="variant"
                                                {{ optional($material)->type == 'variant' ? 'selected' : '' }}>Biến thể
                                            </option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body" id="normal">
                                <div class="row">
                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="price_vnd" class="form-label">Giá (Vnđ)</label>
                                        <input type="text" placeholder="Giá bán" class="form-control format-price-vnd"
                                            name="price_vnd" id="price_vnd"
                                            value="{{ $material ? number_format(optional($material)->price_vnd, 0, '.', '.') : '' }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="price_usd" class="form-label">Giá ($)</label>
                                        <input type="text" placeholder="Giá bán" class="form-control format-price-usd"
                                            name="price_usd" id="price_usd"
                                            value="{{ $material ? number_format(optional($material)->price_usd, 2, '.', ',') : '' }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="stock" class="form-label">Tồn kho</label>
                                        <input type="text" placeholder="Tồn kho" class="form-control" name="stock"
                                            id="stock" value="{{ optional($material)->stock }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-body" id="variant" style="display: none;">
                                <div class="mb-3 position-relative col-md-12">
                                    <label for="attribute_ids" class="form-label">Thuộc tính</label>
                                    <select id="attribute-select" class="form-control w-100" multiple="multiple">
                                        @foreach ($attributes as $attributeId => $attributeName)
                                            <option value="{{ $attributeId }}" @selected(in_array($attributeId, $selectedAttributes ?? []))>
                                                {{ $attributeName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="accordion" id="accordionExample">
                                    @foreach ($materialAttributes ?? [] as $materialAttribute)
                                        {{-- @dd( $materialAttribute); --}}
                                        <div class="accordion-item" id="accordion-{{ $materialAttribute->attribute->id }}">
                                            <h2 class="accordion-header"
                                                id="heading-{{ $materialAttribute->attribute->id }}">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-{{ $materialAttribute->attribute->id }}"
                                                    aria-expanded="true"
                                                    aria-controls="collapse-{{ $materialAttribute->attribute->id }}">
                                                    <span class="fw-bold">{{ $materialAttribute->attribute->name }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $materialAttribute->attribute->id }}"
                                                class="accordion-collapse collapse show"
                                                aria-labelledby="heading-{{ $materialAttribute->attribute->id }}">
                                                <div class="accordion-body position-relative"
                                                    id="accordion-{{ $materialAttribute->attribute->id }}">
                                                    <label class="form-label">Giá trị</label>
                                                    <a href="javascript:void(0)" class="select-all position-absolute">Chọn
                                                        tất cả</a>
                                                    <select class="form-select select2 form-control"
                                                        name="attributes[{{ $materialAttribute->attribute->id }}][]"
                                                        id="select-{{ $materialAttribute->attribute->id }}" multiple>
                                                        @foreach ($materialAttribute->attribute->values as $attribute)
                                                            <option value="{{ $attribute->id }}"
                                                                @selected(in_array($attribute->id, $materialAttribute->attribute_values_ids ?? []))>
                                                                {{ $attribute->value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class=" position-relative col-md-3 my-3">
                                    <button type="button"
                                        class="btn  {{ empty($materialAttributes) ? 'btn-light text-dark' : 'btn-success' }} btn-sm"
                                        id="save-attributes" {{ empty($materialAttributes) ? 'disabled' : '' }}>
                                        Lưu thuộc tính
                                    </button>

                                </div>

                                <div class="accordion" id="variantAccordion">
                                    @foreach ($variants ?? [] as $index => $variantItem)
                                        <div class="accordion-item"
                                            data-variant-id="{{ $variantItem['attribute_value_combine'] }}">
                                            <h2 class="accordion-header">
                                                <button type="button"
                                                    class="accordion-button collapsed position-relative"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#v{{ $variantItem['attribute_value_combine'] }}">
                                                    <span>{{ $variantItem['sku'] }}</span>
                                                    <span class="ms-2 delete-variant text-danger position-absolute"
                                                        data-index="{{ $variantItem['attribute_value_combine'] }}">Xóa</span>
                                                </button>
                                            </h2>
                                            <div id="v{{ $variantItem['attribute_value_combine'] }}"
                                                class="accordion-collapse collapse">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        @foreach (explode('-', $variantItem['attribute_value_combine']) as $valueId)
                                                            <input type="hidden"
                                                                name="variants[{{ $index }}][attribute_value_ids][]"
                                                                value="{{ $valueId }}" />
                                                        @endforeach
                                                        <div class="mb-3 position-relative col-md-3">
                                                            <label for="variants-{{ $index }}-sku"
                                                                class="form-label ">Mã biến thể</label>
                                                            <input type="text" class="form-control"
                                                                id="variants-{{ $index }}-sku"
                                                                name="variants[{{ $index }}][sku]"
                                                                value="{{ $variantItem['sku'] }}">
                                                        </div>
                                                        <div class="mb-3 position-relative col-md-3">
                                                            <label for="variants-{{ $index }}-price"
                                                                class="form-label ">Giá</label>
                                                            <input type="text" class="form-control format-price"
                                                                id="variants-{{ $index }}-price"
                                                                name="variants[{{ $index }}][price]"
                                                                value="{{ number_format($variantItem['price'], 0, ',', '.') }}">
                                                        </div>
                                                        <div class="mb-3 position-relative col-md-3">
                                                            <label for="variants-{{ $index }}-product-unit"
                                                                class="form-label">Đơn vị</label>
                                                            <input type="text" class="form-control"
                                                                id="variants-{{ $index }}-product-unit"
                                                                name="variants[{{ $index }}][product_unit]"
                                                                value="{{ $variantItem['product_unit'] }}">
                                                        </div>
                                                        <div class="mb-3 position-relative col-md-3">
                                                            <label for="variants-{{ $index }}-stock"
                                                                class="form-label">Số lượng</label>
                                                            <input type="text" class="form-control"
                                                                id="variants-{{ $index }}-stock"
                                                                name="variants[{{ $index }}][stock]"
                                                                value="{{ $variantItem['stock'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.materials.index')])

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title fs-6 fw-bold required">Trạng thái
                            </h4>
                        </div>
                        <div class="card-body">
                            <select name="status" class="form-select form-control" id="status">
                                <option value="1" {{ optional($material)->status == 1 ? 'selected' : '' }}>Xuất bản
                                </option>
                                <option value="2" {{ optional($material)->status == 2 ? 'selected' : '' }}>Chưa xuất
                                    bản </option>
                            </select>
                        </div>
                    </div>

                    {{-- <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hình ảnh nổi bật</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_image"
                                style="cursor: pointer; width: 100%; height: 200px; object-fit: cover;"
                                src="{{ showImage(optional($material)->image) }}" alt=""
                                onclick="document.getElementById('image').click();">

                            <input type="file" name="image" id="image" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_image')">
                        </div>
                    </div> --}}

                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/image-uploader/image-uploader.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/tagify/tagify.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        let attributeNames = @json($attributes);
        $(document).ready(function() {

            $('[id^="select-"]').select2({
                placeholder: "Chọn thuộc tính",
                allowClear: true,
                width: '100%'
            });

            $(document).on('click', '.select-all', function() {
                let accordion = $(this).closest('.accordion-item');
                let selectElement = accordion.find('select');
                selectElement.find('option').prop('selected', true);
                selectElement.trigger('change'); // nếu có dùng Select2
            });





            $('#attribute-select').select2({
                placeholder: "Chọn thuộc tính",
                allowClear: true,
                width: '100%'
            });

            let accordionContainer = $('#accordionExample');


            // Hàm kiểm tra có giá trị nào được chọn hay không
            function checkIfAnyValueSelected() {
                let isValid = true;


                $('[id^="select-"]').each(function() {
                    if ($(this).val() === null || $(this).val().length === 0) {
                        isValid = false;
                        return false;
                    }
                });


                if (isValid) {
                    $('#save-attributes').prop('disabled', false)
                        .removeClass('btn-light text-dark')
                        .addClass('btn-success');
                } else {
                    $('#save-attributes').prop('disabled', true)
                        .removeClass('btn-success')
                        .addClass('btn-light text-dark');
                }
            }


            // Xử lý khi chọn thuộc tính
            $('#attribute-select').on('change', function() {
                let selectedAttributes = $(this).val() || [];

                $('.accordion-item').each(function() {
                    let accordionId = $(this).attr('id');

                    if (accordionId) {
                        let attrId = accordionId.replace('accordion-', '');

                        if (!selectedAttributes.includes(attrId)) {
                            $(this).remove();
                        }
                    }
                });

                // Thêm các accordion chưa có hoặc cập nhật lại nếu có
                selectedAttributes.forEach(attributeId => {
                    if (!$('#accordion-' + attributeId).length) {
                        let attributeName = attributeNames[attributeId] || "Không xác định";

                        $.ajax({
                            url: '{{ route('admin.products.selected.attributes', '__id__') }}'
                                .replace('__id__', attributeId),
                            method: 'GET',
                            success: function(response) {
                                let valuesArray = Object.entries(response).map(([id,
                                    name
                                ]) => ({
                                    id,
                                    name
                                }));

                                let valuesOptions = valuesArray.map(value =>
                                    `<option value="${value.id}">${value.name}</option>`
                                ).join('');

                                let accordionItem = `
                                    <div class="accordion-item" id="accordion-${attributeId}">
                                        <h2 class="accordion-header" id="heading-${attributeId}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse-${attributeId}" aria-expanded="true"
                                                    aria-controls="collapse-${attributeId}">
                                                <span class="fw-bold">${attributeName.toUpperCase()}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-${attributeId}" class="accordion-collapse collapse show"
                                            aria-labelledby="heading-${attributeId}">
                                            <div class="accordion-body position-relative">
                                                <label class="form-label">Giá trị</label>
                                                <a href="javascript:void(0)" class="select-all position-absolute">Chọn tất cả</a>
                                                <select class="form-select select2 form-control" name="attributes[${attributeId}][]" id="select-${attributeId}" multiple>
                                                    ${valuesOptions}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                accordionContainer.append(accordionItem);

                                // Initialize select2 for the new select element
                                $('#select-' + attributeId).select2({
                                    width: '100%',
                                    placeholder: "Chọn giá trị",
                                    allowClear: true
                                });

                                // Handle "select all" functionality
                                $('#accordion-' + attributeId).find('.select-all').on(
                                    'click',
                                    function() {
                                        let selectElement = $('#select-' +
                                            attributeId);
                                        selectElement.find('option').prop(
                                            'selected', true);
                                        selectElement.trigger('change');
                                    });

                                // Update when an option is changed
                                $('#select-' + attributeId).on('change', function() {
                                    checkIfAnyValueSelected();
                                });

                                // Nếu có dữ liệu cũ từ chỉnh sửa, đánh dấu chọn
                                if (selectedAttributes && selectedAttributes[
                                        attributeId]) {
                                    selectedAttributes[attributeId].forEach(
                                        selectedValueId => {
                                            $('#select-' + attributeId).find(
                                                `option[value="${selectedValueId}"]`
                                            ).prop('selected', true);
                                        });
                                    $('#select-' + attributeId).trigger(
                                        'change'); // Cập nhật lại select2
                                }

                                checkIfAnyValueSelected();
                            },
                            error: function() {
                                console.log('Lỗi khi lấy dữ liệu thuộc tính: ' +
                                    attributeId);
                            }
                        });
                    }
                });

                checkIfAnyValueSelected();
            });

            $('#save-attributes').on('click', function() {
                let groupedAttributes = {};

                // Lấy dữ liệu từ các select và nhóm lại theo attributeId
                $('[id^="select-"]').each(function() {
                    let attributeId = $(this).attr('id').replace('select-', '');
                    let selectedOptions = $(this).find('option:selected');

                    if (selectedOptions.length > 0) {
                        groupedAttributes[attributeId] = [];

                        selectedOptions.each(function() {
                            let valueId = $(this).val();
                            let valueName = $(this).text();

                            groupedAttributes[attributeId].push({
                                value_id: valueId,
                                value_name: valueName
                            });
                        });
                    }
                });

                const attributeArrays = Object.values(groupedAttributes);

                // Hàm tạo các kết hợp thuộc tính
                function cartesianMaterial(arrays, index = 0, current = [], result = []) {
                    if (index === arrays.length) {
                        result.push({
                            attribute_value_ids: current.map(i => Number(i.value_id)),
                            value_name: current.map(i => i.value_name.trim()).join('-')
                        });
                        return;
                    }

                    for (let item of arrays[index]) {
                        cartesianMaterial(arrays, index + 1, [...current, item], result);
                    }

                    return result;
                }

                const combinations = cartesianMaterial(attributeArrays);
                console.log(combinations);
                const accordion = $('#variantAccordion');

                // Lấy các variant cũ từ accordion
                // $('.accordion-item').each(function() {
                //     existingVariants.push($(this).data('variant-id'));
                // });

                // Xóa tất cả dữ liệu cũ trong accordion
                accordion.empty();
                const existingVariants = @json($variants ?? []);
                console.log(existingVariants);
                const variantMap = new Map();
                existingVariants.forEach(item => {
                    variantMap.set(item.attribute_value_combine, item);
                });

                combinations.forEach((variant, index) => {
                    const idStr = variant.attribute_value_ids.join('-');
                    const oldVariant = variantMap.get(idStr);

                    const html = `
                        <div class="accordion-item" data-variant-id="${idStr}">
                            <h2 class="accordion-header">
                                <button type="button" class="accordion-button collapsed position-relative"
                                        data-bs-toggle="collapse" data-bs-target="#v${idStr}">
                                    <span>${oldVariant?.sku ?? variant.value_name}</span>
                                    <span class="ms-2 delete-variant text-danger position-absolute" data-index="${idStr}">Xóa</span>
                                </button>
                            </h2>
                            <div id="v${idStr}" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <div class="row">
                                        ${variant.attribute_value_ids.map(valueId => `
                                                                                            <input type="hidden" name="variants[${index}][attribute_value_ids][]" value="${valueId}" />
                                                                                        `).join('')}

                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-sku" class="form-label ">Mã sản phẩm</label>
                                            <input type="text" class="form-control" id="variants-${index}-sku" name="variants[${index}][sku]"  value="${oldVariant?.sku ?? variant.value_name}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-sale-price" class="form-label ">Giá</label>
                                            <input type="text" class="form-control format-price" id="variants-${index}-sale-price" name="variants[${index}][price]"  value="${oldVariant?.price ?? ''}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-product-unit" class="form-label">Đơn vị</label>
                                            <input type="text" class="form-control" id="variants-${index}-product-unit" name="variants[${index}][product_unit]" value="${oldVariant?.product_unit ?? ''}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-stock" class="form-label">Số lượng</label>
                                            <input type="text" class="form-control" id="variants-${index}-stock" name="variants[${index}][stock]"  value="${oldVariant?.stock ?? ''}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        `;
                    accordion.append(html);
                });

            });


            $('#variantAccordion').on('click', '.delete-variant', function(e) {
                e.stopPropagation();
                var variantId = $(this).data('index');
                console.log('Xóa variant có ID: ', variantId);
                $(this).closest('.accordion-item').remove();
            });

            $('#type').on('change', function() {
                if ($(this).val() === 'normal') {
                    $('#normal').show();
                    $('#variant').hide();
                } else {
                    $('#normal').hide();
                    $('#variant').show();
                }
            });

            // Tự động hiển thị đúng nội dung nếu có sẵn giá trị
            $('#type').trigger('change');



            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.materials.index') }}"
            })


        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceInputs = document.querySelectorAll('.format-price, .format-price-vnd');

            priceInputs.forEach(function(input) {

                input.addEventListener('input', function(e) {

                    let value = e.target.value;
                    value = value.replace(/[^0-9]/g, '');
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    e.target.value = value;
                });
            });


            const priceInput = document.getElementById('price_usd');

            if (priceInput) {
                priceInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/[^0-9.]/g, '');

                    const parts = value.split('.');
                    const integerPart = parts[0];
                    const decimalPart = parts[1] ? '.' + parts[1].replace(/\./g, '') : '';

                    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                    e.target.value = formattedInteger + decimalPart;
                });
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/image-uploader.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/tagify.css') }}">

    <style>
        .list-group-item {
            display: block !important;
        }

        .delete-variant {
            right: 35px;
        }
    </style>
@endpush
