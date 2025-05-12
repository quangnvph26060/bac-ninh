@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'order']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">order list</h5>
            </div>

            <x-data-table file="order" />

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="show-items" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="exampleModalLabel">Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product name</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.orders.index') }}"
            dataTables(api, columns, 'Brand')
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
                        let path =  "{{ showImage('__image__') }}".replace('__image__', item.image);

                        html += `
                            <tr>
                                <th scope="row">
                                    <p class="mb-0 fw-bold">
                                        ${item.name}
                                    </p>
                                    <small class="fw-medium">
                                        ${item.variant}
                                    </small>
                                </th>
                                <td><img src="${path}" alt="${item.name}" width="32" height="32"></td>
                                <td><small>x</small>${item.quantity}</td>
                                <td>${formatCurrency(item.price)}</td>
                                <td>${formatCurrency(item.total)}</td>
                            </tr>
                        `;
                    });

                    html += `
                        <tr>
                            <th scope="row" colspan="4" class="text-end">Sub Total :</th>
                            <td><div class="fw-bold">${formatCurrency(subTotal)}</div></td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="4" class="text-end">Shipping Charge :</th>
                            <td>${formatCurrency(shippingFee)}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="4" class="text-end">Discount :</th>
                            <td>- ${formatCurrency(discount)}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="4" class="text-end">Total :</th>
                            <td><div class="fw-bold">${formatCurrency(total)}</div></td>
                        </tr>
                    `;

                    $('#show-items tbody').html(html);

                    $('#show-items').modal('show');
                },
                error: (xhr) => {
                    console.log(xhr.responseJSON.message);
                },
                complete: function() {
                    $("#loadingSpinner").fadeOut();
                },
            })

        })
    </script>
@endpush
