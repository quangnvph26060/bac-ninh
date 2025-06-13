@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'yêu cầu xuất vật tư']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách yêu cầu xuất vật tư</h5>
                <div class="card-tool ">
                    <a href="{{ route('admin.material-requests.create') }}" class="btn btn-primary btn-sm fs-6"><i
                            class="ti ti-circle-plus"></i> Tạo yêu cầu xuất vật tư </a>
                </div>
            </div>

            <x-data-table file="material-request" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "{{ route('admin.material-requests.index') }}"
            dataTables(api, columns, 'MaterialRequest', {}, false, false, false, true)
        })
    </script>
@endpush
