<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Tối ưu hóa công cụ tìm kiếm</h4>
        <p id="toggle-seo-fields" class="text-primary mb-0">Ẩn/Hiện SEO Fields</p>
    </div>

    <div class="card-body">
        <div class="seo-preview" v-pre="">
            <p class="default-seo-description" style="{{ $model ? 'display: none;' : '' }}">
                Thiết lập tiêu đề và mô tả meta để trang web của bạn dễ dàng được phát hiện trên
                các công cụ tìm kiếm như Google
            </p>
            <div class="existed-seo-meta" style="{{ $model ? 'display: block;' : '' }}">

                <h4 class="page-title-seo text-truncate">
                    {{ optional($model)->seo_title ?? optional($model)->name }}
                </h4>

                <div class="page-url-seo">
                    <p>
                        {{ env('APP_URL') . '/' . ($model && $model->category ? $model->category->slug . '/' : '') . optional($model)->slug }}
                    </p>
                </div>

                <div>
                    <span style="color: #70757a;">{{ $model ? optional($model)->created_at->format('M d, Y') : '' }}
                        - </span>
                    <span class="page-description-seo">
                        {{ \Str::words(optional($model)->seo_description ?? optional($model)->description, 45, '...') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="seo-edit-section">
            <hr class="my-4">
            <div class="row">
                <div class="mb-3 position-relative col-lg-12">
                    <label for="seo_title" class="form-label">Tiêu đề SEO</label>
                    <input type="text" placeholder="Tiêu đề SEO" class="form-control" name="seo_title" id="seo_title"
                        value="{{ optional($model)->seo_title }}">
                </div>
                <div class="mb-3 position-relative col-lg-12">
                    <label for="seo_description" class="form-label">Mô tả SEO</label>
                    <textarea placeholder="Mô tả SEO" class="form-control" name="seo_description" id="seo_description" rows="3">{{ optional($model)->seo_description }}</textarea>
                </div>
            </div>
        </div>

    </div>
</div>
