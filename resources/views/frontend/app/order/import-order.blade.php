@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper">
            <div class="d-flex gap-2 align-items-center justify-content-between">
                <a href="{{ route('orders.index') }}">
                    <div class="d-flex align-items-center cursor-pointer mb-2" style="color: rgb(66, 82, 110);">
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1429 12L4.85718 12" stroke="#8F9BB3" stroke-width="2" stroke-miterlimit="10" />
                            <path d="M9.85718 17L4.85718 12L9.85718 7" stroke="#8F9BB3" stroke-width="2"
                                stroke-miterlimit="10" stroke-linecap="square" />
                        </svg>
                        <div class="ms-2">Back to Orders</div>
                    </div>
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('orders.downloadTemplate') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-excel-fill me-2"></i> Download XLS Template
                    </a>
                    <a href="{{ route('orders.downloadProductInfo') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-excel-fill me-2"></i> Product Info
                    </a>

                </div>
            </div>

            <h1 class="billing__title__content">Import Order</h1>
        </div>

        <div class="d-flex justify-content-center">
            <div class="border rounded p-3 text-center w-100 cursor" style="border-style: dashed !important;">
                <p class="fw-bold mb-3" id="file-drop-text">Drop your file here or</p>

                <div class="mb-3" id="file-action-wrapper">
                    <label for="formFile" class="btn btn-light cursor-pointer" id="choose-file-btn">
                        Choose file
                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 19V5M12 5L7 10M12 5L17 10" stroke="#000000" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </label>
                    <input class="form-control d-none" type="file" id="formFile" accept=".xlsx">
                </div>

                <p class="text-muted">Up to 10 MB. Only .xlsx files allowed</p>
            </div>
        </div>

        <div id="import-progress" class="mt-3 d-none">
            <div class="progress" style="height: 25px;">
                <div id="progress-bar" class="progress-bar progress-bar-striped bg-success" style="width: 0%">0%</div>
            </div>
            <p id="progress-text" class="mt-2">Đang khởi tạo...</p>
        </div>


        <div id="import-errors" class="mt-3 d-none">
            <div class="alert alert-danger text-start" role="alert">
                <h6 class="fw-bold mb-2">Import Failed</h6>
                <ul class="mb-0" id="error-list">
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
    <script>
        let interval = null;
        let timeout = null;

        function bindFileInput() {
            $('#formFile').on('change', function() {
                const file = this.files[0];
                if (file) {
                    $('#file-drop-text').text(file.name);
                    $('#file-action-wrapper').html(`
                        <button type="button" class="btn btn-primary btn-sm" id="confirm-upload-btn">
                            Confirm Import
                        </button>
                    `);

                    $('#confirm-upload-btn').on('click', function() {
                        let formData = new FormData();
                        formData.append('file', file);

                        $(this).prop('disabled', true).text('Đang import...');

                        $('#import-progress').removeClass('d-none');
                        $('#progress-text').text('Đang khởi tạo...');
                        $('#progress-bar').css('width', '0%').text('0%');
                        $('#import-errors').addClass('d-none');
                        $('#error-list').empty();

                        $.ajax({
                            url: '',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(data) {
                                if (data.job_id) {
                                    const jobId = data.job_id;

                                    interval = setInterval(function() {
                                        $.ajax({
                                            url: "{{ route('orders.import-progress', '__jobId__') }}"
                                                .replace('__jobId__', jobId),
                                            type: 'GET',
                                            success: function(response) {
                                                if (response) {
                                                    const {
                                                        percent,
                                                        current,
                                                        total,
                                                        status,
                                                        failures
                                                    } = response;

                                                    $('#progress-bar').css(
                                                            'width',
                                                            percent + '%')
                                                        .text(percent +
                                                            '%');
                                                    $('#progress-text')
                                                        .text(
                                                            `Đang xử lý: ${current}/${total} đơn hàng...`
                                                        );

                                                    if (status ===
                                                        'success' ||
                                                        status ===
                                                        'completed_with_errors'
                                                    ) {
                                                        clearInterval(
                                                            interval);
                                                        clearTimeout(
                                                            timeout);

                                                        if (status ===
                                                            'success') {
                                                            $('#progress-text')
                                                                .text(
                                                                    'Hoàn tất import đơn hàng!'
                                                                );
                                                            showSuccess(
                                                                'Import hoàn tất!'
                                                            );
                                                        } else {
                                                            $('#progress-text')
                                                                .text(
                                                                    'Import hoàn tất với một số lỗi.'
                                                                );
                                                            showErrors(
                                                                failures
                                                            );
                                                        }

                                                        resetUI()
                                                        // ✅ Reset UI sau một khoảng delay ngắn
                                                        // setTimeout(resetUI,
                                                        //     1000);
                                                    }
                                                }
                                            },
                                            error: function(xhr, status,
                                                error) {
                                                showErrors([error]);
                                                clearInterval(interval);
                                                clearTimeout(timeout);
                                                setTimeout(resetUI, 3000);
                                            }
                                        });
                                    }, 2000);

                                    timeout = setTimeout(() => {
                                        clearInterval(interval);
                                        showErrors(['Quá thời gian chờ']);
                                        setTimeout(resetUI, 3000);
                                    }, 5 * 60 * 1000);
                                } else {
                                    showErrors(['Không nhận được job_id từ server']);
                                    setTimeout(resetUI, 3000);
                                }
                            },
                            error: function() {
                                showErrors(['Đã có lỗi xảy ra khi gửi file.']);
                                setTimeout(resetUI, 3000);
                            }
                        });
                    });
                }
            });
        }

        $(document).ready(function() {
            bindFileInput();
        });

        function showErrors(errors) {
            let html = '';
            errors.forEach(err => {
                html += `<li>${err}</li>`;
            });
            $('#error-list').html(html);
            $('#import-errors').removeClass('d-none')
        }

        function showSuccess(msg) {
            $('#success-container').text(msg).show();
        }

        function resetUI() {
            $('#file-drop-text').text('Drop your file here or');
            $('#file-action-wrapper').html(`
                <label for="formFile" class="btn btn-light cursor-pointer" id="choose-file-btn">
                    Choose file
                    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 19V5M12 5L7 10M12 5L17 10" stroke="#000000" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </label>
                <input class="form-control d-none" type="file" id="formFile" accept=".xlsx">
                    `);

            // $('#import-progress').addClass('d-none');
            // $('#progress-bar').css('width', '0%').text('0%');
            // $('#progress-text').text('');
            // $('#import-errors').addClass('d-none');
            // $('#error-list').empty();

            // Rebind lại sự kiện change cho input file (vì nó bị thay mới)
            $('#confirm-upload-btn').prop('disabled', false).text('Confirm Import');
            bindFileInput();
        }
    </script>
@endpush
