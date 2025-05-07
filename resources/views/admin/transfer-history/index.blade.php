@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'transfer history']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">transfer list</h5>
            </div>

            <x-data-table file="transfer-history" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.transfer.histories.index') }}"
            dataTables(api, columns, 'WalletTransaction', {}, false, false, false)
        })
    </script>
@endpush
