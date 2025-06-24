@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'Boms', 'url' => route('admin.boms.index')], ['name' => $title]]" />
        </div>

        <form action="" method="post" id="myForm" enctype="multipart/form-data">

            @if (!empty($bom))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 position-relative col-md-12">
                                    <label for="product_id" class="form-label required">Tên sản phẩm</label>
                                    <select name="product_id" id="product_id" class="form-control select2" disabled>
                                        {{ isset($bom) ? 'disabled' : '' }}>
                                        @if (isset($bom))
                                            @php
                                                $product =
                                                    $bom->productable_type === \App\Models\Product::class
                                                        ? \App\Models\Product::find($bom->productable_id)
                                                        : \App\Models\Product::find(
                                                            optional(optional($bom->productable)->product)->id,
                                                        );
                                            @endphp
                                            @if ($product)
                                                <option value="{{ $product->id }}" selected>{{ $product->name }}
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="variant_wrapper"
                        class="card mt-3 {{ $bom && $bom->productable_type === 'App\Models\ProductVariant' ? '' : 'd-none' }}">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="variant_id" class="form-label required">Chọn biến thể</label>
                                <select name="variant_id" id="variant_id" class="form-control select2">
                                    {{ $bom->productable_type === 'App\Models\ProductVariant' ? '' : 'disabled d-none' }}>
                                    @if ($bom->productable_type === 'App\Models\ProductVariant')
                                        @php
                                            $variant = \App\Models\ProductVariant::find($bom->productable_id);
                                        @endphp
                                        @if ($variant)
                                            <option value="{{ $variant->id }}" selected>SKU: {{ $variant->sku }}
                                            </option>
                                        @endif
                                    @endif
                                </select>
                                @if (isset($variant))
                                    <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Danh sách vật liệu</h4>
                            <button type="button" class="btn btn-outline-light btn-sm text-dark border"
                                id="add-new-row">Thêm vật
                                liệu</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-stripe table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Vật liệu</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $counter = 0; @endphp
                                    @foreach ($bom->bomItems as $index => $item)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td>
                                                <select name="values[{{ $index }}][material_id]"
                                                    class="form-control material-select select2">
                                                    <option value="">Chọn vật liệu</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}"
                                                            {{ $item->material_id == $material->id ? 'selected' : '' }}>
                                                            {{ $material->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="text" name="values[{{ $index }}][quantity_required]"
                                                    class="form-control"
                                                    value="{{ number_format($item->quantity_required, 0) }}">
                                            </td>
                                            <td class="text-center">
                                                <a href="javascript:void(0)"
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
                                        @php $counter++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.boms.index')])
                </div>
                
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            @if (isset($bom))
                $('#product_id').val(
                    '{{ $bom->productable_type === 'App\\Models\\ProductVariant' ? $variant->product_id : $bom->productable_id }}'
                ).trigger('change');

                @if ($bom->productable_type === 'App\\Models\\ProductVariant')
                    $('#variant_wrapper').removeClass('d-none');
                    $('#variant_id').val('{{ $bom->productable_id }}').trigger('change');
                @endif
            @endif


            let materials = @json($materials);

            $('#product_id').select2({
                placeholder: "--- Chọn sản phẩm ---",
                ajax: {
                    url: '/admin/boms/get-product-select',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(product => ({
                                id: product.id,
                                text: `${product.name}`
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // mở dropdown không cần gõ
            });

            $('#product_id').on('change', function() {
                const productId = $(this).val();

                if (!productId) return;

                // Gọi API để kiểm tra sản phẩm này có biến thể không
                $.get(`/admin/boms/check-variants/${productId}`, function(res) {
                    if (res.has_variant) {
                        // Hiển thị thẻ chọn biến thể
                        $('#variant_wrapper').removeClass('d-none');

                        const $variantSelect = $('#variant_id');
                        $variantSelect.empty();

                        // Thêm placeholder
                        $variantSelect.append('<option value="">-- Chọn biến thể --</option>');

                        res.variants.forEach(variant => {
                            const text = `SKU: ${variant.sku}`;
                            $variantSelect.append(new Option(text, variant.id));
                        });

                        $variantSelect.trigger('change');
                    } else {
                        // Ẩn phần chọn biến thể nếu sản phẩm là đơn giản
                        $('#variant_wrapper').addClass('d-none');
                        $('#variant_id').empty();
                    }
                });
            });

            submitForm('#myForm', function(response) {
                window.location.href = response.data.redirect
            });

            let counter = {{ isset($boms) ? count($boms) : 0 }};
            let materialOptions = materials.map(material =>
                `<option value="${material.id}">${material.name}</option>`
            ).join('');

            // Thêm dòng mới
            $('#add-new-row').click(function() {
                let newRow = `
                    <tr>
                        <td width="5%" class="row-index"></td>
                        <td>
                            <select name="values[${counter}][material_id]" class="form-control material-select select2">
                                <option value="">Chọn vật liệu</option>
                                ${materialOptions}
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="text" name="values[${counter}][quantity_required]" class="form-control">
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0)" class="remove-item text-decoration-none text-danger">
                                <svg class="icon svg-icon-ti-ti-trash" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
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
                `;

                $('table tbody').append(newRow);

                initSelect2ForAll();
                updateRowIndices();
                updateMaterialSelects();

                counter++;
            });

            // Xóa dòng
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();

                updateRowIndices();
                updateMaterialSelects();

                counter = $('table tbody tr').length;
            });

            // Cập nhật STT + name[]
            function updateRowIndices() {
                $('table tbody tr').each(function(index) {
                    $(this).find('.row-index').text(index + 1);
                    $(this).find('select.material-select').attr('name', `values[${index}][material_id]`);
                    $(this).find('input[name$="[quantity_required]"]').attr('name',
                        `values[${index}][quantity_required]`);
                });
            }

            // Khởi tạo Select2
            function initSelect2ForAll() {
                $('select.material-select').select2({
                    placeholder: 'Chọn vật liệu',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Khi thay đổi vật liệu
            $(document).on('change', '.material-select', function() {
                updateMaterialSelects();
            });

            // Lấy danh sách vật liệu đang được chọn
            function getSelectedMaterials() {
                const selected = [];
                $('.material-select').each(function() {
                    const val = $(this).val();
                    if (val) selected.push(val);
                });
                return selected;
            }

            // Cập nhật option trong các select tránh trùng
            function updateMaterialSelects() {
                const selectedMaterials = getSelectedMaterials();

                $('.material-select').each(function() {
                    const currentSelect = $(this);
                    const currentValue = currentSelect.val();

                    // Lưu lại options gốc 1 lần
                    if (!currentSelect.data('original-options')) {
                        currentSelect.data('original-options', currentSelect.html());
                    }

                    currentSelect.html(currentSelect.data('original-options'));

                    // Loại bỏ các option đã chọn ở select khác
                    currentSelect.find('option').each(function() {
                        const optionValue = $(this).val();
                        if (optionValue !== currentValue && selectedMaterials.includes(
                                optionValue)) {
                            $(this).remove();
                        }
                    });

                    // Gán lại value nếu vẫn còn
                    currentSelect.val(currentValue).trigger('change.select2');
                });
            }
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
