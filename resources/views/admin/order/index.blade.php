@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'đơn hàng']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách đơn hàng</h5>
            </div>

            <x-data-table file="order" />

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="show-items" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Chi tiết đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th>Ảnh mẫu</th>
                                    <th>Ảnh thiết kế</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.orders.index') }}"
            dataTables(api, columns, 'Brand', {}, false, true, false, true)
        })

        let oldCode = null;

        $(document).on('click', '.btn-operation-show', function() {
            let code = $(this).data('code')

            if (oldCode == code) {
                $('#show-items').modal('show');
                return
            }
            oldCode = code;

            $.ajax({
                url: "{{ route('admin.orders.get.item.by.code', '__code__') }}".replace('__code__', code),
                method: "POST",
                data: {
                    code
                },
                beforeSend: function() {
                    $("#loadingSpinner").fadeIn();
                },
                success: (response) => {
                    const data = response.data;

                    const items = data.items;
                    const subTotal = parseFloat(data.sub_total);
                    const shippingFee = parseFloat(data.shipping_fee);
                    const discount = parseFloat(data.discount);
                    const total = parseFloat(data.total);

                    let html = '';

                    items.forEach(item => {

                        html += `
                            <tr>
                                <th scope="row">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="img-thumbnail" style="max-width: inherit; width: 60px;" src="${item.image}" alt="${item.name}">
                                        <div>
                                            <h5 class="mb-0">${item.name}</h5>
                                            <small>${item.variant}</small>
                                        </div>
                                    </div>
                                </th>
                                <td style="text-align: center; width: 5%;"><img class="img-thumbnail" style="max-width: inherit; width: 100px;" src="${item.model_image}" alt="${item.name}"></td>
                                <td style="text-align: center; width: 5%;"><img class="img-thumbnail" style="max-width: inherit; width: 100px;" src="${item.design_image}" alt="${item.name}"></td>
                                <td style="text-align: center; width: 5%;"><small>x</small>${item.quantity}</td>
                                <td style="text-align: center; width: 5%;">${formatCurrency(item.price)}</td>
                                <td style="text-align: center; width: 5%;">${formatCurrency(item.total)}</td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr>
                            <th scope="row" colspan="5" class="text-end">Tổng phụ :</th>
                            <td class="text-center"><div class="fw-bold">${formatCurrency(subTotal)}</div></td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="5" class="text-end">Phí vận chuyển :</th>
                            <td class="text-center">${formatCurrency(shippingFee)}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="5" class="text-end">Giảm giá :</th>
                            <td class="text-center">- ${formatCurrency(discount)}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="5" class="text-end">Tổng tiền :</th>
                            <td class="text-center"><div class="fw-bold">${formatCurrency(total)}</div></td>
                        </tr>
                    `;

                    $('#show-items tbody').html(html);

                    $('#show-items').modal('show');
                },
                error: (xhr) => {
                    if (
                        xhr.status === 403 &&
                        xhr.getResponseHeader("Content-Type").includes("text/html")
                    ) {
                        document.open();
                        document.write(xhr.responseText);
                        document.close();
                        return
                    }
                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                },
            })

        })
    </script>
@endpush
