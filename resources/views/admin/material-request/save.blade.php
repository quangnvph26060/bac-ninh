@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'yêu cầu xuất vật tư', 'url' => '/admin/material-requests'],
                ['name' => 'tạo yêu cầu'],
            ]" />
        </div>

        <form action="" method="post" id="myForm">
            @isset($materialRequest)
                @method('PUT')
            @endisset
            <div class="row">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <!-- Chọn đơn hàng -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chọn đơn hàng</label>
                                <select class="form-select" name="order_id" id="order_id" style="width: 100%;"
                                    @disabled(isset($order))>
                                    @isset($order)
                                        <option value="{{ $order->id }}">
                                            {{ $order->order_code . ' - ' . $order->order_name . ' ' . '(' . $order->created_at->format('d-m-Y') . ')' }}
                                        </option>
                                    @endisset
                                </select>
                                @isset($order)
                                    <input type="hidden" value="{{ $order->id }}" name="order_id">
                                @endisset

                            </div>

                            <!-- Chọn sản phẩm -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Chọn sản phẩm</label>
                                <select class="form-select" name="item_id" id="items">
                                    @isset($orderItem)
                                        <option
                                            value="{{ "$orderItem->id-$orderItem->product_id-$orderItem->product_variant_id" }}">
                                            {{ $orderItem->product->name }} - SKU: {{ $orderItem->productVariant->sku }} (SL:
                                            {{ $orderItem->quantity }})
                                        </option>
                                    @endisset
                                </select>
                            </div>

                            <!-- Thông báo bom -->
                            <div class="alert alert-info align-items-center gap-2 d-none">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                                <div>
                                    <strong>BOM đã tồn tại</strong><br>
                                    Hệ thống đã tự động tạo danh sách vật tư dựa trên BOM. Bạn có thể chỉnh sửa số lượng
                                    hoặc thêm vật
                                    tư
                                    mới nếu cần.
                                </div>
                            </div>

                            <!-- Cảnh báo chưa có BOM -->
                            <div class="alert alert-warning align-items-center gap-2 d-none" id="alert-no-bom">
                                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                <div>
                                    <strong>Chưa có BOM</strong><br>
                                    Sản phẩm này chưa được thiết lập định mức vật tư (BOM). Bạn cần nhập vật tư thủ công.
                                </div>
                            </div>

                            <!-- Danh sách vật tư -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold">Danh sách vật tư</h6>
                                <button type="button" id="btn-add-material"
                                    class="btn btn-outline-primary btn-sm {{ !empty($materialRequest) && $materialRequest->items ? '' : 'd-none' }}">
                                    <i class="fa-solid fa-circle-plus me-1"></i> Thêm vật tư
                                </button>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Mã vật tư</th>
                                            <th>Tên vật tư</th>
                                            <th>Đơn vị</th>
                                            <th width="15%">Số lượng</th>
                                            <th>Ghi chú</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($materialRequest->items ?? [] as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->material->code }}</td>
                                                <td>{{ $item->material->name }}</td>
                                                <td>{{ $item->material->unit }}</td>
                                                <td>
                                                    <input type="number" class="form-control text-end"
                                                        name="materials[{{ $item->material->id }}][quantity]"
                                                        value="{{ number_format($item->quantity, 0) }}" min="0">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="materials[{{ $item->material->id }}][note]"
                                                        value="{{ $item->material->note }}" max="255">
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-material">
                                                        <i class="fa-regular fa-trash-can"></i>
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

                <div class="col-3">
                    @include('admin.components.button', [
                        'redirect' => route('admin.material-requests.index'),
                    ])

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title ">Ghi chú</h4>
                        </div>
                        <div class="card-body">
                            <textarea name="note" class="form-control" id="note">{{ $materialRequest->note ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <!-- Modal chọn vật tư -->
    <div class="modal fade" id="materialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn vật tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                        <table class="table table-bordered align-middle text-center" id="material-list-table">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="checkAllMaterials"></th>
                                    <th>Mã</th>
                                    <th>Tên vật tư</th>
                                    <th>Đơn vị</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dữ liệu sẽ được đổ bằng JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary btn-sm" id="confirm-add-materials">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                window.location.href = response.data.redirect
            })

            // Khi bấm "Thêm vật tư"
            $('#btn-add-material').on('click', function() {
                // Clear danh sách cũ
                const $tbody = $('#material-list-table tbody');
                $tbody.html('<tr><td colspan="5">Đang tải...</td></tr>');

                // Gọi API lấy danh sách vật tư
                $.get('/admin/materials/select2', function(res) {
                    const existingIds = $('.table-responsive tbody input[name*="[quantity]"]').map(
                        function() {
                            const name = $(this).attr('name');
                            const match = name.match(/^materials\[(\d+)\]\[quantity\]$/);
                            return match ? match[1] : null;
                        }).get().filter(id => id !== null);

                    if (res.data.length > 0) {
                        $tbody.empty();
                        res.data.forEach(material => {

                            const isExists = existingIds.includes(material.id.toString());

                            const row = `
                                <tr>
                                    <td>
                                        ${isExists ? `<i class="text-muted">✔</i>` : `<input type="checkbox" class="material-checkbox"
                                                                                                                                                                                                                                                                        data-id="${material.id}"
                                                                                                                                                                                                                                                                        data-code="${material.code}"
                                                                                                                                                                                                                                                                        data-name="${material.name}"
                                                                                                                                                                                                                                                                        data-unit="${material.unit}">`}
                                    </td>
                                    <td>${material.code}</td>
                                    <td>${material.name}</td>
                                    <td>${material.unit}</td>
                                    <td>
                                        <input type="number" class="form-control text-end material-qty" min="1" value="1" ${isExists ? 'disabled' : ''}>
                                    </td>
                                </tr>`;
                            $tbody.append(row);
                        });
                    } else {
                        $tbody.html(
                            '<tr><td colspan="5" class="text-muted">Không có vật tư nào</td></tr>'
                        );
                    }

                    const modal = new bootstrap.Modal(document.getElementById('materialModal'));
                    modal.show();
                });
            });

            function updateSTT() {
                $('.table-responsive tbody tr').each(function(i, tr) {
                    $(tr).find('td:first').text(i + 1);
                });
            }

            // Check all
            $(document).on('change', '#checkAllMaterials', function() {
                $('.material-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Xác nhận chọn vật tư
            $('#confirm-add-materials').on('click', function() {
                const $tbodyMain = $('.table-responsive tbody');

                // Xóa dòng trống nếu có
                $tbodyMain.find('tr:has(td[colspan])').remove();

                const indexStart = $tbodyMain.find('tr').length;

                $('.material-checkbox:checked').each(function(i) {
                    const $row = $(this).closest('tr');
                    const id = $(this).data('id');
                    const code = $(this).data('code');
                    const name = $(this).data('name');
                    const unit = $(this).data('unit');
                    const qty = $row.find('.material-qty').val();

                    const newRow = `
                        <tr>
                            <td>${indexStart + i + 1}</td>
                            <td>${code}</td>
                            <td>${name}</td>
                            <td>${unit}</td>
                            <td>
                                <input type="number" class="form-control text-end" name="materials[${id}][quantity]" value="${qty}" min="1">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="materials[${id}][note]" value="">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-material">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $tbodyMain.append(newRow);
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById('materialModal'));
                modal.hide();

                updateSTT();
            });


            $('#items').select2({
                placeholder: '-- Chọn sản phẩm --',
                allowClear: true,
                width: '100%'
            });

            $('#items').on('change', function() {
                const itemId = $(this).val();

                if (itemId) {
                    $('#btn-add-material').removeClass('d-none');
                } else {
                    $('#btn-add-material').addClass('d-none');
                }

                if (!itemId) return;

                $.get('/admin/material-requests/get-boms', {
                    item_id: itemId
                }, function(res) {
                    if (res.status && res.materials.length > 0) {
                        $('.alert-info').removeClass('d-none'); // hiện thông báo BOM
                        $('#alert-no-bom').addClass('d-none'); // ẩn cảnh báo chưa có BOM

                        const $tbody = $('.table-responsive tbody');
                        $tbody.empty();

                        res.materials.forEach((material, index) => {
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${material.code}</td>
                                    <td>${material.name}</td>
                                    <td>${material.unit}</td>
                                    <td>
                                        <input type="number" class="form-control text-end" name="materials[${material.id}][quantity]" value="${material.quantity ?? ''}" min="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="materials[${material.id}][note]" value="" max="255">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-material">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $tbody.append(row);
                        });
                        updateSTT();
                    } else {
                        $('.alert-info').addClass('d-none'); // ẩn thông báo BOM
                        $('#alert-no-bom').removeClass('d-none'); // hiện cảnh báo chưa có BOM

                        $('.table-responsive tbody').html(`
                            <tr><td colspan="7" class="text-center text-muted">Không có BOM</td></tr>
                        `);
                    }

                });
            });

            $(document).on('click', '.remove-material', function() {
                $(this).closest('tr').remove();

                // Cập nhật lại số thứ tự (STT)
                updateSTT();
            });

            $('#order_id').select2({
                placeholder: '-- Chọn đơn hàng --',
                ajax: {
                    url: '/admin/material-requests/orders/select2',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(order => ({
                                id: order.id,
                                text: `${order.code} - ${order.order_name} (${order.date})`
                            })),
                            pagination: {
                                more: data.next_page_url !== null
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // mở dropdown không cần gõ
            });

            $('#order_id').on('change', function() {
                const orderId = $(this).val();

                // 👉 Clear sản phẩm đã chọn & dữ liệu cũ
                const $productSelect = $('#items');
                $productSelect.val(null).empty().trigger('change');

                // 👉 (Nếu có bảng BOM đã render, clear luôn)
                $('.table-materials tbody').empty(); // tùy theo bảng của bạn
                $('.alert-bom').addClass('d-none'); // ẩn cảnh báo bom nếu có

                if (!orderId) return;

                // 👉 Gọi API lấy sản phẩm từ order_items
                $.get(`/admin/material-requests/orders/items/${orderId}`, function(response) {
                    if (response.length === 0) {
                        $productSelect.append('<option value="">Không có sản phẩm</option>');
                    } else {
                        $productSelect.append('<option value="">-- Chọn sản phẩm --</option>');
                        response.forEach(item => {

                            const option = new Option(
                                `${item.product_name} - SKU: ${item.product_variant.sku} (SL: ${item.quantity})`,
                                `${item.id}-${item.product_id}-${item.product_variant_id}`,
                                false, false);
                            $productSelect.append(option);
                        });
                    }

                    $productSelect.trigger('change'); // cập nhật Select2
                });
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
