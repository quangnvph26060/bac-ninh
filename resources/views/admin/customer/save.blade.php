@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'khách hàng', 'url' => '/admin/customers'], ['name' => $title]]" />
        </div>

        <form id="myForm" enctype="multipart/form-data">

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row g-3">

                                    @isset($customer)
                                        @method('PUT')
                                    @endisset

                                    <div class="position-relative col-md-6">
                                        <label for="name" class="form-label required">Tên khách hàng</label>
                                        <input type="text" placeholder="Nhập tên khách hàng" name="name"
                                            id="name" class="form-control"
                                            value="{{ isset($customer) ? $customer->name : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="company_name" class="form-label">Tên công ty</label>
                                        <input type="text" placeholder="Nhập tên công ty" name="company_name"
                                            id="company_name" class="form-control"
                                            value="{{ isset($customer) ? $customer->company_name : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="code" class="form-label">Mã khách hàng</label>
                                        <input type="text" placeholder="Nhập mã khách hàng" name="code" id="code"
                                            class="form-control" value="{{ isset($customer) ? $customer->code : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="company_tax_code" class="form-label">Mã số thuế</label>
                                        <input type="text" placeholder="Nhập mã số thuế" name="company_tax_code"
                                            id="company_tax_code" class="form-control"
                                            value="{{ isset($customer) ? $customer->company_tax_code : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="phone" class="form-label required">Số điện thoại</label>
                                        <input type="text" placeholder="Nhập số điện thoại" name="phone" id="phone"
                                            class="form-control" value="{{ isset($customer) ? $customer->phone : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="company_address" class="form-label">Địa chỉ công ty</label>
                                        <input type="text" placeholder="Nhập địa chỉ công ty" name="company_address"
                                            id="company_address" class="form-control"
                                            value="{{ isset($customer) ? $customer->company_address : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="text" placeholder="Nhập email" name="email" id="email"
                                            class="form-control" value="{{ isset($customer) ? $customer->email : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="citizen_id" class="form-label">Số CCCD</label>
                                        <input type="text" placeholder="Nhập số CCCD" name="citizen_id" id="citizen_id"
                                            class="form-control"
                                            value="{{ isset($customer) ? $customer->citizen_id : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="birthday" class="form-label">Ngày sinh</label>
                                        <input type="date" placeholder="Nhập ngày sinh" name="birthday" id="birthday"
                                            class="form-control"
                                            value="{{ isset($customer) && $customer->birthday ? $customer->birthday->format('Y-m-d') : '' }}">
                                    </div>


                                    <div class="position-relative col-md-6">
                                        <label for="customer_type" class="form-label">Loại khách hàng</label>
                                        <select name="customer_type" id="customer_type" class="form-select form-control">
                                            <option value="retail"
                                                {{ isset($customer) && $customer->customer_type == 'retail' ? 'selected' : '' }}>
                                                Khách lẻ</option>
                                            <option value="wholesale"
                                                {{ isset($customer) && $customer->customer_type == 'wholesale' ? 'selected' : '' }}>
                                                Khách sỉ</option>
                                            <option value="agent"
                                                {{ isset($customer) && $customer->customer_type == 'agent' ? 'selected' : '' }}>
                                                Đại lý</option>
                                        </select>
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <input type="text" placeholder="Nhập địa chỉ" name="address" id="address"
                                            class="form-control"
                                            value="{{ isset($customer) ? $customer->address : '' }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="gender" class="form-label">Giới tính</label>
                                        <select name="gender" id="gender" class="form-select form-control">
                                            <option value="">--- Giới tính ---</option>
                                            <option value="male"
                                                {{ isset($customer) && $customer->gender == 'male' ? 'selected' : '' }}>
                                                Nam</option>
                                            <option value="female"
                                                {{ isset($customer) && $customer->gender == 'female' ? 'selected' : '' }}>
                                                Nữ</option>
                                            <option value="other"
                                                {{ isset($customer) && $customer->gender == 'other' ? 'selected' : '' }}>
                                                Khác</option>
                                        </select>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => '/admin/customers'])

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Ghi chú</h3>
                        </div>
                        <div class="card-body">
                            <textarea name="note" rows="3" class="form-control" id="note" placeholder="Nhập ghi chú"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const inputIds = [{
                    id: 'name',
                    maxLength: 250
                },
                {
                    id: 'company_name',
                    maxLength: 250
                },
                {
                    id: 'code',
                    maxLength: 20
                },
                {
                    id: 'company_tax_code',
                    maxLength: 100
                },
                {
                    id: 'phone',
                    maxLength: 11
                },
                {
                    id: 'company_address',
                    maxLength: 255
                },
                {
                    id: 'email',
                    maxLength: 200
                },
                {
                    id: 'citizen_id',
                    maxLength: 12
                },
                {
                    id: 'address',
                    maxLength: 255
                }
            ];

            $.each(inputIds, function(index, value) {
                updateCharCount(`#${value.id}`, value.maxLength);
            });

            convertToAsciiUpper('#code')

            submitForm('#myForm', function(response) {
                window.location.href = '/admin/customers'
            })
        })
    </script>
@endpush
