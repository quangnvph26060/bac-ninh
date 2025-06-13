@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'yêu cầu xuất vật tư', 'url' => '/admin/material-requests'],
                ['name' => 'tạo yêu cầu'],
            ]" />
        </div>

        <form action="" method="post">
            <div class="row">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <!-- Chọn đơn hàng -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chọn đơn hàng</label>
                                <select class="form-select" id="order_id" style="width: 100%;"></select>
                            </div>

                            <!-- Chọn sản phẩm -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Chọn sản phẩm</label>
                                <select class="form-select" id="product_variant_id">
                                    {{-- <option selected>Sản phẩm A - SKU: SKU-A (SL: 10)</option> --}}

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

                            <!-- Danh sách vật tư -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold">Danh sách vật tư</h6>
                                <button class="btn btn-outline-primary btn-sm ">
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
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>M001</td>
                                            <td>Vải cotton</td>
                                            <td>m</td>
                                            <td><input type="number" class="form-control text-end" value="20"></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
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
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#order_id').select2({
                placeholder: '-- Chọn đơn hàng --',
                ajax: {
                    url: '/admin/material-requests/orders/select2',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            search: params.term || '', // keyword search
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
        });
    </script>
@endpush


@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
