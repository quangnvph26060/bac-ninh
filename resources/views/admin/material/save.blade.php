@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'Nguyên vật tư', 'url' => route('admin.materials.index')],
                    ['name' => !empty($material) ? "Cập nhật nguyên vật tư - $material->name" : 'Thêm mới vật tư'],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>

        <form action="" method="post" id="myForm">
            @isset($material)
                @method('PUT')
            @endisset
            <div class="row">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 position-relative">
                                    <label for="name" class="form-label required">Tên vật tư</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Nhập tên vật tư" value="{{ optional($material)->name }}">
                                </div>

                                <div class="col-lg-6 mb-3 position-relative">
                                    <label for="code" class="form-label">Mã vật tư</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        placeholder="Nhập mã vật tư" aria-label="code"
                                        value="{{ optional($material)->code }}">
                                </div>

                                <div class="col-lg-3 mb-3">
                                    <label for="min_stock" class="form-label">Số lượng báo động</label>
                                    <input type="text" name="min_stock" id="min_stock" class="form-control"
                                        value="{{ $material && $material->min_stock ? number_format($material->min_stock, 0, ',', '.') : 0 }}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label for="unit" class="form-label required">Đơn vị</label>
                                    <input list="units" type="text" name="unit" id="unit" class="form-control"
                                        placeholder="Nhập đơn vị" value="{{ optional($material)->unit }}">

                                    <datalist id="units">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="col-lg-12 mb-3 position-relative">
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea name="note" id="note" class="form-control" placeholder="Nhập ghi chú">{{ optional($material)->note }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    @include('admin.components.button', ['redirect' => route('admin.materials.index')])
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.materials.index') }}"
            })

            updateCharCount("#name", 250);
            updateCharCount("#code", 8);
            updateCharCount("#note", 255);

            convertToAsciiUpper("#code")
        })
    </script>
@endpush
