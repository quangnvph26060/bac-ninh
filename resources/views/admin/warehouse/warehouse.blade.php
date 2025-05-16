@extends('admin.layout.index')

@section('content')
    @if (empty($material))
        @php
            $material = null;
        @endphp
    @endif

    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'Lịch sử nhập hàng', 'url' => route('admin.warehouse.index')],
                ['name' => 'Nhập hàng'],
            ]" />
        </div>

        <div class="gap-3">
            <div class="card">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="mb-3 position-relative col-md-12">
                                <label for="name" class="form-label">Tên vật liệu</label>
                                <input type="text" placeholder="Tên vật liệu" class="form-control" name="name"
                                    id="name" aria-="true" value="" onfocus="showPopup()"
                                    oninput="filterItems()">

                                <div id="popup" class="popup card" style="display: none;">
                                    <ul id="popupList" class="list-group">

                                    </ul>
                                    <div class="d-flex justify-content-end p-2">
                                        <button id="addBtn" class="btn btn-primary btn-sm" disabled>Thêm</button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="itemsTable" style="display: none">
            <div class="card-body">
                <div class="form-body">
                    <div id="selectedItemsTable" class="mt-3"></div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const items = @json($listdata);
        const supplier = @json($suppliers);
        console.log(supplier);
        const selectedItems = new Set();

        const popup = document.getElementById('popup');
        const popupList = document.getElementById('popupList');
        const input = document.getElementById('name');
        const itemsTable = document.getElementById('itemsTable');

        function showPopup() {
            popup.style.display = 'block';
            filterItems();
            positionPopup();
        }

        window.showPopup = showPopup;
        window.filterItems = filterItems;

        function renderPopup(displayItems) {
            popupList.innerHTML = '';

            if (displayItems.length === 0) {
                popupList.innerHTML = '<li class="list-group-item">Không tìm thấy vật liệu</li>';
                return;
            }

            displayItems.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('list-group-item');

                const isChecked = selectedItems.has(item.id) ? 'checked' : '';

                li.innerHTML = `
                <input
                    type="checkbox"
                    id="checkbox-${item.id}"
                    data-id="${item.id}"
                    data-type="${item.type}"
                    ${item.type !== 'normal' ? `data-nameparent="${item.name_parent}"` : ''}
                    class="me-2 checkbox-item"
                    ${isChecked}
                >
                <label for="checkbox-${item.id}">
                    ${item.type === 'normal' ? item.name : `${item.name_parent} - ${item.name}`}
                </label>
            `;
                popupList.appendChild(li);
            });

            document.querySelectorAll('.checkbox-item').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = parseInt(this.dataset.id);
                    if (this.checked) {
                        selectedItems.add(id);
                    } else {
                        selectedItems.delete(id);
                    }

                    const addBtn = document.getElementById('addBtn');
                    addBtn.disabled = selectedItems.size === 0;
                });
            });
        }

        function filterItems() {
            const keyword = input.value.toLowerCase();
            const filtered = items.filter(item =>
                item.name.toLowerCase().includes(keyword) ||
                (item.name_parent && item.name_parent.toLowerCase().includes(keyword))
            );
            renderPopup(filtered);
        }

        function positionPopup() {
            popup.style.left = `0px`;
            popup.style.top = `100px`;
        }

        document.addEventListener('click', function(event) {
            if (!popup.contains(event.target) && event.target !== input) {
                popup.style.display = 'none';
            }
        });

        document.getElementById('addBtn').addEventListener('click', function() {
            itemsTable.style.display = 'block';
            const selectedData = items.filter(item => selectedItems.has(item.id));
            renderSelectedTable(selectedData);
        });

        function renderSelectedTable(data, previousValues = {}) {
            const tableDiv = document.getElementById('selectedItemsTable');
            let table = tableDiv.querySelector('table');

            if (!table) {
                tableDiv.innerHTML = `
                    <form id="submitForm">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;"></th>
                                    <th style="width: 25%;">Tên vật liệu</th>
                                    <th style="width: 10%;">Số lượng</th>
                                    <th style="width: 20%;">Đơn giá nhập</th>
                                    <th style="width: 20%;">Nhà phân phối</th>
                                    <th style="width: 20%;">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="d-flex justify-content-end p-2">
                            <button id="addsubmit" class="btn btn-primary btn-sm" type="submit">Thêm</button>
                        </div>
                    </form>
                `;
                table = tableDiv.querySelector('table');

                document.getElementById('submitForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const rows = this.querySelectorAll('tbody tr');
                    const tableData = [];
                    let hasError = false;

                    for (let index = 0; index < rows.length; index++) {
                        const row = rows[index];
                        const quantity = row.querySelector('input[name="quantity[]"]').value.trim();
                        const price = row.querySelector('input[name="price[]"]').value.trim();
                        const supplier = row.querySelector('select[name="supplier[]"]').value.trim();

                        const id = row.querySelector('input[name="id[]"]').value;
                        const type = row.querySelector('input[name="type[]"]').value;
                        const currency = row.querySelector('select[name="currency[]"]').value;
                        const note = row.querySelector('input[name="note[]"]').value;

                        const errors = [];

                        if (!quantity || isNaN(quantity) || Number(quantity) <= 0) {
                            errors.push("• Số lượng không hợp lệ");
                        }

                        if (!price) {
                            errors.push("• Đơn giá không hợp lệ");
                        }

                        if (!supplier) {
                            errors.push("• Chưa chọn nhà phân phối");
                        }

                        if (errors.length > 0) {
                            hasError = true;
                            Notifications(`Dòng ${index + 1}:\n${errors.join(' - ')}`, 'danger');
                            break; // Dừng kiểm tra các dòng tiếp theo
                        } else {
                            tableData.push({
                                id,
                                type,
                                quantity,
                                price,
                                currency,
                                supplier,
                                note
                            });
                        }
                    }


                    if (hasError) {
                        return;
                    }

                    fetch('/submit-table', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                data: tableData
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert('✅ Gửi dữ liệu thành công!');
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        })
                        .catch(error => {
                            console.error('Lỗi:', error);
                            alert('❌ Có lỗi xảy ra khi gửi!');
                        });
                });



            }

            const tbody = table.querySelector('tbody');
            const currentRows = tbody.querySelectorAll('tr');
            const currentData = {};

            currentRows.forEach(row => {
                const id = parseInt(row.querySelector('input[name="id[]"]').value);
                currentData[id] = {
                    row: row,
                    values: {
                        quantity: row.querySelector('input[name="quantity[]"]').value,
                        price: row.querySelector('input[name="price[]"]').value,
                        currency: row.querySelector('select[name="currency[]"]').value,
                        supplier: row.querySelector('select[name="supplier[]"]').value,
                        note: row.querySelector('input[name="note[]"]').value
                    }
                };
            });

            Object.keys(currentData).forEach(id => {
                if (!selectedItems.has(parseInt(id))) {
                    currentData[id].row.remove();
                }
            });

            data.forEach(item => {
                if (currentData[item.id]) return;

                const saved = previousValues[item.id] || {};
                const tr = document.createElement('tr');

                const supplierOptions = supplier.map(sup => {
                    const selected = saved.supplier == sup.id ? 'selected' : '';
                    return `<option value="${sup.id}" ${selected}>${sup.company_name}</option>`;
                }).join('');

                tr.innerHTML = `
                    <input type="hidden" name="id[]" value="${item.id}" />
                    <input type="hidden" name="type[]" value="${item.type}" />
                    <td>
                        <button type="button" onclick="removeItem(${item.id})" class="btn btn-sm btn-link text-danger p-0">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                    <td>${item.type === 'normal' ? item.name : `${item.name_parent} - ${item.name}`}</td>
                    <td>
                        <input type="number" name="quantity[]" value="${saved.quantity || 1}" min="1"
                            class="form-control form-control-sm text-end" />
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <input type="text" name="price[]" value="${saved.price || ''}"
                                class="form-control form-control-sm text-end" style="width: 60%;" />
                            <select name="currency[]" class="form-select form-select-sm" style="width: 40%;">
                                <option value="vnd" ${saved.currency == 'vnd' ? 'selected' : ''}>đ</option>
                                <option value="usd" ${saved.currency == 'usd' ? 'selected' : ''}>$</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <select name="supplier[]" class="form-select form-select-sm select2-supplier">
                            <option value="">Chọn</option>
                            ${supplierOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" name="note[]" value="${saved.note || ''}"
                            class="form-control form-control-sm" />
                    </td>
                `;

                tbody.appendChild(tr);
                attachPriceFormatListeners(tr);

                // Kích hoạt select2
                $(tr).find('.select2-supplier').select2({
                    width: '100%'
                });
            });

            itemsTable.style.display = 'block';
            popup.style.display = 'none';
        }

        function formatPriceInput(value, currency) {
            let cleanValue = currency === 'usd' ?
                value.replace(/[^0-9.]/g, '') :
                value.replace(/[^0-9]/g, '');


            if (currency === 'usd') {
                const parts = cleanValue.split('.');
                if (parts.length > 2) {
                    cleanValue = parts.shift() + '.' + parts.join('');
                }
            }

            if (!cleanValue) return '';


            let number = currency === 'usd' ? parseFloat(cleanValue) : parseInt(cleanValue);

            if (isNaN(number)) return '';

            if (currency === 'usd') {
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else {
                return number.toLocaleString('vi-VN');
            }
        }

        function attachPriceFormatListeners(row) {
            const priceInput = row.querySelector('input[name="price[]"]');
            const currencySelect = row.querySelector('select[name="currency[]"]');

            priceInput.addEventListener('input', (e) => {
                const currency = currencySelect.value;
                const oldValue = priceInput.value;
                const cursorPos = priceInput.selectionStart;

                const formattedValue = formatPriceInput(oldValue, currency);

                if (formattedValue !== oldValue) {
                    priceInput.value = formattedValue;

                    let newCursorPos = cursorPos + (formattedValue.length - oldValue.length);

                    newCursorPos = Math.min(newCursorPos, formattedValue.length);
                    newCursorPos = Math.max(newCursorPos, 0);

                    priceInput.setSelectionRange(newCursorPos, newCursorPos);
                }
            });

            currencySelect.addEventListener('change', () => {
                priceInput.value = formatPriceInput(priceInput.value, currencySelect.value);
            });
        }




        function removeItem(id) {
            const previousValues = {};
            const rows = document.querySelectorAll('#selectedItemsTable table tbody tr');
            rows.forEach(row => {
                const itemId = parseInt(row.querySelector('input[name="id[]"]').value);
                if (itemId !== id) {
                    previousValues[itemId] = {
                        // date: row.querySelector('input[name="date[]"]').value,
                        quantity: row.querySelector('input[name="quantity[]"]').value,
                        price: row.querySelector('input[name="price[]"]').value,
                        currency: row.querySelector('select[name="currency[]"]').value,
                        supplier: row.querySelector('select[name="supplier[]"]').value,
                        note: row.querySelector('input[name="note[]"]').value,
                    };
                }
            });

            selectedItems.delete(id);

            const checkbox = document.querySelector(`input[type="checkbox"][data-id="${id}"]`);
            if (checkbox) checkbox.checked = false;

            if (selectedItems.size === 0) {
                document.getElementById('selectedItemsTable').innerHTML = `
                        <div class="text-center text-muted py-3">
                            <h3>Không có vật liệu nào được chọn</h3>
                        </div>
                    `;
                return;
            }

            const selectedData = items.filter(item => selectedItems.has(item.id));
            renderSelectedTable(selectedData, previousValues);
        }
    </script>
@endpush

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .popup {
            position: absolute;
            border: 1px solid #e0e0e0;
            background-color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            width: 100%;
            z-index: 1050;
            max-height: 300px;
            overflow-y: auto;
            transition: all 0.2s ease-in-out;
            padding: 0.5rem 0;
            top: calc(100% + 4px);
            padding: 0px;

        }

        .list-group-item {
            cursor: pointer;
            padding: 10px 16px;
            font-size: 15px;
            color: #333;
            border: none;
            background-color: transparent;
            transition: background-color 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f1f3f5;
        }

        .position-relative {
            position: relative;
        }

        table.no-border,
        table.no-border td,
        table.no-border th,
        table.no-border input,
        table.no-border select {
            border: none !important;
            outline: none;
        }

        table.no-border input,
        table.no-border select {
            width: 100%;
            background-color: transparent;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th {
            padding: 5px 10px !important;
        }
    </style>
@endpush
