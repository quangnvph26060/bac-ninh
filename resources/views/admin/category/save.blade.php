@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'danh mục', 'url' => route('admin.categories.index')],
                ['name' => $category ? $title . ' - ' . $category->name : $title],
            ]" />
        </div>
        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if ($category)
                @method('PUT')
            @endif
            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="name" class="form-label required">Tên danh mục</label>
                                        <input type="text" placeholder="Tên sản phẩm" class="form-control" name="name"
                                            id="name" aria-required="true" required="required"
                                            value="{{ optional($category)->name }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="slug" class="form-label">Liên kết</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">{{ env('APP_URL') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3" value="{{ optional($category)->slug }}">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href=" {{ env('APP_URL') }}" id="preview-path" target="_blank"
                                                style="pointer-events: none">
                                                {{ env('APP_URL') }}/</a></small>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="ckeditor" id="description">{!! optional($category)->description !!}</textarea>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Tối ưu hóa công cụ tìm kiếm</h4>
                            <p id="toggle-seo-fields" class="text-primary mb-0">Ẩn/Hiện SEO Fields</p>
                        </div>

                        <div class="card-body">

                            <div class="seo-preview" v-pre="">
                                <p class="default-seo-description">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">
                                            Thiết lập tiêu đề và mô tả meta để trang web của bạn dễ dàng được phát hiện trên
                                            các công cụ tìm kiếm như Google
                                        </font>
                                    </font>
                                </p>

                                <div class="existed-seo-meta">

                                    <h4 class="page-title-seo text-truncate">

                                    </h4>

                                    <div class="page-url-seo">
                                        <p>-</p>
                                    </div>

                                    <div>
                                        <span style="color: #70757a;">Apr 01, 2025
                                            - </span>
                                        <span class="page-description-seo">
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="seo-edit-section">
                                <hr class="my-4">
                                <div class="mb-3 position-relative">
                                    <label for="seo_title" class="form-label">Tiêu đề SEO</label>
                                    <input type="text" placeholder="Tiêu đề SEO" class="form-control" name="seo_title"
                                        id="seo_title" value="{{ optional($category)->seo_title }}">
                                </div>

                                <div class="mb-3 position-relative">
                                    <label for="seo_description" class="form-label">Mô tả SEO</label>
                                    <textarea placeholder="Mô tả SEO" class="form-control" name="seo_description" id="seo_description" rows="3">{{ optional($category)->seo_description }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.categories.index')])

                    <x-status :status="optional($category)->status" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Danh mục cha</h4>
                        </div>
                        <div class="card-body">
                            <select id="parent_id" name="parent_id" class="form-control form-select">
                                <option value="">-- Chọn danh mục cha --</option>
                                @foreach ($categories as $item)
                                    <option @selected($item['id'] == optional($category)->parent_id) value="{{ $item['id'] }}">{{ $item['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hình ảnh nổi bật</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_image"
                                style="cursor: pointer; width: 100%; height: 200px; object-fit: cover;"
                                src="{{ showImage(optional($category)->image) }}" alt="{{ optional($category)->name }}"
                                onclick="document.getElementById('image').click();">

                            <input type="file" name="image" id="image" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_image')">
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection


@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>
    <script>
        $("#parent_id").select2({
            placeholder: "Tìm kiếm danh mục cha...",
            allowClear: true
        });

        $(document).ready(function() {
            $('#name').on('input', function() {
                let slug = generateSlug($(this).val())

                $('#slug').val(slug)

                const website = "{{ env('APP_URL') }}/"
                $('#preview-path').text(website + slug)
            })

            $('#slug').on('input', function() {

                let slug = generateSlug($(this).val())

                const website = "{{ env('APP_URL') }}/"
                $('#preview-path').text(website + slug)
            })

            $('#toggle-seo-fields').click(function() {
                $('.seo-edit-section').toggle(); // Ẩn/hiện các trường SEO
            });

            submitForm('#myForm', function(response) {
                Notifications(response.message, "success");


            })
        })
    </script>
@endpush


@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">

    <style>
        #toggle-seo-fields {
            font-size: 1rem !important;
            cursor: pointer;
            font-weight: 500;
        }

        .seo-edit-section,
        .existed-seo-meta {
            display: none;
        }
    </style>
@endpush
