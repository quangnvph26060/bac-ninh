@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'danh mục', 'url' => route('admin.collections.index')],
                ['name' => $collection ? $title . ' - ' . $collection->name : $title],
            ]" />
        </div>

        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @if ($collection)
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
                                            value="{{ optional($collection)->name }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="slug" class="form-label">Liên kết</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">{{ env('APP_URL') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3" value="{{ optional($collection)->slug }}">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href="{{ env('APP_URL') }}/{{ $collection && $collection->parent ? $collection->parent->slug . '/' : '' }}{{ optional($collection)->slug }}"
                                                id="preview-path" target="_blank"
                                                @if (!$collection) style="pointer-events: none;" @endif>
                                                {{ env('APP_URL') }}/{{ $collection && $collection->parent ? $collection->parent->slug . '/' : '' }}{{ optional($collection)->slug }}
                                            </a>
                                        </small>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" rows="3" class="form-control" id="description" placeholder="Mô tả ngắn">{{ optional($collection)->description }}</textarea>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.categories.index')])

                    <x-status :status="optional($collection)->status" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Danh mục</h4>
                        </div>

                        <div class="card-body">
                            <select id="category_id" name="category_id[]" class="form-control form-select" multiple>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}" @selected(in_array($id, $selectedCategories ?? []))>{{ $name }}
                                    </option>
                                @endforeach
                            </select>
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

            $('#category_id').select2({
                placeholder: "Chọn danh mục",
                allowClear: true,
                // minimumInputLength: 1
            });

            updateCharCount('#name', 250)
            updateCharCount('#description', 400)

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.collections.index') }}"
            })
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
