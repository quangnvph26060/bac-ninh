@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'vai trò']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách vai trò</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Thêm mới </a>
                </div>
            </div>

            <x-data-table file="role" />

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.roles.index') }}"
            dataTables(api, columns, 'Role')
        })
    </script>
@endpush



@push('styles')
    <style>
        #dt-length-0 {
            margin-right: 10px
        }
    </style>
@endpush
