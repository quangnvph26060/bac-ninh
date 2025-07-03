@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'tạo phiếu thu chi']]" />
        </div>

        <div class="form-container">
            <form id="myForm">

                @if (!empty($cashTransaction))
                    @method('PUT')
                @endif

                <div class="row g-0 ">
                    <!-- Thông tin Section -->
                    <div class="col-lg-8 pe-0">
                        <div class="section-header">
                            <i class="fas fa-info-circle"></i>
                            Thông tin
                        </div>
                        <div class="section-content">
                            <div class="row g-3">
                                <!-- Ngày thu chi -->
                                <div class="col-md-6 ">
                                    <label class="form-label">Ngày thu chi</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="date"
                                            value="{{ !empty($cashTransaction) ? \Carbon\Carbon::parse($cashTransaction->date)->format('Y-m-d') : date('Y-m-d') }}">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Loại chứng từ -->
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-content-center">
                                        <label class="form-label d-flex align-items-center">
                                            Loại chứng từ
                                        </label>
                                        <a href="#" id="addVoucherTypeBtn"
                                            class="ms-2 text-primary text-decoration-underline" title="Thêm loại chứng từ">
                                            <i class="fas fa-plus-circle"></i> Thêm loại chứng từ
                                        </a>
                                    </div>
                                    <select class="form-select" id="voucher_type_id" name="voucher_type_id">
                                        <option value="">Chọn loại chứng từ</option>
                                        @foreach ($voucherTypes as $typeId => $typeName)
                                            <option value="{{ $typeId }}" @selected(optional($cashTransaction)->voucher_type_id == $typeId)>
                                                {{ $typeName }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Tài khoản tiền mặt -->
                                <div class="col-md-6">
                                    <label class="form-label">Tài khoản<span class="required">*</span></label>
                                    <select class="form-select" name="cash_account_id">
                                        <option value="">Chọn tài khoản</option>
                                        {{-- <option value="tk-111">TK 111 - Tiền mặt</option>
                                    <option value="tk-112">TK 112 - Tiền gửi ngân hàng</option> --}}
                                        @foreach ($orderedAccounts as $account)
                                            <option @selected(optional($cashTransaction)->cash_account_id == $account->id) value="{{ $account->id }}">
                                                {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $account->level_display) !!} {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Loại phiếu -->
                                <div class="col-md-6">
                                    <label class="form-label">Loại phiếu <span class="required">*</span></label>
                                    <select class="form-select" name="type">
                                        <option value="">Chọn loại phiếu</option>
                                        <option @selected(optional($cashTransaction)->type === 'income') value="income">Phiếu thu</option>
                                        <option @selected(optional($cashTransaction)->type === 'expense') value="expense">Phiếu chi</option>
                                    </select>
                                </div>

                                <!-- File Upload -->
                                <div class="col-12">
                                    <div class="file-upload-area">
                                        {{-- Hiển thị file đã có nếu đang cập nhật --}}
                                        @if (!empty($cashTransaction) && $cashTransaction->attachment)
                                            <div class="mb-2 d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ asset('storage/' . $cashTransaction->attachment) }}"
                                                    target="_blank"
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

                                        {{-- Preview file mới khi chọn --}}
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

                                        {{-- Input ẩn để đánh dấu cần xoá file cũ nếu xoá --}}
                                        <input type="hidden" name="remove_attachment" id="removeAttachment" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thanh toán Section -->
                    <div class="col-lg-4 section-divider ps-0">
                        <div class="section-header">
                            <i class="fas fa-credit-card"></i>
                            Thanh toán
                        </div>
                        <div class="section-content">
                            <!-- Số tiền -->
                            <div class="mb-3">
                                <label class="form-label">Số tiền (USD) <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <input type="text" value="{{ formatPrice(optional($cashTransaction)->amount) }}"
                                        class="form-control usd-price-format" name="amount" placeholder="0">
                                </div>
                            </div>

                            <!-- Ghi chú -->
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fab fa-rocketchat"></i>
                                    </span>
                                    <textarea class="form-control" name="description" placeholder="Nhập ghi chú...">{{ optional($cashTransaction)->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-top p-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/admin/cashbook" class="btn btn-outline-secondary btn-sm">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            submitForm('#voucherTypeForm', function(response) {
                if (response.success) {
                    const data = response.data;

                    // Tạo option mới
                    const newOption = $('<option>', {
                        value: data.id,
                        text: data.name,
                        selected: true
                    });

                    // Thêm ngay sau option "Chọn loại chứng từ"
                    $('#voucher_type_id option:eq(0)').after(newOption);

                    // Đóng modal
                    $('#addVoucherTypeModal').modal('hide');

                } else {
                    Notifications('Có lỗi xảy ra, vui lòng thử lại', 'error');
                }

            }, '/admin/cashbook/voucher-types');

            let url =
                '{{ !empty($cashTransaction) ? "/admin/cashbook/update/$cashTransaction->id" : '/admin/cashbook/store' }}'

            submitForm('#myForm', function(response) {
                window.location.href = response.redirect
            }, url)

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
