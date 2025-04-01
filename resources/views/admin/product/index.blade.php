@extends('admin.layout.index')


@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'sản phẩm']]" />
        </div>

        @if (session('message'))
            <div class="alert alert-danger">{!! session('message') !!}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách sản phẩm</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.products.download.product.template') }}" target="_blank"
                        class="btn btn-outline-light btn-sm text-dark border fs-6"><i class="ti ti-file-download me-2"></i>
                        Download Template </a>
                    <a href="{{ route('admin.products.export') }}" target="_blank"
                        class="btn btn-outline-light btn-sm text-dark border fs-6"><i class="ti ti-file-export me-2"></i>
                        Export</a>
                    <button class="btn btn-outline-light btn-sm text-dark border fs-6" data-bs-toggle="modal"
                        data-bs-target="#importProduct"><i class="ti ti-file-import me-2"></i>
                        Import</button>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Thêm mới </a>
                </div>
            </div>

            <x-data-table file="product" />

        </div>

    </div>

    <div class="modal fade" id="importProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.products.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Nhập sản phẩm từ Excel</h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.products.index') }}"
            dataTables(api, columns, 'Product', {
                category_id: {
                    title: 'Chọn danh mục',
                    data: @json($categories)
                },
                brand_id: {
                    title: 'Chọn thương hiệu',
                    data: @json($brands)
                },
                company_id: {
                    title: 'Chọn nhà cung cấp',
                    data: @json($companies)
                },
            })

        })
    </script>
@endpush
