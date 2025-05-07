@extends('admin.layout.index')
@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'hành động']]" />
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-uppercase card-title fw-bold">danh sách hành động</h5>
            </div>

            <x-data-table file="activity-log" />

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Chi tiết thay đổi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre><code id="jsonContent" class="language-json"></code></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/prism.min.js"></script>
    <script>
        $(document).ready(function() {
            let oldId = null;
            const api = "{{ route('admin.activity.log.history') }}"
            dataTables(api, columns, 'ActivityLog', {}, false, false, false, true)

            $(document).on('click', '.btn-view-changes', function() {
                let id = $(this).data('id');

                if (oldId === id) {
                    $('#viewHistoryModal').modal('show');
                    return;
                }

                oldId = id

                $.ajax({
                    url: "{{ route('admin.activity.log.history', '__id__') }}".replace('__id__',
                        id),
                    method: "GET",
                    data: {
                        id
                    },
                    beforeSend: () => {
                        $("#loadingSpinner").fadeIn();
                    },
                    success: (ressponse) => {
                        $('#jsonContent').text(JSON.stringify(ressponse.data, null, 4));
                        Prism.highlightAll();
                        $('#viewHistoryModal').modal('show');
                    },
                    error: (xhr) => {
                        console.error('Lỗi:', xhr.responseText);
                    },
                    complete: () => {
                        $("#loadingSpinner").fadeOut();
                    }
                })
            })
        })
    </script>
@endpush

@push('styles')

    <style>
        pre {
            max-height: 400px;
            overflow-y: auto;
            background-color: #2d2d2d !important;
            color: #ccc;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
@endpush
