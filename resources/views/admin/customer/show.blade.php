@extends('admin.layout.index')
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                @php
                    $items = [['name' => "thông tin khách hàng - $user->name"]];
                @endphp
                <x-breadcrumb :items="$items" />
            </div>

            <!-- Thông tin khách hàng -->
            <div class="card customer-card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="customer-info">
                        <img src="{{ showImage($user->img_url) }}" alt="Avatar" class="avatar" />
                        <div class="customer-details">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Họ và tên:</strong> {{ $user->name }}
                                    </p>
                                    <p>
                                        <strong>Email:</strong>
                                        {{ $user->email }}
                                    </p>
                                    <p>
                                        <strong>Số điện thoại:</strong> {!! $user->phone ?? '<small class="text-muted">Chưa cập nhật...</small>' !!}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>Địa chỉ:</strong> {!! $user->address ?? '<small class="text-muted">Chưa cập nhật...</small>' !!}
                                    </p>
                                    <p>
                                        <strong>Ngày đăng ký:</strong>
                                        {{ $user->created_at->format('d-m-Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Số tiền trong ví và tổng số đơn hàng -->
            <div class="row my-4">
                <div class="col-md-4">
                    <div class="customer-card card stat-card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Số dư ví</h5>
                            <h3 class="mb-0">${{ formatPrice($user->wallet?->balance) }}</h3>
                            <small><i class="fas fa-wallet me-2"></i>Cập nhật mới
                                nhất</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card customer-card stat-card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Tổng đơn hàng</h5>
                            <h3 class="mb-0">{{ $user->orders_count }}</h3>
                            <small><i class="fas fa-shopping-cart me-2"></i>Đơn
                                hàng</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card customer-card stat-card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Tổng chi tiêu</h5>
                            <h3 class="mb-0">${{ formatPrice($total) }}</h3>
                            <small><i class="fas fa-chart-line me-2"></i>Tất cả
                                thời gian</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách đơn hàng -->
            <div class="card customer-card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user->orders as $order)
                                    <tr>
                                        <td>{{ $order->code }}</td>
                                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                        <td>${{ formatPrice($order->total) }}</td>
                                        <td>
                                            {{-- <span class="badge bg-success">{{ $order->status }}</span> --}}
                                            @switch($order->status)
                                                @case(1)
                                                    <span class="badge bg-success">Đang xử lý</span>
                                                @break

                                                @case(2)
                                                    <span class="badge bg-warning">Đang giao hàng</span>
                                                @break

                                                @case(3)
                                                    <span class="badge bg-danger">Đã giao hàng</span>
                                                @break

                                                @default
                                                    <span class="badge bg-danger">Đã hủy</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-custom">Xem</a>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có đơn hàng</td>
                                        </tr>
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
            .customer-card {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                border-radius: 15px;
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                transition: transform 0.3s ease;
            }

            .customer-card:hover {
                transform: translateY(-5px);
            }

            .wallet-balance {
                font-size: 1.8rem;
                font-weight: bold;
                color: #00ff88;
                text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
            }

            .order-count {
                font-size: 1.5rem;
                color: #ffeb3b;
                text-shadow: 0 0 10px rgba(255, 235, 59, 0.5);
            }

            .table {
                background: rgba(255, 255, 255, 0.05);
                color: #ffffff;
            }

            .table th {
                background: linear-gradient(90deg, #6b7280, #4b5563);
                color: #ffffff;
            }

            .table td {
                border-color: rgba(255, 255, 255, 0.1);
            }

            .table tr:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .badge {
                padding: 8px 12px;
                border-radius: 20px;
            }

            .btn-outline-custom {
                border-color: #00ff88;
                color: #00ff88;
                transition: all 0.3s ease;
            }

            .btn-outline-custom:hover {
                background: #00ff88;
                color: #1e3c72;
            }

            .container {
                padding: 40px 20px;
            }

            .avatar {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid #00ff88;
                box-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
                margin-right: 20px;
            }

            .customer-info {
                display: flex;
                align-items: center;
            }

            .customer-details {
                flex-grow: 1;
            }
        </style>
    @endpush
