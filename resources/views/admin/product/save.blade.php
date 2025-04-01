@extends('admin.layout.index')

@section('content')

    @if (empty($product))
        @php
            $product = null;
        @endphp
    @endif

    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'sản phẩm', 'url' => route('admin.product.index')], ['name' => 'sản phẩm mới']]" />
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <form action="" method="post" enctype="">
            @csrf

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="name" class="form-label required">Tên sản phẩm</label>
                                        <input type="text" placeholder="Tên sản phẩm" class="form-control" name="name"
                                            id="name" aria-required="true" required="required">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="slug" class="form-label">Liên kết</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">{{ env('APP_URL') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href=" {{ env('APP_URL') }}" target="_blank">
                                                {{ env('APP_URL') }}</a></small>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="ckeditor" id="description"></textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="content" class="form-label">Nội dung</label>
                                        <textarea name="content" class="ckeditor" id="content"></textarea>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.products.index')])

                    @include('admin.components.status')

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Là nổi bật?
                            </h4>
                        </div>
                        <div class="card-body">
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Danh mục</h4>
                        </div>
                        <div class="card-body">
                            <select id="category_id" name="category_id" class="form-control form-select">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cId => $cName)
                                    <option value="{{ $cId }}">{{ $cName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thương hiệu</h4>
                        </div>
                        <div class="card-body">
                            <select id="brand_id" name="brand_id" class="form-control form-select">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach ($brands as $bId => $bName)
                                    <option value="{{ $bId }}">{{ $bName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><label for="sku" class="form-label">SKU</label></h4>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" name="sku" id="sku">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hình ảnh nổi bật</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_image"
                                style="cursor: pointer; width: 100%; height: 200px; object-fit: cover;"
                                src="{{ showImage('') }}" alt=""
                                onclick="document.getElementById('image').click();">

                            <input type="file" name="path" id="image" class="form-control d-none" accept="image/*"
                                onchange="previewImage(event, 'show_image')">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Bộ sưu tập sản phẩm</h4>
                        </div>
                        <div class="card-body">

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tags</h4>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" name="tags" id="tags"
                                placeholder="tags sản phẩm" value="">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/tagify/tagify.js') }}"></script>
    <script>
        $(document).ready(function() {

            $("#category_id").select2({
                placeholder: "Tìm kiếm danh mục...",
                allowClear: true
            });

            $("#brand_id").select2({
                placeholder: "Tìm kiếm thương hiệu...",
                allowClear: true
            });

            const tags = document.querySelector('#tags');
            const tagsTagify = new Tagify(tags, {
                dropdown: {
                    maxItems: 10,
                    classname: "tags-look",
                    enabled: 0,
                    closeOnSelect: false
                }
            });

            tagsTagify.on('add', () => {
                adjustTagifyHeight(tagsTagify.DOM.scope);
            });

            function adjustTagifyHeight(scopeElement) {
                if (scopeElement) {
                    scopeElement.style.height = "auto"; // Reset chiều cao
                    scopeElement.style.height = scopeElement.scrollHeight + "px"; // Điều chỉnh theo nội dung
                }
            }

        });

        updateCharCount('#name', 250)
        updateCharCount('#sku', 250)
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/tagify.css') }}">
@endpush
