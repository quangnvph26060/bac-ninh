<div class="sidebar no-print" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="{{ asset('images/aicrm1.png') }}" alt="navbar brand" class="navbar-brand" height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                {{-- Tổng quan --}}
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-chart-bar"></i>
                        <p>Tổng quan</p>
                    </a>
                </li>

                {{-- Quản lý sản phẩm --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#product">
                        <i class="fas fa-box"></i>
                        <p>Sản phẩm</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="product">
                        <ul class="nav nav-collapse">
                            <li><a href="{{ route('admin.products.index') }}"><span class="sub-item">Sản phẩm</span></a>
                            </li>
                            <li><a href="{{ route('admin.categories.index') }}"><span class="sub-item">Danh
                                        mục</span></a></li>
                            <li><a href="{{ route('admin.brands.index') }}"><span class="sub-item">Thương
                                        hiệu</span></a></li>
                            <li><a href="{{ route('admin.attributes.index') }}"><span class="sub-item">Thuộc
                                        tính</span></a></li>
                            <li><a href="{{ route('admin.collections.index') }}"><span class="sub-item">Bộ sưu
                                        tập</span></a></li>
                        </ul>
                    </div>
                </li>

                {{-- Kho hàng --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#warehouse">
                        <i class="fas fa-warehouse"></i>
                        <p>Kho hàng</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="warehouse">
                        <ul class="nav nav-collapse">
                            <li><a href="{{ route('admin.storage.index') }}"><span class="sub-item">Kho</span></a></li>
                            <li><a href="{{ route('admin.importproduct.index') }}"><span class="sub-item">Nhập
                                        hàng</span></a></li>
                            <li><a href="#"><span class="sub-item">Xuất kho</span></a></li>
                            <li><a href="#"><span class="sub-item">Chuyển kho</span></a></li>
                            <li><a href="{{ route('admin.check.index') }}"><span class="sub-item">Phiếu kiểm
                                        kho</span></a></li>
                            <li><a href="{{ route('admin.inventory.index') }}"><span class="sub-item">Tồn
                                        kho</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#marketing">
                        <i class="fas fa-tags"></i>
                        <p>Marketing</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="marketing">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('admin.coupons.index') }}">
                                    <span class="sub-item">Mã giảm giá</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="sub-item">Chiến dịch</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                {{-- Nhà cung cấp --}}
                <li class="nav-item">
                    <a href="{{ route('admin.suppliers.index') }}">
                        <i class="fas fa-building"></i>
                        <p>Nhà cung cấp</p>
                    </a>
                </li>

                {{-- Quản lý thu chi --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#thuChi">
                        <i class="fas fa-coins"></i>
                        <p>Thu - Chi</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="thuChi">
                        <ul class="nav nav-collapse">
                            <li><a href="{{ route('admin.quanlythuchi.receipts.index') }}"><span class="sub-item">Phiếu
                                        thu</span></a></li>
                            <li><a href="{{ route('admin.quanlythuchi.expense.index') }}"><span class="sub-item">Phiếu
                                        chi</span></a></li>
                        </ul>
                    </div>
                </li>

                {{-- Hỗ trợ --}}
                <li class="nav-item">
                    <a href="{{ route('admin.support.lienhe') }}">
                        <i class="fas fa-headset"></i>
                        <p>Hỗ trợ</p>
                    </a>
                </li>

                {{-- Báo cáo --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#baocao">
                        <i class="fas fa-chart-line"></i>
                        <p>Báo cáo</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="baocao">
                        <ul class="nav nav-collapse">
                            <li><a href="{{ route('admin.order.index') }}"><span class="sub-item">Đơn hàng</span></a>
                            </li>
                            <li><a href="{{ route('admin.client.index') }}"><span class="sub-item">Khách
                                        hàng</span></a></li>
                            <li><a href="#"><span class="sub-item">Báo giá</span></a></li>
                            <li><a href="#"><span class="sub-item">Hợp đồng</span></a></li>
                            <li><a href="{{ route('admin.profit.index') }}"><span class="sub-item">Lợi
                                        nhuận</span></a>
                            </li>

                            {{-- Thống kê ngày --}}
                            <li>
                                <a data-bs-toggle="collapse" href="#thongke">
                                    <span class="sub-item">Thống kê ngày</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="thongke">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('admin.report.orders.getDailyOrder') }}"><span
                                                    class="sub-item">Bán hàng</span></a></li>
                                        <li><a href="{{ route('admin.report.imports.getDailyImport') }}"><span
                                                    class="sub-item">Nhập hàng</span></a></li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Công nợ --}}
                            <li>
                                <a data-bs-toggle="collapse" href="#congno">
                                    <span class="sub-item">Công nợ</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="congno">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('admin.debts.client') }}"><span class="sub-item">Khách
                                                    hàng</span></a></li>
                                        <li><a href="{{ route('admin.debts.supplier') }}"><span class="sub-item">Nhà
                                                    cung cấp</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Cấu hình --}}
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#config">
                        <i class="fas fa-cogs"></i>
                        <p>Cấu hình</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="config">
                        <ul class="nav nav-collapse">
                            {{-- Nhân viên --}}
                            <li>
                                <a data-bs-toggle="collapse" href="#nhanvien">
                                    <i class="fas fa-user-tie"></i>
                                    <p>Nhân viên</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="nhanvien">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('admin.staff.store') }}"><span class="sub-item">Danh
                                                    sách</span></a></li>
                                        <li><a href="{{ route('admin.staff.addForm') }}"><span class="sub-item">Thêm
                                                    mới</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>

        </div>
    </div>
</div>
