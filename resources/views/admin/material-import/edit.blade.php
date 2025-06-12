@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'phiếu nhập', 'url' => '/admin/material-imports'],
                    [
                        'name' => "Cập nhật phiếu nhập - {$materialImport->code}",
                    ],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>

        <form method="post" id="my-form">

            @method('PUT')

            <div class="row">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3 position-relative">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="product-search"
                                            placeholder="Tìm kiếm vật tư...">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addMaterialModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div id="search-results"
                                        class="position-absolute bg-white border rounded shadow-sm d-none"
                                        style="left: 15px; right:15px; z-index: 1000;">
                                        <div class="list-group list-group-flush" id="search-results-list"></div>
                                        <div class="d-flex justify-content-between p-2 border-top">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="prev-page">Trang trước</button>
                                            <span id="page-info">Trang 1</span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                id="next-page">Trang sau</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="selected-products">
                                            <thead>
                                                <tr>
                                                    <th width="50">STT</th>
                                                    <th>Tên vật tư</th>
                                                    <th width="150">Đơn vị</th>
                                                    <th width="150">Đơn giá (USD)</th>
                                                    <th width="150">Số lượng</th>
                                                    <th width="150">Thành tiền (USD)</th>
                                                    <th width="100">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($materialImport->details ?? [] as $detail)
                                                    <tr data-id="${materialId}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {{ $detail->material->name }}
                                                        </td>
                                                        <td> {{ $detail->material->unit }}</td>
                                                        <td><input type="number"
                                                                name="materials[{{ $detail->material->id }}][unit_price]"
                                                                class="form-control form-control-sm price"
                                                                value="{{ formatPrice($detail->unit_price) }}">
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                name="materials[{{ $detail->material->id }}][quantity]"
                                                                class="form-control form-control-sm quantity"
                                                                value="{{ formatNumber($detail->quantity) }}">
                                                        </td>
                                                        <td class="total text-center">
                                                            {{ formatPrice($detail->unit_price * $detail->quantity) }}</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title fw-bold">Thông tin phiếu nhập</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label for="code" class="form-label">Mã phiếu nhập</label>
                                    <input type="text" class="form-control" name="code" id="code"
                                        placeholder="Nhập mã phiếu" value="{{ $materialImport->code }}">
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="date" class="form-label">Ngày nhập</label>
                                    <input type="text" class="form-control form-date-time" name="date" id="date"
                                        value="">
                                </div>

                                <hr>

                                <div class="col-lg-12 mb-3">
                                    <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                                    <div class="input-group">
                                        <select name="supplier_id" id="supplier_id" class="form-select">
                                            <option value="">-- Chọn nhà cung cấp --</option>
                                            @foreach ($suppliers as $supplierId => $supplierName)
                                                <option value="{{ $supplierId }}" @selected($supplierId === $materialImport->supplier_id)>
                                                    {{ $supplierName }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addSupplierModal">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between" style="cursor:pointer;"
                            data-bs-toggle="collapse" data-bs-target="#price-summary" aria-expanded="true">
                            <h5 class="card-title fw-bold mb-0">Bảng giá</h5>
                            <i class="fas fa-chevron-up" id="collapse-icon"></i>
                        </div>
                        <div class="collapse show" id="price-summary">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng tiền (<span id="summary-count">0</span>)</span>
                                    <strong id="summary-total">0.00</strong>
                                </div>

                                <hr class="my-2">

                                <div id="payment-list">
                                    @foreach ($materialImport->debt->payments as $index => $payment)
                                        <div class="row mb-2 payment-item">
                                            <div class="col-5 pe-1">
                                                <input type="text" name="payments[{{ $payment->id }}][amount]"
                                                    class="form-control form-control-sm text-end payment-amount usd-price-format"
                                                    placeholder="Số tiền" value="{{ formatPrice($payment->amount) }}">
                                            </div>
                                            <div class="col-5 ps-1 pe-1">
                                                <input type="text" name="payments[{{ $payment->id }}][date]"
                                                    class="form-control form-control-sm form-date-time" placeholder="Ngày"
                                                    value="{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}">
                                            </div>
                                            <div class="col-1 ps-0 d-flex align-items-center">
                                                <button type="button" class="btn btn-sm text-danger remove-payment"
                                                    title="Xóa">
                                                    <i class="fas fa-times small"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" id="add-payment"
                                    class="btn btn-sm btn-outline-primary w-100 mb-2">
                                    + Thêm thanh toán
                                </button>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Nợ NCC</span>
                                    <strong id="summary-debt">0.00</strong>
                                </div>

                                <textarea class="form-control form-control-sm" name="note" rows="2" placeholder="Ghi chú">{{ $materialImport->note ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="card-footer p-2">
                            <div class="row gx-2">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-save me-1"></i>Lưu
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.material-imports.index') }}"
                                        class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-undo-alt me-1"></i>Quay lại
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="new-supplier-addition-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSupplierModalLabel">Thêm nhà cung cấp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-lg-6">
                                <label for="company_name" class="form-label required">Tên nhà cung cấp</label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="representative_name" class="form-label required">Người đại diện</label>
                                <input type="text" class="form-control" id="representative_name"
                                    name="representative_name">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="phone" class="form-label required">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <input type="text" class="form-control" id="email" name="email">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="tax_code" class="form-label">Mã số thuế</label>
                                <input type="text" class="form-control" id="tax_code" name="tax_code">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="bank_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" class="form-control" id="bank_account_number"
                                    name="bank_account_number">
                            </div>

                            <div class="mb-3 col-lg-6">
                                <label for="bank_id " class="form-label">Ngân hàng</label>
                                <select name="bank_id" id="bank_id" class="form-select">
                                    <option value="">--- Chọn ngân hàng ---</option>
                                    @foreach ($banks as $bankId => $bankName)
                                        <option value="{{ $bankId }}">{{ $bankName }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal thêm vật tư mới -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="new-material-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMaterialModalLabel">Thêm vật tư mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 position-relative">
                                <label for="material_name" class="form-label required">Tên vật tư</label>
                                <input type="text" name="name" id="material_name" class="form-control"
                                    placeholder="Nhập tên vật tư" value="">
                            </div>

                            <div class="col-lg-6 mb-3 position-relative">
                                <label for="material_code" class="form-label">Mã vật tư</label>
                                <input type="text" name="code" id="material_code" class="form-control"
                                    placeholder="Nhập mã vật tư" aria-label="code" value="">
                            </div>

                            <div class="col-lg-3 mb-3">
                                <label for="material_min_stock" class="form-label">Số lượng báo động</label>
                                <input type="text" name="min_stock" id="material_min_stock" class="form-control"
                                    value="0">
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label for="material_unit" class="form-label required">Đơn vị</label>
                                <input list="units" type="text" name="unit" id="material_unit"
                                    class="form-control" placeholder="Nhập đơn vị" value="">

                                <datalist id="units">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit }}">
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="col-lg-12 mb-3 position-relative">
                                <label for="material_note" class="form-label">Ghi chú</label>
                                <textarea name="note" id="material_note" class="form-control" placeholder="Nhập ghi chú"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>

    <script>
        updateCharCount("#material_name", 250);
        updateCharCount("#material_code", 8);
        updateCharCount("#material_note", 255);

        convertToAsciiUpper("#material_code")
        convertToAsciiUpper("#code")

        $(document).ready(function() {
            flatpickr(".form-date-time", {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "vn",
                defaultDate: "{{ $materialImport->date->format('d/m/Y') }}"
            });

            let currentPage = 1;
            let searchTimeout;
            let selectedProducts = @json($materialImport->details->pluck('material_id'));

            $(document).on('click', function(event) {
                if (!$(event.target).closest('#product-search, #search-results').length) {
                    $('#search-results').addClass('d-none');
                }
            });

            // Xử lý tìm kiếm sản phẩm
            $('#product-search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();

                if (searchTerm.length > 0) {
                    searchTimeout = setTimeout(() => {
                        searchProducts(searchTerm, currentPage);
                    }, 300);
                } else {
                    $('#search-results').addClass('d-none');
                }
            });

            $('#product-search').on('focus', function() {

                if ($('#search-results-list a').length > 0) {
                    $('#search-results').removeClass('d-none');
                    return;
                }

                searchProducts('', currentPage);
            })

            // Xử lý phân trang
            $('#prev-page').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    searchProducts($('#product-search').val(), currentPage);
                }
            });

            $('#next-page').on('click', function() {
                currentPage++;
                searchProducts($('#product-search').val(), currentPage);
            });

            // Hàm tìm kiếm sản phẩm
            function searchProducts(term, page) {
                $.get('/admin/materials/search', {
                    term: term,
                    page: page
                }, function(response) {
                    const resultsList = $('#search-results-list');
                    resultsList.empty();

                    const {
                        data,
                        meta
                    } = response.data;

                    data.forEach(material => {
                        resultsList.append(`
                            <a href="#" class="list-group-item list-group-item-action fw-bold py-3"
                                data-id="${material.id}"
                                data-name="${material.name}"
                                data-unit="${material.unit}">
                                ${material.name} - ${material.unit}
                            </a>
                        `);
                    });

                    // Update page info
                    $('#page-info').text(
                        `Trang ${meta.current_page} / ${meta.last_page}`
                    );

                    // Disable/enable pagination buttons
                    $('#prev-page').prop('disabled', meta.current_page === 1);
                    $('#next-page').prop('disabled', meta.current_page === meta.last_page);

                    $('#search-results').removeClass('d-none');
                });
            }

            // Xử lý chọn sản phẩm
            $(document).on('click', '#search-results-list a', function(e) {
                e.preventDefault();
                const materialId = $(this).data('id');
                const materialName = $(this).data('name');
                const unit = $(this).data('unit');

                if (!selectedProducts.includes(materialId)) {
                    selectedProducts.push(materialId);

                    console.log(selectedProducts);

                    const rowCount = $('#selected-products tbody tr').length;

                    $('#selected-products tbody').append(`
                        <tr data-id="${materialId}">
                            <td>${rowCount + 1}</td>
                            <td>
                                ${materialName}
                            </td>
                            <td>${unit}</td>
                            <td><input type="number" name="materials[${materialId}][unit_price]" class="form-control form-control-sm price" value="0"></td>
                            <td><input type="number" name="materials[${materialId}][quantity]" class="form-control form-control-sm quantity" value="1"></td>
                            <td class="total text-center">$0</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                }

                $('#search-results').addClass('d-none');
                $('#product-search').val('');
            });

            // Xử lý xóa hàng
            $(document).on('click', '.delete-row', function() {
                const row = $(this).closest('tr');
                const productId = row.data('id');

                selectedProducts = selectedProducts.filter(id => id !== productId);
                row.remove();

                // Cập nhật lại STT
                $('#selected-products tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });

            // Tính toán thành tiền
            $(document).on('input', '.price, .quantity', function() {
                const row = $(this).closest('tr');
                const price = parseFloat(row.find('.price').val()) || 0;
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const total = price * quantity;

                row.find('.total').text(formatNumber(total));
            });

            // Xử lý thêm vật tư mới
            submitForm('#new-material-form', function(response) {
                const material = response.data;

                // Add to search results list
                const resultsList = $('#search-results-list');
                const newItem = $(`
                    <a href="#" class="list-group-item list-group-item-action fw-bold py-3"
                        data-id="${material.id}"
                        data-name="${material.name}"
                        data-unit="${material.unit}">
                        ${material.name} - ${material.unit}
                    </a>
                `);
                resultsList.prepend(newItem);

                // Automatically select the newly added item
                newItem.trigger('click');

                // Hide modal and reset form
                $('#addMaterialModal').modal('hide');
                $('#new-material-form')[0].reset();
            }, '/admin/materials/create');

            submitForm('#my-form', function(response) {
                window.location.href = "/admin/material-imports"
            });

            submitForm('#new-supplier-addition-form', function(response) {
                const supplier = response.data;
                const select = $('#supplier_id');

                // Tạo option mới
                const newOption = $('<option>', {
                    value: supplier.id,
                    text: supplier.name,
                    selected: true
                });

                // Thêm option mới ngay sau option đầu tiên
                select.find('option:eq(0)').after(newOption);
                // Ẩn modal và reset form
                $('#addSupplierModal').modal('hide');
                $('#new-supplier-addition-form')[0].reset();
            }, '/admin/suppliers');

            let paymentIndex = 0;

            function updateSummary() {
                let total = 0,
                    count = 0;

                $('#selected-products tbody tr').each(function() {
                    const price = parseFloat($(this).find('.price').val()) || 0;
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    total += price * qty;
                    if (qty > 0) count++;
                });

                let paid = 0;
                $('.payment-amount').each(function() {
                    let val = parseFloat($(this).val().replace(/,/g, '')) || 0;
                    paid += val;

                    // Ràng buộc: nếu nhập vượt quá tổng - phần còn lại
                    const remaining = total - (paid - val); // trừ phần đang xét ra
                    if (val > remaining) {
                        $(this).val(remaining.toFixed(2));
                        paid = paid - val + remaining;
                    }
                });

                const debt = total - paid;

                $('#summary-count').text(count);
                $('#summary-total').text(formatNumber(total) + ' USD');
                $('#summary-debt').text(formatNumber(debt) + ' USD');

                // Nếu đã thanh toán đủ, ẩn nút thêm
                if (debt <= 0) {
                    $('#add-payment').prop('disabled', true);
                } else {
                    $('#add-payment').prop('disabled', false);
                }
            }

            // Gọi lại khi thay đổi giá trị
            $(document).on('input', '.price, .quantity, .payment-amount', updateSummary);

            // Xóa thanh toán
            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-item').remove();
                updateSummary();
            });

            // Thêm dòng thanh toán
            $('#add-payment').on('click', function() {
                let total = 0;
                $('#selected-products tbody tr').each(function() {
                    const price = parseFloat($(this).find('.price').val()) || 0;
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    total += price * qty;
                });

                let paid = 0;
                $('.payment-amount').each(function() {
                    paid += parseFloat($(this).val().replace(/,/g, '')) || 0;
                });

                const debt = total - paid;

                if (debt <= 0) {
                    Notifications('Đã thanh toán đủ, không thể thêm dòng mới.', "danger");
                    return;
                }

                // 👉 Tạo ID ngẫu nhiên 8 ký tự
                const uid = 'pm_' + Math.random().toString(36).substr(2, 8);

                const html = `
                    <div class="row mb-2 payment-item">
                        <div class="col-5 pe-1">
                            <input type="text" name="payments[${uid}][amount]" class="form-control form-control-sm text-end payment-amount usd-price-format" placeholder="Số tiền">
                        </div>
                        <div class="col-5 ps-1 pe-1">
                            <input type="text" name="payments[${uid}][date]" class="form-control form-control-sm form-date-time" placeholder="Ngày">
                        </div>
                        <div class="col-1 ps-0 d-flex align-items-center">
                            <button type="button" class="btn btn-sm text-danger remove-payment" title="Xóa">
                                <i class="fas fa-times small"></i>
                            </button>
                        </div>
                    </div>`;

                $('#payment-list').append(html);

                flatpickr('.form-date-time', {
                    dateFormat: 'd/m/Y'
                });

                updateSummary();
            });


            flatpickr('.form-date-time', {
                dateFormat: 'd/m/Y'
            });

            updateSummary();

            // Hiệu ứng icon thu gọn/mở rộng
            $('#price-summary').on('show.bs.collapse', function() {
                $('#collapse-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });
            $('#price-summary').on('hide.bs.collapse', function() {
                $('#collapse-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

            // Gọi lần đầu
            updateSummary();
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">
    <style>
        #search-results {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
@endpush
