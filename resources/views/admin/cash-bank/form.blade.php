@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'tạo phiếu thu chi']]" />
        </div>

        <div class="form-container">
            <form id="myForm">

                @if (!empty($transaction))
                    @method('PUT')
                @endif

                <input type="hidden" name="transaction_id" value="{{ optional($transaction)->id }}">
                <input type="hidden" name="entry_id" value="{{ optional($mainEntry)->id }}">

                <div class="row">
                    <div class="col-lg-8 pe-0">
                        <div class="section-header">
                            <i class="fas fa-info-circle"></i>
                            Thông tin
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Ngày thu chi</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="transaction_date"
                                            value="{{ optional($transaction)->transaction_date ? optional($transaction)->transaction_date->format('Y-m-d') : date('Y-m-d') }}">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label required">Loại đối tượng</label>
                                    <select name="obj_type" id="object-type" class="form-select">
                                        <option value=""></option>
                                        <option value="customer" @selected(optional($contraEntry)->tableable_type === 'App\Models\Customer')>Khách hàng</option>
                                        <option value="supplier" @selected(optional($contraEntry)->tableable_type === 'App\Models\Supplier')>Nhà cung cấp</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Tài khoản tiền mặt</label>
                                    <select class="form-select" name="account_id" id="account_id">
                                        <option value=""></option>
                                        @foreach ($moneyAccounts as $moneyAccount)
                                            <option value="{{ $moneyAccount->id }}" @selected(optional($mainEntry)->account_id == $moneyAccount->id)>
                                                {{ "$moneyAccount->code - $moneyAccount->name" }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <label class="form-label">Đối tượng</label>

                                        <input type="text" id="object_code" class="form-control"
                                            placeholder="Nhập 3 ký tự để tìm đối tượng"
                                            value="{{ !empty($contraEntry) ? $contraEntry->tableable->name : '' }} - {{ !empty($contraEntry) ? $contraEntry->tableable->phone : '' }}">

                                        <input type="hidden" name="obj_id"
                                            value="{{ optional($contraEntry)->tableable_id }}">

                                        <div id="object-search-result"
                                            class="border bg-white position-absolute w-100 shadow-sm"
                                            style="z-index: 9999; display: none;">
                                            <!-- Kết quả sẽ render tại đây -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Loại phiếu <span class="required">*</span></label>
                                    <select class="form-select" name="type" id="type">
                                        <option value=""></option>

                                        @if ($type === 'cash')
                                            <option value="income" @selected(optional($transaction)->type === 'income')>Phiếu thu</option>
                                            <option value="expense" @selected(optional($transaction)->type === 'expense')>Phiếu chi</option>
                                        @else
                                            <option value="debit_notice" @selected(optional($transaction)->type === 'debit_notice')>Báo nợ (Rút tiền)
                                            </option>
                                            <option value="credit_notice" @selected(optional($transaction)->type === 'credit_notice')>Báo có (Nộp tiền)
                                            </option>
                                        @endif

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label d-flex align-items-center">
                                        Loại chứng từ
                                    </label>

                                    <input type="text" name="document_type" placeholder="ví dụ: Đơn hàng"
                                        class="form-control" value="{{ optional($transaction)->document_type }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ID chứng từ</label>
                                    <input type="text" name="reference_number"
                                        value="{{ optional($transaction)->reference_number }}"
                                        placeholder="Nhập ID chứng từ" class="form-control">
                                </div>

                                <div class="col-12">
                                    <div class="file-upload-area">
                                        @if (!empty($transaction) && $transaction->attachment)
                                            <div class="mb-2 d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ asset("storage/$transaction->attachment") }}" target="_blank"
                                                    class="btn btn-sm btn-primary text-white text-decoration-none">
                                                    <i class="bi bi-file-earmark-text me-1"></i>
                                                    Xem file đính kèm
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    id="removeAttachmentBtn">
                                                    <i class="bi bi-x-circle"></i> Xoá file
                                                </button>
                                            </div>
                                        @endif

                                        <div id="filePreviewArea" class="mb-2"></div>

                                        <div class="file-upload-text">
                                            Chọn file jpg, jpeg, gif, png, doc,... &lt;= 8MB
                                        </div>
                                        <button type="button" class="btn btn-file" id="triggerFileInput">
                                            <i class="bi bi-upload me-1"></i>
                                            Chọn File
                                        </button>
                                        <input type="file" class="d-none" name="attachment" id="fileInput"
                                            accept=".jpg,.jpeg,.gif,.png,.doc,.docx,.pdf">

                                        <input type="hidden" name="remove_attachment" id="removeAttachment"
                                            value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 section-divider ps-0">
                        <div class="section-header">
                            <i class="fas fa-credit-card"></i>
                            Thanh toán
                        </div>
                        <div class="section-content">
                            <div class="mb-3">
                                <label class="form-label required">Số tiền (USD)</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <input type="text" name="amount" class="form-control usd-price-format"
                                        value="{{ $mainEntry ? ($mainEntry->debit_amount > 0 ? formatPrice($mainEntry->debit_amount) : formatPrice($mainEntry->credit_amount)) : '' }}"
                                        placeholder="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-rocketchat"></i>
                                    </span>
                                    <textarea name="description" class="form-control" rows="3">{{ optional($transaction)->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @php
                    $backUrl = request()->is('admin/cash-transactions*')
                        ? '/admin/cash-transactions'
                        : '/admin/bank-transactions';
                @endphp

                <div class="border-top p-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>
                            Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-circle me-1"></i>
                            Lưu
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="addVoucherTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="voucherTypeForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm loại chứng từ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Tên loại chứng từ</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="description"></textarea>
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
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            $('#object-type').select2({
                placeholder: "Chọn loại đối tượng",
                allowClear: true,
                width: '100%'
            });

            $('#type').select2({
                placeholder: "Chọn loại phiếu",
                allowClear: true,
                width: '100%'
            });

            $('#corresponding-account').select2({
                placeholder: "Chọn tài khoản đổi ứng",
                allowClear: true,
                width: '100%'
            });

            $('#account_id').select2({
                placeholder: "Chọn tài khoản",
                allowClear: true,
                width: '100%'
            });

            let typingTimer;
            let doneTypingInterval = 500;

            $('#object-type').on('change', function() {
                $('#object_code').val('');
                $('input[name="objectable_id"]').val('');
                $('#object-search-result').hide();

                const type = $(this).val();
                const $correspondingWrapper = $('#corresponding-account-wrapper');

                if (type === 'employee') {
                    $correspondingWrapper.removeClass('d-none');
                } else {
                    $correspondingWrapper.addClass('d-none');
                    $('#corresponding-account').val(''); // clear giá trị nếu không phải nhân viên
                }
            });

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
                $('input[name="obj_id"]').val(id);
                $('#object-search-result').hide();
            });

            $(document).click(function(e) {
                if (!$(e.target).closest('#object-search-result, #object_code').length) {
                    $('#object-search-result').hide();
                }
            });

            let basePath = window.location.pathname.includes('/bank-transactions') ?
                '/admin/bank-transactions' :
                '/admin/cash-transactions';

            let url =
                '{{ !empty($transaction) && !empty($mainEntry) && !empty($contraEntry) ? 'update' : 'store' }}';

            let fullUrl = `${basePath}/${url}`;

            console.log(fullUrl);


            submitForm('#myForm', function(response) {
                window.location.href = response.redirect;
            }, fullUrl);

            const fileInput = document.getElementById('fileInput');
            const triggerFileInput = document.getElementById('triggerFileInput');
            const filePreviewArea = document.getElementById('filePreviewArea');
            const removeAttachmentBtn = document.getElementById('removeAttachmentBtn');
            const removeAttachment = document.getElementById('removeAttachment');

            // Click nút chọn file
            triggerFileInput?.addEventListener('click', () => {
                fileInput.click();
            });

            // Preview file mới khi chọn
            fileInput?.addEventListener('change', () => {
                const file = fileInput.files[0];
                filePreviewArea.innerHTML = ''; // Clear preview
                if (!file) return;

                const fileType = file.type;
                if (fileType.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-thumbnail';
                    img.style.maxWidth = '200px';
                    img.onload = () => URL.revokeObjectURL(img.src);
                    filePreviewArea.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = URL.createObjectURL(file);
                    iframe.width = '200';
                    iframe.height = '250';
                    iframe.onload = () => URL.revokeObjectURL(iframe.src);
                    filePreviewArea.appendChild(iframe);
                } else {
                    const div = document.createElement('div');
                    div.innerHTML = `<i class="bi bi-file-earmark-text me-1"></i> ${file.name}`;
                    filePreviewArea.appendChild(div);
                }
            });

            // Xoá file đính kèm hiện tại
            removeAttachmentBtn?.addEventListener('click', () => {
                if (confirm('Bạn có chắc chắn muốn xoá file đính kèm này?')) {
                    removeAttachment.value = '1';
                    removeAttachmentBtn.closest('div').remove(); // Ẩn block file đã có
                }
            });

            // Form validation
            const form = document.querySelector('form') || document.createElement('form');
            const requiredFields = document.querySelectorAll('[required]');

            function validateForm() {
                let isValid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                return isValid;
            }

            // Add validation on submit
            document.querySelector('.btn-primary').addEventListener('click', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ các trường bắt buộc!');
                }
            });

            $('#addVoucherTypeBtn').on('click', function(e) {
                e.preventDefault();

                // Clear toàn bộ input, textarea bên trong form modal
                $(this).find('input[type="text"], textarea').val('');

                // Nếu muốn focus vào ô đầu tiên
                $(this).find('input[type="text"]').first().focus();
                $('#addVoucherTypeModal').modal('show');
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">

    <style>
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0;
        }

        .section-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 16px;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-content {
            padding: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .required {
            color: #dc3545;
        }

        .form-control,
        .form-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #6c757d;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #fafafa;
            margin-top: 10px;
        }

        .file-upload-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .btn-file {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            padding: 6px 16px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-file:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .section-divider {
            border-left: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .section-divider {
                border-left: none;
                border-top: 1px solid #dee2e6;
                margin-top: 20px;
                padding-top: 20px;
            }
        }
    </style>
@endpush
