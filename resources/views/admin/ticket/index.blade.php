@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'ticket']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách ticket</h5>
            </div>

            <x-data-table file="ticket" />

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.tickets.index') }}"
            dataTables(api, columns, 'Ticket', {}, false, false, false)
        })
    </script>
@endpush
