@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'khách hàng']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách khách hàng</h5>
                <a href="/admin/customers/create" class="btn btn-primary"><i class="ti ti-circle-plus"></i> Tạo mới</a>
            </div>

            <x-data-table file="customer" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.customers.index') }}"
            dataTables(api, columns, 'Customer', {}, false)
        })
    </script>
@endpush
