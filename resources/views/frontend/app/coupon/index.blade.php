@extends('frontend.app')

@section('content')
    <div class="order">
        <div class="order_container">
            <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
                <h1 class="billing__title__content">Mã giảm giá</h1>
            </div>
        </div>

        <form id="order-filter-form" class="d-flex flex-wrap gap-3 mt-4">
            <!-- Ô tìm kiếm -->
            <div class="form-group position-relative">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <div class="form-group input-icon-right">
                    <input type="search" class="form-control" name="search" placeholder="Tìm kiếm theo mã đơn hàng">
                    <i class="bi bi-search"></i>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-4">
            <div id="coupon-content">
            </div>

            <div id="loading" style="display: none; text-align: center; padding: 50px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <div id="copy-toast" class="position-fixed top-0 end-0 m-3 p-3 bg-success text-white rounded shadow d-none"
        style="z-index: 1055;">
        📋 Đã sao chép mã giảm giá!
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            $(document).on('click', '.copy-btn', function() {
                const text = $(this).data('code');
                navigator.clipboard.writeText(text).then(() => {
                    // Đổi icon tạm thời
                    $(this).removeClass('bi-clipboard').addClass('bi-clipboard-check text-success');

                    const $this = $(this);
                    setTimeout(function() {
                        $this.removeClass('bi-clipboard-check text-success').addClass(
                            'bi-clipboard text-primary');
                    }, 1500);

                    notyf.success('📋 Đã sao chép mã giảm giá!');

                    // Hiển thị toast thông báo
                    // const $toast = $('#copy-toast');
                    // $toast.removeClass('d-none').addClass('show');

                    // setTimeout(() => {
                    //     $toast.removeClass('show').addClass('d-none');
                    // }, 2000);
                });
            });

            // Gõ tìm kiếm (debounce)
            let debounceTimer;
            $(document).on('input', 'input[name="search"]', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchCoupon();
                }, 500); // 500ms chờ sau khi ngừng gõ
            });


            // Phân trang
            $(document).on('click', '.page-url-link', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    fetchCoupon(url);
                }
            });

            $(document).on('change', '.per-page-selector', function() {
                fetchCoupon();
            });
        });

        // Gửi AJAX để lọc đơn hàng
        function fetchCoupon(url = "{{ route('coupons.index') }}", page = 1) {
            const search = $('input[name="search"]').val();

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    search: search,
                    page: page
                },
                beforeSend: () => {
                    $('#coupon-content').hide();
                    $('#loading').show();
                },
                success: function(response) {
                    $('#coupon-content').html(response.html).fadeIn(200);
                    $('#loading').hide();
                },
                error: function(xhr) {
                    console.error("Lỗi khi lọc:", xhr);
                    notyf.error('Đã có lỗi xảy ra. Vui lòng thử lại sau!');
                },
                complete: () => {
                    $('#loading').hide();
                    $('#coupon-content').show();
                }
            });
        }

        fetchCoupon()
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endpush
