@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'Vật liệu trong sản phẩm', 'url' => route('admin.boms.index')],
                ['name' => isset($boms[0]) ? $title : $title],
            ]" />
        </div>
        {{-- @dd($boms[0]); --}}
        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if (isset($boms) && count($boms))
                @method('PUT')
            @endif
            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="productable_id" class="form-label required">Tên sản phẩm</label>
                                        <select name="productable_id" id="name"
                                            class="form-control select2 {{ isset($boms[0]) ? 'readonly-select' : '' }}">
                                            <option value="" disabled selected>Chọn tên sản phẩm</option>
                                            @foreach ($products as $product)
                                                @if (!empty($product->variants) && $product->variants->count() > 0)
                                                    @foreach ($product->variants as $variant)
                                                        <option value="{{ $variant->id }}" data-id="ProductVariant"
                                                            @if (isset($boms[0]) &&
                                                                    $boms[0]->productable_type == '\App\Models\ProductVariant' &&
                                                                    $boms[0]->productable_id == $variant->id) selected @endif>
                                                            {{ $product->name }} - {{ $variant->sku }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ $product->id }}" data-id="Product"
                                                        @if (isset($boms[0]) && $boms[0]->productable_type == '\App\Models\Product' && $boms[0]->productable_id == $product->id) selected @endif>
                                                        {{ $product->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="productable_type" name="productable_type"
                                            {{ isset($boms[0]) ? 'readonly' : '' }}
                                            value="{{ isset($boms[0]) ? $boms[0]->productable_type : '' }}">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Danh sách vật liệu</h4>
                            <button type="button" class="btn btn-outline-light btn-sm text-dark border">Thêm vật
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
                                    @foreach ($boms ?? [] as $index => $bom)
                                        <tr>
                                            <td width="5%">{{ $index + 1 }}</td>
                                            <td>
                                                <select name="values[{{ $index }}][material_id]"
                                                    class="form-control material-select select2">
                                                    <option value="">Chọn vật liệu</option>
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}"
                                                            {{ $bom->material_id == $material->id ? 'selected' : '' }}>
                                                            {{ $material->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="text" name="values[{{ $index }}][quantity_required]"
                                                    class="form-control" value="{{ $bom->quantity_required }}">
                                            </td>
                                            <td class="text-center">
                                                <a href="javascript:void(0)"
                                                    class="remove-item text-decoration-none text-danger">
                                                    {{-- SVG trash icon --}}
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

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-selection__clear {
            height: 39px !important;
        }

        .select2-selection__rendered {
            padding-left: 15px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#name').select2({
                placeholder: "Chọn tên sản phẩm",
                allowClear: true,
                width: '100%'
            });
            $('#name').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const dataId = selectedOption.data('id') || '';
                $('#productable_type').val('\\App\\Models\\' + dataId);
            });

            if ($('#name').hasClass('readonly-select')) {
                $('#name').on('select2:opening', function(e) {
                    e.preventDefault(); // chặn không cho mở dropdown
                });
            }

        });
    </script>

    <script>
        let materials = @json($materials);
    </script>

    <script>
        function getSelectedMaterials() {
            const selected = [];
            $('.material-select').each(function() {
                const val = $(this).val();
                if (val) selected.push(val);
            });
            return selected;
        }

        function updateMaterialSelects() {
            const selectedMaterials = getSelectedMaterials();

            $('.material-select').each(function() {
                const currentSelect = $(this);
                const currentValue = currentSelect.val();

                // Lấy lại danh sách option ban đầu (nếu chưa có)
                if (!currentSelect.data('original-options')) {
                    currentSelect.data('original-options', currentSelect.html());
                }

                // Reset lại option gốc
                currentSelect.html(currentSelect.data('original-options'));

                // Xử lý ẩn option
                currentSelect.find('option').each(function() {
                    const option = $(this);
                    const optionValue = option.val();

                    // Nếu không phải value đang chọn và đã bị chọn ở select khác thì ẩn
                    if (optionValue !== currentValue && selectedMaterials.includes(optionValue)) {
                        option.remove(); // Xoá hẳn khỏi select
                    }
                });

                // Gán lại value đang chọn nếu mất
                currentSelect.val(currentValue);

                // Re-init lại Select2 để cập nhật UI
                currentSelect.trigger('change.select2');
            });
        }


        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.boms.index') }}"
            });

            counter = {{ isset($boms) ? count($boms) : 0 }};
            let materialOptions = materials.map(material =>
                `<option value="${material.id}">${material.name}</option>`
            ).join('');

            $('.btn-outline-light').click(function() {
                let newRow = `
                    <tr>
                        <td width="5%">${counter + 1}</td>
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

                $('select.material-select').select2({
                    placeholder: 'Chọn vật liệu',
                    allowClear: true,
                    width: '100%'
                });

                updateMaterialSelects();
                counter++;
            });

            $(document).on('change', '.material-select', function() {
                updateMaterialSelects();
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();

                $('table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });

                updateMaterialSelects();
            });
        });
    </script>
@endpush
