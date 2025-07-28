@extends('admin.layout.index')


@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'nhập công nợ đầu kỳ']]" />
        </div>

        <form id="myForm" enctype="multipart/form-data">

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row g-3">

                                    <div class="position-relative col-md-6">
                                        <label for="transaction_date" class="form-label">Ngày thu chi</label>
                                        <input type="date" placeholder="Chọn ngày thu chi" name="transaction_date"
                                            id="transaction_date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label class="form-label required">Loại đối tượng</label>
                                        <select name="object_type" id="object-type" class="form-select">
                                            <option value=""></option>
                                            <option value="customer">Khách hàng</option>
                                            <option value="supplier">Nhà cung cấp</option>
                                        </select>
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label class="form-label required">Loại phiếu</label>
                                        <select class="form-select" name="type" id="type">
                                            <option value=""></option>
                                            <option value="income">Phiếu thu</option>
                                            <option value="expense">Phiếu trả</option>
                                        </select>
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label class="form-label">Đối tượng</label>

                                        <input type="text" id="object_code" class="form-control"
                                            placeholder="Nhập 3 ký tự để tìm đối tượng" value="">

                                        <input type="hidden" name="object_id" value="">

                                        <div id="object-search-result"
                                            class="border bg-white position-absolute w-100 shadow-sm"
                                            style="z-index: 9999; display: none;">
                                            <!-- Kết quả sẽ render tại đây -->
                                        </div>
                                    </div>

                                    <div class="position-relative col-md-6">
                                        <label for="amount" class="form-label required">Số tiền</label>
                                        <input type="text" placeholder="Nhập số tiền" name="amount" id="amount"
                                            class="form-control usd-price-format">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button')

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Ghi chú</h3>
                        </div>
                        <div class="card-body">
                            <textarea name="description" rows="3" class="form-control" id="description" placeholder="Nhập ghi chú"></textarea>
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
        $(function() {
            $('#object-type').select2({
                placeholder: "Chọn loại đối tượng",
                allowClear: true,
                width: '100%'
            });

            $('#type').select2({
                placeholder: "Chọn loại đối tượng",
                allowClear: true,
                width: '100%'
            });

            let typingTimer;
            let doneTypingInterval = 500;

            $('#object_code').on('keyup', function() {
                clearTimeout(typingTimer);
                let keyword = $(this).val();
                let type = $('#object-type').val();

                if (keyword.length >= 3 && type) {
                    typingTimer = setTimeout(function() {
                        $.ajax({
                            url: '/admin/cash-transactions/search-object',
                            data: {
                                type: type,
                                keyword: keyword
                            },
                            success: function(res) {
                                let html = '';
                                if (res.length > 0) {
                                    res.forEach(item => {
                                        html += `<div class="p-2 border-bottom object-item" style="cursor: pointer;" data-id="${item.id}" data-phone="${item.phone}" data-code="${item.code}" data-name="${item.name}">
                                            ${item.name} - ${item.phone}
                                        </div>`;
                                    });
                                } else {
                                    html =
                                        `<div class="p-2 text-muted text-center">Không tìm thấy dữ liệu phù hợp</div>`;
                                }
                                $('#object-search-result').html(html).show();
                            }
                        });
                    }, doneTypingInterval);
                } else {
                    $('#object-search-result').hide();
                }
            });

            $(document).on('click', '.object-item', function() {
                let name = $(this).data('name');
                let phone = $(this).data('phone');
                let id = $(this).data('id');
                $('#object_code').val(name + ' - ' + phone);
                $('input[name="object_id"]').val(id);
                $('#object-search-result').hide();
            });

            submitForm('#myForm', function(response) {


                $('#myForm')[0].reset();

                window.location.href = response.data

                // Reset select2 thủ công
                $('#object-type').val(null).trigger('change');
                $('#type').val(null).trigger('change');
            });

        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
