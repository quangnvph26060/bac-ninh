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

    <!-- Modal nhập lý do từ chối -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <form action="" id="myForm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectReasonModalLabel">Nhập lý do từ chối</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">Lý do từ chối:</label>
                            <textarea name="reason" class="form-control" id="rejectReason" rows="3" required></textarea>
                        </div>
                        <input name="id" type="hidden" id="transactionId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger btn-sm" id="submitReject">Xác nhận từ chối</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            const api = "{{ route('admin.transfer.histories.index') }}"
            dataTables(api, columns, 'WalletTransaction', {}, false, false, false)

            $(document).on('click', '.image-popup', function(e) {
                e.preventDefault();
                $.magnificPopup.open({
                    items: {
                        src: $(this).attr('href')
                    },
                    type: 'image',
                    closeOnContentClick: true,
                    mainClass: 'mfp-fade',
                    gallery: {
                        enabled: true
                    }
                });
            });

            $(document).on('click', '.btn-confirm', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                Swal.fire({
                    title: "Bạn có chắc chắn không?",
                    text: "Hành động này sẽ không thể hoàn tác!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Đồng ý, xác nhận!",
                    cancelButtonText: "Hủy",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.transfer.histories.confirm') }}",
                            type: 'POST',
                            data: {
                                id
                            },
                            beforeSend: function() {
                                $("#loadingSpinner").fadeIn();
                            },
                            success: function(response) {
                                $('#myTable').DataTable().ajax.reload();

                                Notifications(response.message, 'success');
                            },
                            error: function(xhr) {
                                if (
                                    xhr.status === 403 &&
                                    xhr.getResponseHeader("Content-Type").includes(
                                        "text/html")
                                ) {
                                    document.open();
                                    document.write(xhr.responseText);
                                    document.close();
                                    return
                                }
                                Notifications(xhr.responseJSON.message, 'danger');
                            },
                            complete: function() {
                                $("#loadingSpinner").fadeOut();
                            }
                        })
                    }
                });
            });

            $(document).on('click', '.btn-reject', function(e) {
                e.preventDefault();
                const id = $(this).data('id');

                $('#transactionId').val(id);
                $('#rejectReason').val(''); // Clear lý do cũ nếu có
                $('#rejectReasonModal').modal('show');
            });

            let url = "{{ route('admin.transfer.histories.reject') }}"

            submitForm('#myForm', function(response) {
                $('#rejectReasonModal').modal('hide');
                $('#myTable').DataTable().ajax.reload();

                Notifications(response.message, 'success');
            }, url, function(xhr) {
                if (
                    xhr.status === 403 &&
                    xhr.getResponseHeader("Content-Type").includes("text/html")
                ) {
                    document.open();
                    document.write(xhr.responseText);
                    document.close();
                    return
                }
                $('#rejectReasonModal').modal('hide');
            })

            $(document).on('click', '.btn-reason', function(e) {
                e.preventDefault();
                const reason = $(this).data('reason');

                Swal.fire({
                    title: "Lý do từ chối",
                    text: reason,
                    showClass: {
                        popup: `
                        animate__animated
                        animate__fadeInUp
                        animate__faster
                        `
                    },
                    hideClass: {
                        popup: `
                        animate__animated
                        animate__fadeOutDown
                        animate__faster
                        `
                    }
                });
            })
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />
    <style>
        .review-zoom {
            cursor: pointer;
        }
    </style>
@endpush
