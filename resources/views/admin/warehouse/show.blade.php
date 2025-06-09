@extends('admin.layout.index')


@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'Lịch sử nhập hàng', 'url' => route('admin.warehouse.index')], ['name' => 'Chi tiết']]" />
        </div>

        @if (session('message'))
            <div class="alert alert-danger">{!! session('message') !!}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">Danh sách vật tư đã nhập - {{ $warehouse->code }}</h5>

            </div>


            <div class="card-body">
                <div class="overflow-x-auto">
                    <table id="myTable" class="min-w-full border border-gray-300 text-sm text-center" style="width: 100%;">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 border">Tên sản phẩm</th>
                                <th class="px-4 py-2 border">Số lượng</th>
                                <th class="px-4 py-2 border">Đơn giá</th>
                                <th class="px-4 py-2 border">Tổng tiền</th>
                                <th class="px-4 py-2 border">Nhà cung cấp</th>
                                <th class="px-4 py-2 border">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($warehouse->details as  $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $item->name }}</td>
                                    <td class="px-4 py-2 border"><small>x </small>{{ $item->quantity }}</td>
                                    <td class="px-4 py-2 border">{{ $item->price_type == 'vnd' ? number_format($item->price ,0, ',', '.').' VND' : '$'.formatPrice($item->price) }}</td>
                                    <td class="px-4 py-2 border">{{ $item->price_type == 'vnd' ? number_format($item->price *  $item->quantity,0, ',', '.').' VND' : '$'.formatPrice($item->price) }}</td>
                                    <td class="px-4 py-2 border">{{ $item->distributor }}</td>
                                    <td class="px-4 py-2 border">{{ $item->note }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">

                    <p class="text-base font-semibold mb-1">Tổng số lượng: <span class="text-gray-800">{{ $warehouse->details->sum('quantity') }}</span></p>
                    <div class="text-base font-semibold text-gray-700 ">
                        <p class="mb-1">
                          Tổng tiền (VND):
                          <span class="text-blue-700">{{ number_format($warehouse->price_vnd, 0, ',', '.') }} VND</span>
                        </p>
                        <p class="mb-1">
                          Tổng tiền (USD):
                          <span class="text-green-700">${{ formatPrice($warehouse->price_usd) }} USD</span>
                        </p>
                      </div>




                </div>
            </div>


        </div>

    </div>

@endsection

@push('scripts')
    <style>

    </style>
@endpush

@push('scripts')

@endpush
