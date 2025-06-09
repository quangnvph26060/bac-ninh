@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Design Library</h1>

            <button class="ant-btn ant-btn-primary" data-bs-toggle="modal" data-bs-target="#uploadArtworkModal">
                Upload Design <i class="bi bi-plus ms-2"></i>
            </button>

        </div>
    </div>

    <form id="" class="mt-4">
        <!-- Search box -->
        <div class="form-group position-relative">
            <label class="form-label fw-bold">Search</label>
            <div class="form-group input-icon-right">
                <input type="search" class="form-control" name="search" placeholder="Search by image name">
                <i class="bi bi-search"></i>
            </div>
        </div>
    </form>

    <div id="bulkActionBar" class=" d-flex d-none align-items-center gap-2 my-3">
        <i class="bi bi-x-circle-fill fs-4 cursor"></i>
        <span id="selectedCount" class="fw-semibold"></span>
        <button class="ant-btn ant-btn-default ms-2 px-3" id="handleDelete">Xóa đã chọn</button>
    </div>


    <div class="row mt-4" id="imageGrid">

    </div>

    <!-- Modal -->
    <div class="modal fade" id="uploadArtworkModal" tabindex="-1" aria-labelledby="uploadArtworkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="uploadArtworkModalLabel">Upload Artwork</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <div id="uploadArea"
                        class="border rounded-3 p-4 d-flex flex-column align-items-center justify-content-center"
                        style="border-style: dashed; background-color: #f8f9fa; cursor: pointer;">
                        <i class="bi bi-card-image display-4 text-primary mb-3"></i>
                        <p class="fw-semibold mb-1">Max: 20 Files</p>
                        <p class="mb-2">
                            <span class="text-primary fw-bold">Click or drag file to this area to upload</span>
                        </p>
                        <p class="text-muted small">
                            Support for a single or bulk upload. Strictly prohibit from uploading company data or other band
                            files
                        </p>

                    </div>

                    <input type="file" id="artworkFileInput" class="form-control mt-2" multiple style="display: none;"
                        accept="image/*">

                    {{-- File Preview Area --}}
                    <div id="filePreviewArea" class="p-3 row">
                        {{-- File previews will be added here by JavaScript --}}
                    </div>

                </div>

                <div class="modal-footer border-0 justify-content-center">
                    <button id="uploadArtworkBtn" class="ant-btn ant-btn-default">Upload Design</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedPhotoIds = new Set();
        let selectedFiles = [];

        $('#uploadArea').on('click', function() {
            $('#artworkFileInput').click();
        });

        const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
        const MAX_WIDTH = 8000;
        const MAX_HEIGHT = 8000;
        const MAX_FILES = 20;

        $('#artworkFileInput').on('change', function(e) {
            const files = Array.from(e.target.files);

            let duplicateCount = 0;
            let oversizeCount = 0;
            let dimensionCount = 0;
            let skippedCount = 0;

            const remainingSlots = MAX_FILES - selectedFiles.length;

            if (remainingSlots <= 0) {
                datgin.error(`Bạn chỉ có thể chọn tối đa ${MAX_FILES} ảnh.`);
                return $(this).val('');
            }

            const filesToProcess = files.slice(0, remainingSlots);

            filesToProcess.forEach((file) => {
                // Bỏ qua file không phải ảnh
                if (!file.type.startsWith('image/')) {
                    skippedCount++;
                    return;
                }

                // Kiểm tra dung lượng
                if (file.size > MAX_FILE_SIZE) {
                    oversizeCount++;
                    return;
                }

                // Kiểm tra trùng
                const isDuplicate = selectedFiles.some(f =>
                    f.name === file.name && f.size === file.size && f.type === file.type
                );
                if (isDuplicate) {
                    duplicateCount++;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        // Kiểm tra kích thước ảnh
                        if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
                            dimensionCount++;
                            return;
                        }

                        const index = selectedFiles.push(file) - 1;

                        const preview = $(`
                            <div class="col-md-3 mb-3 position-relative" data-index="${index}">
                                <div class="border rounded overflow-hidden position-relative">
                                    <img src="${e.target.result}" class="w-100" style="height: 90px; object-fit: contain;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `);

                        $('#filePreviewArea').append(preview);
                    };
                    img.onerror = function() {
                        skippedCount++;
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });

            // Thông báo lỗi sau khi xử lý xong
            setTimeout(() => {
                let messages = [];
                if (duplicateCount > 0) messages.push(`${duplicateCount} ảnh bị trùng.`);
                if (oversizeCount > 0) messages.push(`${oversizeCount} ảnh vượt quá 10MB.`);
                if (dimensionCount > 0) messages.push(
                    `${dimensionCount} ảnh vượt quá kích thước ${MAX_WIDTH}x${MAX_HEIGHT}.`);
                if (skippedCount > 0) messages.push(`${skippedCount} file không hợp lệ hoặc bị lỗi.`);

                if (messages.length > 0) datgin.error(messages.join('<br>'));
            }, 500);

            // Reset input để lần sau chọn lại
            $(this).val('');
        });



        // Xóa ảnh khỏi preview + danh sách
        $('#filePreviewArea').on('click', '.remove-image', function() {
            const parent = $(this).closest('[data-index]');
            const index = parseInt(parent.data('index'));
            selectedFiles[index] = null;
            parent.remove();
        });

        // Upload ảnh
        $('#uploadArtworkBtn').on('click', function() {
            const validFiles = selectedFiles.filter(file => file !== null);

            if (validFiles.length === 0) {
                datgin.error('Please select at least one image.');
                return;
            }

            const formData = new FormData();
            validFiles.forEach(file => {
                formData.append('artworks[]', file);
            });

            $.ajax({
                url: '/photos',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: function(res) {
                    if (res.success) {
                        datgin.success('Upload thành công!');
                        selectedFiles = [];
                        $('#filePreviewArea').empty();
                        $('#uploadArtworkModal').modal('hide');
                        getPhoto();
                    } else {
                        datgin.error('Upload thất bại.');
                    }
                },
                error: function(xhr) {
                    datgin.error('Đã có lỗi xảy ra.');
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
        });

        // Gõ tìm kiếm (debounce)
        let debounceTimer;
        $(document).on('input', 'input[name="search"]', function() {

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

        $(document).on('change', '.artwork-card input[type="checkbox"]', function() {
            const $checkbox = $(this);
            const $card = $checkbox.closest('.artwork-card');
            const photoId = $checkbox.val();

            if (this.checked) {
                selectedPhotoIds.add(photoId);
                $card.addClass('selected');
            } else {
                selectedPhotoIds.delete(photoId);
                $card.removeClass('selected');
            }

            const checkedCount = selectedPhotoIds.size;

            if (checkedCount > 0) {
                $('#bulkActionBar').removeClass('d-none');
                $('#selectedCount').text(`${checkedCount} sản phẩm được chọn.`);
            } else {
                $('#bulkActionBar').addClass('d-none');
                $('#selectedCount').text('');
            }
        });



        $('.bi-x-circle-fill').click(function() {
            $('#bulkActionBar').addClass('d-none');

            $('.artwork-card input[type="checkbox"]').prop('checked', false);

            $('#selectedCount').text('');

            $('.artwork-card').removeClass('selected')

            selectedPhotoIds.clear();
        });

        // Sự kiện nút "Xoá đã chọn"
        $(document).on('click', '#handleDelete', function() {
            let selectedIds = Array.from(selectedPhotoIds); // Dùng biến toàn cục đã lưu

            if (selectedIds.length === 0) return;

            if (!confirm(`Bạn có chắc chắn muốn xoá ${selectedIds.length} ảnh?`)) return;

            $.ajax({
                url: "/photos",
                type: "DELETE",
                data: {
                    ids: selectedIds,
                },
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: function(res) {
                    if (res.success) {
                        // Xoá các ID vừa xoá khỏi danh sách đã chọn
                        selectedIds.forEach(id => selectedPhotoIds.delete(id));

                        $('#bulkActionBar').addClass('d-none');
                        $('#selectedCount').text('');

                        getPhoto(); // load lại danh sách ảnh
                    } else {
                        datgin.error('Xóa thất bại.');
                    }
                },
                error: function() {
                    datgin.error('Đã xảy ra lỗi khi xóa ảnh.');
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            });
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
                    per_page
                },
                beforeSend: () => {
                    $('#loadingOverlay').show();
                },
                success: (response) => {
                    $('#imageGrid').html(response.data);

                    // Khôi phục trạng thái checked
                    selectedPhotoIds.forEach(id => {
                        $(`.artwork-card input[type="checkbox"][value="${id}"]`).prop('checked', true);
                    });
                },
                error: (xhr) => {
                    datgin.error(xhr.responseJSON.messages)
                },
                complete: () => {
                    $('#loadingOverlay').hide();
                }
            })
        }

        getPhoto()
    </script>
@endpush
