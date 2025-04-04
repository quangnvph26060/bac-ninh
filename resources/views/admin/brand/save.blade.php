@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'thương hiệu', 'url' => route('admin.brands.index')],
                ['name' => $brand ? $title . ' - ' . $brand->name : $title],
            ]" />
        </div>


        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if ($brand)
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
                                        <input type="text" placeholder="Tên sản phẩm" aria-required="true" required
                                            class="form-control" name="name" id="name"
                                            value="{{ optional($brand)->name }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="slug" class="form-label">Liên kết</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">{{ env('APP_URL') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3" value="{{ optional($brand)->slug }}">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href="{{ env('APP_URL') }}/{{ $brand && $brand->parent ? $brand->parent->slug . '/' : '' }}{{ optional($brand)->slug }}"
                                                id="preview-path" target="_blank"
                                                @if (!$brand) style="pointer-events: none;" @endif>
                                                {{ env('APP_URL') }}/{{ $brand && $brand->parent ? $brand->parent->slug . '/' : '' }}{{ optional($brand)->slug }}
                                            </a>
                                        </small>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea rows="3" name="description" class="form-control" id="description" placeholder="Mô tả ngắn">{!! optional($brand)->description !!}</textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="text" placeholder="website" class="form-control" name="website"
                                            id="website" value="{{ optional($brand)->website }}">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <x-seo :model="$brand" />
                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.brands.index')])

                    <x-status :status="optional($brand)->status" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Logo</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_logo"
                                style="cursor: pointer; width: 100%; height: 200px; object-fit: cover;"
                                src="{{ showImage(optional($brand)->logo) }}" alt="{{ optional($brand)->name }}"
                                onclick="document.getElementById('logo').click();">

                            <input type="file" name="logo" id="logo" class="form-control d-none" accept="image/*"
                                onchange="previewImage(event, 'show_logo')">
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#toggle-seo-fields').click(function() {
            $('.seo-edit-section').toggle(); // Ẩn/hiện các trường SEO
        });

        const inputIds = [
            {
                id: 'name',
                maxLength: 250
            },
            {
                id: 'website',
                maxLength: 150
            },
            {
                id: 'seo_title',
                maxLength: 250
            },
            {
                id: 'seo_description',
                maxLength: 400
            },
            {
                id: 'description',
                maxLength: 400
            },
            {
                id: 'slug',
                maxLength: 150
            }
        ];

        $.each(inputIds, function(index, value) {
            updateCharCount(`#${value.id}`, value.maxLength);
        });

        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.brands.index') }}"
            })
        })
    </script>
@endpush
