@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'nguyên vật liệu']]" />
        </div>
        {{-- Thành công --}}
        @if (session('success'))
            <div class="alert alert-success">
                <strong>Thành công:</strong> {{ session('success') }}
            </div>
        @endif

        {{-- Lỗi tổng --}}
        @if (session('import_errors'))
            <div class="alert alert-danger">
                <strong>Lỗi khi nhập liệu:</strong>
                <ul class="mb-0">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách nguyên vật liệu</h5>
                <div class="card-tool d-flex gap-2">

                    {{-- Nút Tải File Mẫu --}}
                    <a href="{{ route('admin.materials.template') }}" class="btn btn-secondary btn-sm fs-6">
                        <i class="ti ti-download"></i> Tải file mẫu
                    </a>


                    {{-- Nút Xuất Excel --}}
                    <a href="/admin/materials/export" class="btn btn-success btn-sm fs-6">
                        <i class="ti ti-download"></i> Xuất Excel
                    </a>

                    {{-- Nút Nhập Excel --}}
                    <form action="{{ route('admin.materials.import') }}" method="POST" enctype="multipart/form-data"
                        id="importExcelForm">
                        @csrf
                        <label class="btn btn-danger text-light btn-sm fs-6 mb-0" for="importExcelInput">
                            <i class="ti ti-upload"></i> Nhập Excel
                        </label>
                        <input type="file" name="file" id="importExcelInput" accept=".xlsx, .xls"
                            style="display: none" onchange="document.getElementById('importExcelForm').submit();">
                    </form>

                    {{-- Nút Thêm mới --}}
                    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary btn-sm fs-6">
                        <i class="ti ti-circle-plus"></i> Thêm mới vật liệu
                    </a>
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
            dataTables(api, columns, 'Material', {
                unit: {
                    title: 'Lọc đơn vị',
                    data: @json($units)
                }
            })
        })
    </script>
@endpush
