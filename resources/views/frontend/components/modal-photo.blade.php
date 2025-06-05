<div class="modal fade" id="mockupModal" tabindex="-1" aria-labelledby="mockupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="mockupModalLabel">Chọn thiết kế</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- Khu vực tải lên -->
                <div class="border border-2 border-dashed rounded text-center p-4 mb-4 bg-light cursor">
                    <div class="mb-2"><i class="bi bi-cloud-upload fs-1 text-secondary"></i></div>
                    <div class="fw-semibold">Nhấp và chọn ảnh để tải lên</div>
                    <div class="text-muted small">
                        Hỗ trợ tải lên hàng loạt hoặc đơn lẻ. Nghiêm cấm tải lên dữ liệu công ty hoặc các tệp ban nhạc
                        khác.
                    </div>
                </div>

                <input type="file" id="image-upload-input" multiple accept="image/*" style="display: none;">

                <!-- Ô tìm kiếm -->
                <input type="text" class="form-control mb-3" name="search" placeholder="Tìm kiếm theo tên ảnh mẫu">

                <!-- Danh sách ảnh -->
                <div class="row g-3" id="imageGrid">

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="ant-btn ant-btn-primary" id="apply-image">Áp dụng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).on('click', '.border-dashed', function() {
            $('#image-upload-input').click();
        });

        $('#image-upload-input').on('change', function() {
            const files = this.files;
            if (!files.length) return;

            const formData = new FormData();
            $.each(files, function(i, file) {
                formData.append('artworks[]', file);
            });

            $.ajax({
                url: '/photos', // ← route Laravel để xử lý
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    datgin.success('Tải ảnh thành công!');
                    // Render ảnh mới ra grid:
                    getPhoto();
                },
                error: function() {
                    datgin.error('Tải ảnh thất bại!');
                }
            });
        });

        $(document).on('change', 'input[type="radio"]', function() {
            // Bỏ class selected khỏi toàn bộ
            $('.artwork-card').removeClass('selected');

            // Tìm label[for=radio_id], rồi tìm .artwork-card gần nhất
            const radioId = $(this).attr('id');
            const $card = $(`label[for="${radioId}"]`).closest('.artwork-card');

            $card.addClass('selected');
        });

        let currentProductId = null;

        $(document).on('click', '#apply-image', function() {
            const selectedRadio = $('input[name="selected_photo"]:checked');
            if (!selectedRadio.length) {
                datgin.error('Vui lòng chọn một ảnh!');
                return;
            }

            const selectedId = selectedRadio.val();
            const container = $("#" + 'design-tooltip-' + currentProductId);

            const expectedWidth = parseInt(container.data("width"));
            const expectedHeight = parseInt(container.data("height"));
            const expectedFormat = (container.data("format") || "").toLowerCase();
            const expectedPpi = container.data("dpi");
            const imageUrl = selectedRadio.closest('.artwork-card').find('img').attr('src');

            if (!currentProductId || !imageUrl) return;

            $.ajax({
                url: '/orders/validate-image',
                type: 'POST',
                data: {
                    image_id: selectedId,
                    width: expectedWidth,
                    height: expectedHeight,
                    format: expectedFormat,
                    dpi: expectedPpi,
                },
                success: function(res) {
                    let photo = res.photo;
                    const $container = $(`#image_container_${currentProductId}`);
                    const $img = $container.find(`#show_design_${currentProductId}`);
                    const $zoomLink = $container.find('.image-zoom-link');

                    if (res.valid) {
                        $container.addClass('has-image');
                        $img.attr('src', imageUrl);
                        $zoomLink.attr('href', imageUrl);
                    } else {
                        const imageDefault = "{{ showImage('') }}"
                        $img.attr('src', imageDefault);
                        $zoomLink.attr('href', imageDefault);
                        datgin.error(
                            `Thiết kế không khớp với mẫu. Thiết kế đề xuất: Width: ${expectedWidth}, Height: ${expectedHeight}, PPI: ${expectedPpi}, File format: ${expectedFormat}. Thiết kế của bạn: ${photo.width}, Height: ${photo.height}, PPI: ${photo.ppi}, File format: ${photo.format}.`
                        );
                    }
                    $('#mockupModal').modal('hide');

                },
                error: function() {
                    datgin.error('Đã xảy ra lỗi khi kiểm tra ảnh.');
                }
            });
        });

        $(document).on('click', '.remove-image', function() {
            const $wrapper = $(this).closest('.image-preview-wrapper');
            const $img = $wrapper.find('img');

            // Đặt lại ảnh mặc định (cập nhật đường dẫn đúng ảnh default của bạn)
            $img.attr('src', '{{ showImage('default.jpg') }}');

            // Bỏ class để không hiện icon khi hover nữa
            $wrapper.removeClass('has-image');
        });

        $(document).ready(function() {
            $(document).on('click', '.image-zoom-link', function(e) {
                e.preventDefault(); // Tránh chuyển tab nếu không khởi tạo kịp
                e.stopPropagation();

                // Khởi tạo nếu chưa có
                $(this).magnificPopup({
                    type: 'image',
                    closeOnContentClick: true,
                    mainClass: 'mfp-img-mobile',
                    image: {
                        verticalFit: true
                    }
                }).magnificPopup('open');
            });
        });

        $('#mockupModal').on('show.bs.modal', function(event) {
            // const button = $(event.relatedTarget);

            getPhoto();
        });

        $(document).on('click', '.open-mockup-modal', function() {
            currentProductId = $(this).data('product-id');
        });

        // Gõ tìm kiếm (debounce)
        let debounceTimer;
        $(document).on('input', 'input[name="search"]', function() {
            $('input[name="search"]').val('');
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                getPhoto();
            }, 500); // 500ms chờ sau khi ngừng gõ
        });

        $(document).on('click', '.page-url-link', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                getPhoto(url);
            }
        });

        $(document).on('change', '.per-page-selector', function() {
            getPhoto();
        });

        function getPhoto(url = "{{ route('photos.index') }}", page = 1) {
            const search = $('input[name="search"]').val();

            const urlWithParams = new URL(url, window.location.href);
            const searchParams = new URLSearchParams(urlWithParams.search);
            const pageParam = searchParams.get('page') || page;
            const per_page = $('.per-page-selector').val() || 10;

            $.ajax({
                url: urlWithParams.pathname,
                method: 'GET',
                data: {
                    search: search,
                    page: pageParam,
                    per_page,
                    type: 'radio'
                },
                beforeSend: () => {
                    $('#loading').show();
                },
                success: (response) => {
                    $('#imageGrid').html(response.data);
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.messages)
                },
                complete: () => {
                    $('#loading').hide();
                }
            })
        }
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/magnific-popup/dist/magnific-popup.css">
@endpush
