@extends('frontend.app')

@section('content')
    <div class="order">
        <div class="order_container">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
                <h1 class="billing__title__content">Lịch sử giao dịch</h1>
            </div>
        </div>

        <form id="order-filter-form" class="d-flex flex-wrap gap-3 mt-4">
            <!-- Ô tìm kiếm -->
            <div class="form-group position-relative">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <div class="form-group input-icon-right">
                    <input type="search" class="form-control" name="search" placeholder="Tìm kiếm theo mã giao dịch">
                    <i class="bi bi-search"></i>
                </div>
            </div>

            <!-- Date range -->
            <div class="form-group">
                <label class="form-label fw-bold">Ngày</label>
                <input type="text" id="date-range" name="date_range" class="form-control"
                    placeholder="Chọn khoảng ngày" />
            </div>
        </form>

        <div class="table-responsive mt-4">
            <div id="result">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            $('#date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Áp dụng',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#date-range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
                fetchWalletTransaction();
            });

            $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                fetchWalletTransaction();
            });

            // Gõ tìm kiếm (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchWalletTransaction();
                }, 500); // 500ms chờ sau khi ngừng gõ
            });


            // Phân trang
            $(document).on('click', '.page-url-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    fetchWalletTransaction(url);
                }
            });

            $(document).on('change', '.per-page-selector', function() {
                fetchWalletTransaction();
            });
        });

        // Gửi AJAX để lọc đơn hàng
        function fetchWalletTransaction(url = "{{ route('transaction.history') }}", page = 1) {
            const search = $('input[name="search"]').val();

            const urlWithParams = new URL(url, window.location.href); // URL gốc
            const searchParams = new URLSearchParams(urlWithParams
                .search); // Tạo đối tượng để truy xuất tham số query string
            const pageParam = searchParams.get('page') ||
                page; // Nếu có 'page' trong URL thì lấy, nếu không thì dùng giá trị mặc định

            $.ajax({
                url: urlWithParams.pathname,
                method: 'GET',
                data: {
                    search: search,
                    page: pageParam, // Truyền 'page' vào data của AJAX
                    date_range: $('input[name="date_range"]').val()
                },
                beforeSend: () => {
                    $('#result').hide();
                    $('#loadingOverlay').show();
                },
                success: function(response) {
                    $('#result').html(response.html).fadeIn(200);
                    $('#loadingOverlay').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi lọc:", xhr);
                    datgin.error('Đã có lỗi xảy ra. Vui lòng thử lại sau!');
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                    $('#result').show();
                }
            });
        }

        fetchWalletTransaction()
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endpush
