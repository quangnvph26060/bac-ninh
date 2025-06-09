@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'nguyên vật liệu']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách nguyên vật liệu</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Nhập vật liệu </a>
                </div>
            </div>

            <x-data-table file="material" />

        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.materials.index') }}"
            dataTables(api, columns, 'Material')
        })
    </script>
@endpush
