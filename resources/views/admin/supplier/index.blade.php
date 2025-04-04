@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'nhà cung cấp']]" />
        </div>


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách nhà cung cấp</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Thêm mới </a>
                </div>
            </div>

            <x-data-table file="supplier" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.suppliers.index') }}"
            dataTables(api, columns, 'supplier')
        })
    </script>
@endpush
