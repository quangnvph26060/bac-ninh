@extends('admin.layout.index')


@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'vật tư']]" />
        </div>

        @if (session('message'))
            <div class="alert alert-danger">{!! session('message') !!}</div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách vật tư</h5>
                <div class="card-tool">
                    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Thêm mới </a>
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
