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
                <h5 class="text-uppercase card-title fw-bold">Danh sách vật tư đã nhập</h5>

            </div>

            <x-data-table file="warehouseDetal" />

        </div>

    </div>

@endsection

@push('scripts')
    <style>
        th:last-child, td:last-child{
            display:none;
        }
    </style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const api = "{{ route('admin.warehouse.show', $id) }}"
        dataTables(api, columns, 'WarehouseDetail')
    })
</script>
@endpush
