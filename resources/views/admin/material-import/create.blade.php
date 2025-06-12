@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'phiếu nhập', 'url' => '/admin/material-imports'], ['name' => 'Tạo phiếu nhập']]" />
        </div>

        <form method="post" id="my-form">

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
                                        placeholder="Nhập mã phiếu" value="">
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
                                                <option value="{{ $supplierId }}">
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
                            <h4 class="card-title fw-bold">Bảng giá</h4>
                            <i class="fas fa-chevron-up" id="collapse-icon"></i>
                        </div>
                        <div class="collapse show" id="price-summary">
                            <div class="card-body p-3">
                                <div class="row mb-2">
                                    <div class="col-6">Tổng tiền (<span id="summary-count">0</span>)</div>
                                    <div class="col-6 text-end"><span id="summary-total">0.00</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Đã trả</div>
                                    <div class="col-6 text-end">
                                        <input type="text" class="form-control form-control-sm text-end"
                                            name="summary_paid" id="summary-paid" value="0">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Nợ NCC</div>
                                    <div class="col-6 text-end"><span id="summary-debt">0.00</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <textarea class="form-control" name="note" rows="2" placeholder="Ghi chú">{{ $materialImport->note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-save me-2"></i>Lưu
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.material-imports.index') }}"
                                        class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-undo-alt me-2"></i>Quay lại
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
                defaultDate: "today"
            });

            let currentPage = 1;
            let searchTimeout;
            let selectedProducts = [];

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

            function updateSummary() {
                let total = 0;
                let count = 0;
                $('#selected-products tbody tr').each(function() {
                    const price = parseFloat($(this).find('.price').val()) || 0;
                    const qty = parseFloat($(this).find('.quantity').val()) || 0;
                    total += price * qty;
                    if (qty > 0) count++;
                });

                let pay = total;
                let paidInput = $('#summary-paid');
                let paid = parseFloat(paidInput.val().replace(/,/g, '')) || 0;

                // Không cho nhập vượt quá tổng tiền
                if (paid > pay) {
                    paid = pay;
                    paidInput.val(paid); // Cập nhật lại ô input
                }

                let debt = pay - paid;

                $('#summary-count').text(count);
                $('#summary-total').text(formatNumber(total) + ' USD');
                $('#summary-debt').text(formatNumber(debt) + ' USD');
            }

            // Gọi lại khi thay đổi giá trị
            $(document).on('input', '.price, .quantity, #summary-paid', updateSummary);
            $(document).on('click', '.delete-row', updateSummary);

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
