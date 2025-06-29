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
                {{-- <div class="d-flex gap-2">
                    <a href="{{ route('orders.downloadTemplate') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-excel-fill me-2"></i> Download XLS Template
                    </a>
                    <a href="{{ route('orders.downloadProductInfo') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-excel-fill me-2"></i> Product Information
                    </a>

                </div> --}}
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

        <div class="row align-items-start">
            <!-- Left Content -->
            <div class="col-lg-6 col-md-12">
                <div class="pe-lg-4">
                    <!-- Main Title -->
                    <h3 class="main-title mt-5">
                        Your standardized file data and order settings
                    </h3>

                    <!-- Checklist Items -->
                    <div class="mb-4">
                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="content-text">
                                Upload your file
                            </div>
                        </div>

                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="content-text">
                                Assign header names to data fields in your import file or use our template
                            </div>
                        </div>

                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="content-text">
                                Match the header names in your import file with the ones in the tool
                            </div>
                        </div>

                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="content-text">
                                Import your orders
                            </div>
                        </div>

                        <div class="check-item">
                            <div class="check-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="content-text">
                                Confirm your orders and pay from your Printway Wallet
                            </div>
                        </div>
                    </div>

                    <!-- Subtitle -->
                    <div class="subtitle">
                        Download our data import template with all the mandatory and optional fields
                    </div>

                    <!-- Download Links -->
                    <div class="download-section grid gap-3">
                        @if ($fileUpload?->sample_file_path)
                            <div
                                class="download-item flex items-center gap-3 p-3 border rounded hover:bg-gray-50 transition">
                                <div class="excel-icon text-green-600 text-3xl">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div>
                                    <a href="{{ Storage::url($fileUpload->sample_file_path) }}"
                                        class="download-link font-semibold text-blue-600 hover:underline" download>
                                        📥 Download XLS Template
                                    </a>
                                    <div class="update-text text-sm text-gray-500">
                                        Updated at: {{ $fileUpload->updated_at_sample?->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($fileUpload?->data_file_path)
                            <div
                                class="download-item flex items-center gap-3 p-3 border rounded hover:bg-gray-50 transition">
                                <div class="excel-icon text-green-600 text-3xl">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div>
                                    <a href="{{ Storage::url($fileUpload->data_file_path) }}"
                                        class="download-link font-semibold text-blue-600 hover:underline" download>
                                        📥 Product Information
                                    </a>
                                    <div class="update-text text-sm text-gray-500">
                                        Updated at: {{ $fileUpload->updated_at_data?->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Right Illustration -->
            <div class="col-lg-6 col-md-12">
                <div class="illustration-container">
                    <img src="{{ asset('images/excel-upload.png') }}" alt="Data Import Illustration" class="img-fluid">
                </div>
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
    <style>
        .check-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .check-icon {
            width: 20px;
            height: 20px;
            background-color: #e8f5e8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .check-icon i {
            color: #28a745;
            font-size: 12px;
        }

        .download-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 8px 0;
        }

        .excel-icon {
            width: 24px;
            height: 24px;
            background-color: #ff6b35;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .excel-icon i {
            color: white;
            font-size: 14px;
        }

        .download-link {
            color: #17a2b8;
            text-decoration: none;
            font-weight: 500;
        }

        .download-link:hover {
            color: #138496;
            text-decoration: underline;
        }

        .update-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-left: 8px;
        }

        .main-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 2rem;
        }

        .subtitle {
            font-weight: 500;
            color: #333;
            margin-bottom: 1.5rem;
            margin-top: 2rem;
        }

        .content-text {
            color: #555;
            line-height: 1.6;
        }

        .illustration-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .illustration-container img {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            .main-title {
                font-size: .9rem;
            }

            .illustration-container {
                margin-top: 2rem;
                min-height: 300px;
            }
        }
    </style>
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
                                                console.log(response);

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

                                                    if (status ===
                                                        'processing') {
                                                        if (current === 0) {
                                                            $('#progress-text')
                                                                .text(
                                                                    'Đang khởi tạo...'
                                                                );
                                                        } else {
                                                            $('#progress-text')
                                                                .text(
                                                                    `Đang xử lý...`
                                                                );
                                                        }
                                                    }
                                                    // : ${current}/${total} đơn hàng
                                                    if (status === 'done') {
                                                        clearInterval(
                                                            interval);
                                                        clearTimeout(
                                                            timeout);

                                                        if (failures &&
                                                            failures
                                                            .length > 0) {
                                                            $('#progress-text')
                                                                .text(
                                                                    'Import hoàn tất với một số lỗi.'
                                                                );
                                                            showErrors(
                                                                failures
                                                            );
                                                        } else {
                                                            $('#progress-text')
                                                                .text(
                                                                    'Hoàn tất import đơn hàng!'
                                                                );
                                                            showSuccess(
                                                                'Import hoàn tất!'
                                                            );
                                                        }

                                                        resetUI();
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
                                    }, 10 * 60 * 1000);
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
