@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'nhà cung cấp', 'url' => route('admin.suppliers.index')],
                ['name' => $supplier ? $title . ' - ' . $supplier->company_name : $title],
            ]" />
        </div>

        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if ($supplier)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="company_name" class="form-label required">Tên nhà cung cấp</label>
                                        <input type="text" placeholder="Tên sản phẩm" aria-required="true" required
                                            class="form-control" name="company_name" id="company_name"
                                            value="{{ optional($supplier)->company_name }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-8">
                                        <label for="representative_name" class="form-label">Người đại diện</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="representative_name"
                                                id="representative_name"
                                                value="{{ optional($supplier)->representative_name }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="position" class="form-label">Chức vụ</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="position" id="position"
                                                value="{{ optional($supplier)->position }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="phone" id="phone"
                                                value="{{ optional($supplier)->phone }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="email" class="form-label">Địa chỉ email</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="email" id="email"
                                                value="{{ optional($supplier)->email }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="address" id="address"
                                                value="{{ optional($supplier)->address }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-4">
                                        <label for="tax_code" class="form-label">Mã số thuế</label>
                                        <input type="text" placeholder="tax_code" class="form-control" name="tax_code"
                                            id="tax_code" value="{{ optional($supplier)->tax_code }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-8">
                                        <label for="notes" class="form-label">Số tài khoản</label>
                                        <input type="text" placeholder="Số tài khoản" class="form-control"
                                            name="bank_account_number" id="bank_account_number"
                                            value="{{ optional($supplier)->bank_account_number }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="bank_account_number" class="form-label">Ngân hàng</label>
                                        <select name="bank_id" class="form-control form-select" id="bank_id">
                                            <option value="">--- Chọn ngân hàng ---</option>
                                            @foreach ($banks as $id => $name)
                                                <option value="{{ $id }}" @selected($id == optional($supplier)->bank_id)>
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="notes" class="form-label">Ghi chú</label>
                                        <textarea name="notes" id="notes" class="form-control" placeholder="Ghi chú" rows="3">{{ optional($supplier)->notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.suppliers.index')])

                    <x-status :status="optional($supplier)->status" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Thương hiệu
                            </h4>
                        </div>
                        <div class="card-body">
                            <select id="brand_id" name="brand_id[]" class="form-control select2" multiple="multiple">
                                @foreach ($brands as $bId => $bName)
                                    <option value="{{ $bId }}" @selected(in_array($bId, $getSelectdBrands ?? []))>{{ $bName }}
                                    </option>
                                @endforeach
                            </select>
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
        const inputIds = [{
                id: 'company_name',
                maxLength: 250
            },
            {
                id: 'representative_name',
                maxLength: 250
            },
            {
                id: 'position',
                maxLength: 100
            },
            {
                id: 'phone',
                maxLength: 12
            },
            {
                id: 'email',
                maxLength: 250
            },
            {
                id: 'address',
                maxLength: 255
            },
            {
                id: 'notes',
                maxLength: 400
            }
        ];

        $.each(inputIds, function(index, value) {
            updateCharCount(`#${value.id}`, value.maxLength);
        });

        $(document).ready(function() {
            $('#brand_id').select2({
                placeholder: "Chọn thương hiệu...",
                allowClear: true
            });

            $('#bank_id').select2({
                placeholder: "Chọn ngân hàng...",
                allowClear: true
            });

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.suppliers.index') }}"
            })
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
