@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'tài khoản kế toán']]" />
        </div>

        {{-- <form action="{{ route('admin.accounting-accounts.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" required>
            <button type="submit" class="btn btn-primary">Import Excel</button>
        </form> --}}

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-success btn-sm fs-6" data-bs-toggle="modal"
                    data-bs-target="#addCashAccountModal">
                    <i class="ti ti-circle-plus"></i> Thêm mới
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="myTable" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr class="text-center">
                                <th style="width:5%"><input type="checkbox" id="checked-all" class="form-check-input"></th>
                                <th style="width:5%">#</th>
                                <th style="width:5%">ID</th>
                                <th style="width:15%">Code</th>
                                <th>Tên</th>
                                <th style="width:15%">Trạng thái</th>
                                <th style="width:15%">Người tạo</th>
                                <th style="width:5%"><i class="fas fa-cog"></i></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addCashAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="myForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm tài khoản kế toán</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 position-relative">
                            <label for="parent_search" class="form-label">Tài khoản cha</label>
                            <input type="text" id="parent_search" class="form-control"
                                placeholder="Nhập mã hoặc tên tài khoản cha...">
                            <input type="hidden" id="parent_id" name="parent_id">
                            <div id="parent_results" class="list-group position-absolute w-100"
                                style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Mã tài khoản</label>
                            <input type="text" name="code" id="code" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên tài khoản</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="mb-3 form-check">
                            <label class="switch">
                                <input name="status" type="checkbox" value="1" checked="">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let typingTimer;
            const doneTypingInterval = 300; // ms

            // Tài khoản cha: AJAX khi nhập >= 3 ký tự
            $('#parent_search').on('keyup', function() {
                clearTimeout(typingTimer);
                const query = $(this).val();

                if (query.length >= 3) {
                    typingTimer = setTimeout(function() {
                        $.ajax({
                            url: '{{ route('admin.accounting-accounts.search') }}',
                            data: {
                                q: query
                            },
                            success: function(data) {
                                let resultBox = $('#parent_results');
                                resultBox.empty();

                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        resultBox.append(
                                            `<a href="#" class="list-group-item list-group-item-action" data-id="${item.id}" data-text="${item.code} - ${item.name}">
                                        ${item.code} - ${item.name}
                                    </a>`
                                        );
                                    });
                                    resultBox.show();
                                } else {
                                    resultBox.hide();
                                }
                            }
                        });
                    }, doneTypingInterval);
                } else {
                    $('#parent_results').hide();
                }
            });

            $('#parent_results').on('click', 'a', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const text = $(this).data('text');

                $('#parent_id').val(id);
                $('#parent_search').val(text);
                $('#parent_results').hide();
            });

            // Ẩn khi click ra ngoài
            $(document).click(function(e) {
                if (!$(e.target).closest('#parent_search, #parent_results').length) {
                    $('#parent_results').hide();
                }
            });

            // Submit form
            // $('#addCashAccountForm').on('submit', function(e) {
            //     e.preventDefault();
            //     let form = $(this);
            //     let submitBtn = form.find('button[type="submit"]');
            //     submitBtn.prop('disabled', true).text('Đang lưu...');

            //     $.ajax({
            //         url: '',
            //         method: 'POST',
            //         data: form.serialize(),
            //         success: function(response) {
            //             toastr.success(response.message);
            //             $('#addCashAccountModal').modal('hide');
            //             form[0].reset();
            //             $('#parent_id').val(null).trigger('change');
            //             // Reload DataTable hoặc location.reload() nếu cần
            //         },
            //         error: function(xhr) {
            //             toastr.error('Đã xảy ra lỗi, vui lòng kiểm tra lại.');
            //         },
            //         complete: function() {
            //             submitBtn.prop('disabled', false).text('Lưu');
            //         }
            //     });
            // });

            submitForm('#myForm', function(response) {
                console.log(response);
            })
        });
    </script>
@endpush
