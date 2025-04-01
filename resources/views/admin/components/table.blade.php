
<div class="card-body">
    <div class="table-responsive">
        <table id="myTable" class="display" style="width:100%">
        </table>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('backend/assets/js/columns/' . $file . '.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/connect-datatable.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/dataTables.min.css') }}">
@endpush
