@extends('admin.layout.index')

@section('content')
    <div class="page-inner">

        <div class="row">
            <div class="col-xl-6  mb-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <div class="flex-fill mb-2">
                                    Tổng doanh số
                                </div>
                                <h3 class="fw-semibold mb-2">
                                    {{ number_format($total, 0, ',', '.') }} VND

                                </h3>

                            </div>
                            <div class="avatar avatar-md avatar-rounded  svg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" style="background: rgb(25 135 84)">
                                    <rect width="256" height="256" fill="none"></rect>
                                    <polyline points="48 208 48 136 96 136" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></polyline>
                                    <line x1="224" y1="208" x2="32" y2="208" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></line>
                                    <polyline points="96 208 96 88 152 88" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></polyline>
                                    <polyline points="152 208 152 40 208 40 208 208" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6  mb-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <div class="flex-fill mb-2">
                                    Tổng số đơn hàng
                                </div>
                                <h3 class="fw-semibold mb-2">
                                    {{ number_format($order_list, 0, ',', '.') }}

                                </h3>

                            </div>
                            <div class="avatar avatar-md avatar-rounded svg-white"> <svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 256 256" style="background: rgb(13 110 253)">
                                    <rect width="256" height="256" fill="none"></rect>
                                    <path
                                        d="M209.67,208H46.33a8.06,8.06,0,0,1-8-7.07l-14.25-120a8,8,0,0,1,8-8.93H223.92a8,8,0,0,1,8,8.93l-14.25,120A8.06,8.06,0,0,1,209.67,208Z"
                                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></path>
                                    <path d="M88,104V64a40,40,0,0,1,80,0v40" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path>
                                </svg> </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-6  mb-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <div class="flex-fill mb-2">
                                    Tổng số lượt truy cập
                                </div>
                                <h3 class="fw-semibold mb-2">
                                    2,21,635
                                </h3>

                            </div>
                            <div class="avatar avatar-md avatar-rounded bg-secondary svg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none"></rect>
                                    <circle cx="128" cy="120" r="40" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></circle>
                                    <path d="M63.8,199.37a72,72,0,0,1,128.4,0" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path>
                                    <line x1="176" y1="56" x2="224" y2="56" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></line>
                                    <line x1="200" y1="32" x2="200" y2="80" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></line>
                                    <path d="M222.67,112A95.92,95.92,0,1,1,144,33.33" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="col-xl-6  mb-3">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <div class="flex-fill mb-2">
                                    Tổng sản phẩm
                                </div>
                                <h3 class="fw-semibold mb-2">
                                    {{ number_format($product_list) }}
                                </h3>

                            </div>
                            <div class="avatar avatar-md avatar-rounded svg-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"
                                    style="background: rgb(13 202 240)">
                                    <rect width="256" height="256" fill="none"></rect>
                                    <polyline points="32.7 76.92 128 129.08 223.3 76.92" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></polyline>
                                    <path
                                        d="M131.84,25l88,48.18a8,8,0,0,1,4.16,7v95.64a8,8,0,0,1-4.16,7l-88,48.18a8,8,0,0,1-7.68,0l-88-48.18a8,8,0,0,1-4.16-7V80.18a8,8,0,0,1,4.16-7l88-48.18A8,8,0,0,1,131.84,25Z"
                                        fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="16"></path>
                                    <line x1="128" y1="129.09" x2="128" y2="232" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16"></line>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class=" mb-3">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card custom-card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="card-title">
                                Đơn hàng gần đây
                            </div> <a href="javascript:void(0);" class="fs-13 text-muted">
                                Xem tất cả<i class="ti ti-arrow-narrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled project-recent-transactions-list">
                                @forelse ($order_processing as $item)
                                    <li class="mb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="lh-1">
                                                <span
                                                    class="avatar avatar-ms avatar-rounded bg-primary-transparent fw-semibold fs-14"
                                                    style="color: #000000">
                                                    {{ strtoupper(substr($item->order_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="flex-fill"> <span class="d-block fw-semibold">
                                                    {{ $item->order_name }}

                                                </span> <span class="d-block fs-13 text-muted">
                                                    {{ $item->phone_number }}

                                                </span> </div>
                                            <div class="text-end"> <span class="h6 mb-0 fw-semibold text-danger">

                                                    {{ number_format($item->total) }} VND
                                                </span> <span class="d-block text-muted fs-13">
                                                    @php

                                                        $dateString = $item->created_at;
                                                        $date = new DateTime($dateString);

                                                        // Định dạng lại ngày
                                                        $formattedDate =
                                                            'Ngày ' .
                                                            $date->format('d') .
                                                            ' tháng ' .
                                                            $date->format('n') .
                                                            ' năm ' .
                                                            $date->format('Y');
                                                    @endphp
                                                    {{ $formattedDate }}

                                                </span> </div>
                                        </div>
                                    </li>

                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card custom-card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="card-title">
                                Sản phẩm bán chạy nhất
                            </div> <a href="javascript:void(0);" class="fs-13 text-muted">
                                Xem tất cả<i class="ti ti-arrow-narrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled ecommerce-top-selling-list">
                                @forelse ($bestSellingProducts as $item)
                                    <li class="mb-2">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <div class="me-3 lh-1">
                                                <span class="avatar avatar-md">
                                                    <img src="{{ asset('storage'. $item['product']->image) }}" alt="">
                                                </span>
                                            </div>
                                            <div class=" flex-fill"> <span class="d-block mb-0 fw-semibold">
                                                    {{ $item['product']->name }}

                                                </span> <span class="text-muted fs-13">

                                                    {{ $item['product']->category->name }}
                                                </span> </div>
                                            <div class="text-end"> <span class="mb-0 d-block h6 fw-semibold">

                                                    {{ number_format($item['total_price'], 0, ',', '.') }} VND

                                                </span> <span class="mb-0 d-block text-muted fs-13">
                                                    {{ number_format($item['sold_quantity'], 0, ',', '.') }} đã bán

                                                </span> </div>
                                        </div>
                                    </li>
                                @empty
                                @endforelse

                            </ul>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">
                        Tóm tắt sản phẩm
                    </div> <a href="{{ route('admin.products.index') }};" class="fs-13 text-muted">
                        Xem tất cả<i class="ti ti-arrow-narrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Tồn kho</th>
                                    <th>Đơn vị</th>
                                    <th>Thương hiệu</th>
                                    <th>Danh mục</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $item)
                                    <tr>
                                        <td class="brder-bottom-0">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2"> <img
                                                        src="{{ asset('storage/' . $item->image) }}" alt="avatar"
                                                        class=""> </div> <a href=""
                                                    title="{{ $item->name }}">{{ Str::limit($item->name, 30) }}</a>
                                            </div>
                                        </td>
                                        <td>{{ $item->stock }}</td>

                                        <td>{{ $item->product_unit }}</td>
                                        <td>{{ $item->brand->name }}</td>
                                        <td>{{ $item->category->name }}</td>
                                    </tr>
                                @empty
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 0.25rem;
        }

        .avatar {
            position: relative;
            height: 2.625rem;
            width: 2.625rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            color: #fff;
            font-weight: 500;
        }
    </style>
@endpush
