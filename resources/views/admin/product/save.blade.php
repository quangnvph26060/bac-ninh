@extends('admin.layout.index')

@section('content')
    @if (empty($product))
        @php
            $product = null;
        @endphp
    @endif

    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'sản phẩm', 'url' => route('admin.products.index')],
                ['name' => $product ? $title . ' - ' . $product->name : $title],
            ]" />
        </div>

        <form action="" method="post" enctype="multipart/form-data" id="myForm">
            @csrf

            @if ($product)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="name" class="form-label required">Tên sản phẩm</label>
                                        <input type="text" placeholder="Tên sản phẩm" class="form-control" name="name"
                                            id="name" aria-required="true" required="required"
                                            value="{{ optional($product)->name }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="slug" class="form-label">Liên kết</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon3">{{ env('APP_URL') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3" value="{{ optional($product)->slug }}">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href=" {{ env('APP_URL') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}"
                                                target="_blank">
                                                {{ env('APP_URL') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}</a></small>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea rows="3" name="description" class="form-control" id="description" placeholder="Mô tả ngắn">{!! optional($product)->description !!}</textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="content" class="form-label">Nội dung</label>
                                        <textarea name="content" class="ckeditor" id="content">{!! optional($product)->description !!}</textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="images" class="form-label">Album sản phẩm</label>
                                        <div class="input-images pb-3"></div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <h4 class="card-title me-2">
                                Dữ liệu sản phẩm
                            </h4>
                            —
                            <select name="type" class="form-control form-select w-25 ms-2" id="type">
                                <optgroup label="Loại sản phẩm">
                                    <option value="simple" @selected(optional($product)->type == 'simple')>Sản phẩm đơn giản</option>
                                    <option value="variant" @selected(optional($product)->type == 'variant')>Sản phẩm có biến thể</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation" id="tabs-overview">
                                    <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview"
                                        role="tab" aria-controls="overview" aria-selected="true">Tổng quan</a>
                                </li>
                                <li class="nav-item" role="presentation" id="tabs-inventory">
                                    <a class="nav-link" id="inventory-tab" data-bs-toggle="tab" href="#inventory"
                                        role="tab" aria-controls="inventory" aria-selected="false">Kiểm kê kho hàng</a>
                                </li>
                                <li class="nav-item" role="presentation" style="display: none" id="tabs-attribute">
                                    <a class="nav-link" id="attribute-tab" data-bs-toggle="tab" href="#attribute"
                                        role="tab" aria-controls="attribute" aria-selected="false">thuộc tính</a>
                                </li>
                                <li class="nav-item" role="presentation" style="display: none" id="tabs-variant">
                                    <a class="nav-link" id="variant-tab" data-bs-toggle="tab" href="#variant"
                                        role="tab" aria-controls="variant" aria-selected="false">Biến thể</a>
                                </li>
                                <li class="nav-item" role="presentation" id="tabs-cross-selling">
                                    <a class="nav-link" id="cross-selling-tab" data-bs-toggle="tab"
                                        href="#cross-selling" role="tab" aria-controls="cross-selling"
                                        aria-selected="false">Sản phẩm bán
                                        chéo</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                    aria-labelledby="overview-tab">
                                    <div class="row mt-3">
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="sale_price" class="form-label required">Giá bán</label>
                                            <input type="text" placeholder="Giá bán" class="form-control"
                                                name="sale_price" id="sale_price"
                                                value="{{ $product ? formatNumber(optional($product)->sale_price) : '' }}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="stock" class="form-label">Tồn kho</label>
                                            <input type="text" placeholder="Tồn kho" class="form-control"
                                                name="stock" id="stock" value="{{ optional($product)->stock }}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="product_unit" class="form-label">Đơn vị</label>
                                            <input type="text" placeholder="Đơn vị" class="form-control"
                                                name="product_unit" id="product_unit"
                                                value="{{ optional($product)->product_unit }}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="discount_price" class="form-label">Giá ưu đãi
                                                <span class="form-label-description">
                                                    <a href="javascript:void(0)" class="turn-on-schedule">Lên lịch</a>
                                                    <a class="turn-off-schedule" style="display: none"
                                                        href="javascript:void(0)">
                                                        Ẩn
                                                    </a>
                                                </span>
                                            </label>

                                            <input type="text" placeholder="Giá bán" class="form-control"
                                                name="discount_price" id="discount_price"
                                                value="{{ $product ? formatNumber(optional($product)->discount_price) : '' }}">
                                        </div>
                                        <div class="col-md-6 scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="discount_start">
                                                    Từ ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="discount_start" id="discount_start" placeholder="d-m-Y"
                                                    value="{{ $product && $product->discount_start ? $product->discount_start->format('d-m-Y') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="discount_end">
                                                    Đến ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="discount_end" id="discount_end" placeholder="d-m-Y"
                                                    value="{{ $product && $product->discount_end ? $product->discount_end->format('d-m-Y') : '' }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="inventory" role="tabpanel"
                                    aria-labelledby="inventory-tab">
                                    <div class="row">
                                        <div class="mb-3 position-relative col-md-12 mt-3">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input type="text" class="form-control" name="sku" id="sku"
                                                value="{{ optional($product)->sku }}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-12 mt-3">
                                            <label for="stock_status" class="form-label d-inline">Trạng thái kho
                                                hàng:</label>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="stock_status"
                                                    id="in_stock" value="in_stock" checked>
                                                <label class="form-check-label" for="in_stock">Còn hàng</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="stock_status"
                                                    id="out_of_stock" value="opout_of_stocktion1">
                                                <label class="form-check-label" for="out_of_stock">Hết hàng</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="stock_status"
                                                    id="waiting_for_goods" value="waiting_for_goods">
                                                <label class="form-check-label" for="waiting_for_goods">Chờ hàng</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="attribute" role="tabpanel"
                                    aria-labelledby="attribute-tab">
                                    <label for="attribute_ids" class="form-label mt-3">Thuộc tính</label>
                                    <select id="attribute-select" class="form-control w-100" multiple="multiple">
                                        @foreach ($attributes as $attributeId => $attributeName)
                                            <option value="{{ $attributeId }}" @selected(in_array($attributeId, $selectedAttributes ?? []))>
                                                {{ $attributeName }}</option>
                                        @endforeach
                                    </select>

                                    <div class="accordion my-4" id="selected-attribute">

                                        @foreach ($attributesWithValues ?? [] as $aId => $attributeValues)
                                            <div class="accordion-item" id="accordion-{{ $aId }}">
                                                <h2 class="accordion-header" id="heading-{{ $aId }}">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-{{ $aId }}"
                                                        aria-expanded="true"
                                                        aria-controls="collapse-{{ $aId }}">
                                                        <span
                                                            class="fw-bold">{{ strtoupper($attributeValues['attribute']) }}</span>
                                                    </button>
                                                </h2>
                                                <div id="collapse-{{ $aId }}"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="heading-{{ $aId }}"
                                                    data-bs-parent="#accordionExample">
                                                    <div class="accordion-body position-relative">
                                                        <label class="form-label">Giá trị</label>
                                                        <a href="javascript:void(0)"
                                                            class="select-all position-absolute">Chọn
                                                            tất cả</a>
                                                        <select class="form-select select2 form-control"
                                                            name="attributes[{{ $aId }}][]"
                                                            id="select-{{ $aId }}" multiple>
                                                            @foreach ($attributeValues['values'] as $vId => $vValue)
                                                                <option value="{{ $vId }}-{{ $vValue }}"
                                                                    @selected(in_array($vId, $attributeValues['selected'] ?? []))>
                                                                    {{ $vValue }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="btn btn-light text-dark btn-sm" disabled
                                        id="save-attributes">Lưu thuộc
                                        tính</button>
                                </div>

                                <div class="tab-pane fade" id="variant" role="tabpanel" aria-labelledby="variant-tab">
                                    <button type="button" class="btn btn-primary btn-sm my-3" id="generateVariants">Tạo
                                        biến
                                        thể tự
                                        động</button>

                                    <div class="accordion" id="variantAccordion">
                                        @foreach ($variants ?? [] as $variantItem)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button type="button"
                                                        class="accordion-button collapsed position-relative"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#v{{ $variantItem['id'] }}">
                                                        <span>{{ $variantItem['variant_name'] }}</span>
                                                        <span class="ms-2 delete-variant text-danger position-absolute"
                                                            data-index="{{ $variantItem['id'] }}">Xóa</span>
                                                    </button>
                                                </h2>
                                                <div id="v{{ $variantItem['id'] }}" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label for="variants-{{ $variantItem['id'] }}-sku"
                                                                    class="form-label required">Mã sản phẩm</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['id'] }}-sku"
                                                                    name="variants[{{ $variantItem['id'] }}][sku]"
                                                                    aria-required="true" required="required"
                                                                    value="{{ $variantItem['sku'] }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label for="variants-{{ $variantItem['id'] }}-sale-price"
                                                                    class="form-label required">Giá</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['id'] }}-sale-price"
                                                                    name="variants[{{ $variantItem['id'] }}][sale_price]"
                                                                    aria-required="true" required="required"
                                                                    value="{{ $variantItem['sale_price'] }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['id'] }}-product-unit"
                                                                    class="form-label">Đơn vị</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['id'] }}-product-unit"
                                                                    name="variants[{{ $variantItem['id'] }}][product_unit]"
                                                                    value="{{ $variantItem['product_unit'] }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['id'] }}-discount-price"
                                                                    class="form-label">Giá ưu đãi
                                                                    <span class="form-label-description">
                                                                        <a href="javascript:void(0)"
                                                                            class="variant-turn-on-schedule">Lên lịch</a>
                                                                        <a class="variant-turn-off-schedule"
                                                                            style="display: none"
                                                                            href="javascript:void(0)">
                                                                            Hủy
                                                                        </a>
                                                                    </span>
                                                                </label>

                                                                <input type="text" class="form-control"
                                                                    name="variants[{{ $variantItem['id'] }}][discount_price]"
                                                                    id="variants-{{ $variantItem['id'] }}-discount-price"
                                                                    value="{{ $variantItem['discount_price'] }}">
                                                            </div>
                                                            <div class="col-md-6 variant-scheduled-time"
                                                                style="display: none;">
                                                                <div class="mb-3 position-relative">
                                                                    <label class="form-label"
                                                                        for="variants-{{ $variantItem['id'] }}-discount-start">
                                                                        Từ ngày
                                                                    </label>
                                                                    <input class="form-control form-date-time"
                                                                        type="text"
                                                                        name="variants[{{ $variantItem['id'] }}][discount_start]"
                                                                        id="variants-{{ $variantItem['id'] }}-discount-start"
                                                                        value="{{ $variantItem['discount_start'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 variant-scheduled-time"
                                                                style="display: none;">
                                                                <div class="mb-3 position-relative">
                                                                    <label class="form-label"
                                                                        for="variants-{{ $variantItem['id'] }}-discount-end">
                                                                        Đến ngày
                                                                    </label>
                                                                    <input class="form-control form-date-time"
                                                                        type="text"
                                                                        name="variants[{{ $variantItem['id'] }}][discount_end]"
                                                                        id="variants-{{ $variantItem['id'] }}-discount-end"
                                                                        value="{{ $variantItem['discount_end'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-12">
                                                                <label
                                                                    for="variants-{{ $variantItem['id'] }}-stock-status"
                                                                    class="form-label">Trạng thái kho hàng</label>

                                                                <select
                                                                    name="variants[{{ $variantItem['id'] }}][stock_status]"
                                                                    id="variants-{{ $variantItem['id'] }}-stock-status"
                                                                    class="form-control form-select">
                                                                    <option value="in_stock" @selected($variantItem['stock_status'] == 'in_stock')>
                                                                        Còn hàng</option>
                                                                    <option value="out_of_stock"
                                                                        @selected($variantItem['stock_status'] == 'out_of_stock')>Hết hàng</option>
                                                                    <option value="waiting_for_goods"
                                                                        @selected($variantItem['stock_status'] == 'waiting_for_goods')>Chờ hàng</option>
                                                                </select>
                                                            </div>
                                                            <div class="position-relative col-md-12">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        value="1"
                                                                        id="variants-{{ $variantItem['id'] }}-status"
                                                                        @checked($variantItem['status'] == 2)>
                                                                    <label class="form-check-label mb-0"
                                                                        for="variants-{{ $variantItem['id'] }}-status">
                                                                        Ẩn trên giao diện
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="cross-selling" role="tabpanel"
                                    aria-labelledby="cross-selling-tab">

                                    <input name="cross_sell" type="hidden" value="">

                                    <div class="mb-3 mt-3 position-relative">
                                        <input class="form-control" type="text" name="search_input" id="searchInput"
                                            placeholder="Tìm kiếm sản phẩm">
                                        <div class="card position-absolute z-1 shadow w-100 active" style="display:none"
                                            id="popup-dropdown">
                                            <div class="card-body p-0">
                                                <div class="list-search-data">
                                                    <div class="list-group list-group-flush overflow-y-auto overflow-x-hidden"
                                                        style="max-height: 25rem;">

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-footer pb-0 d-flex justify-content-end">
                                                <nav>
                                                    <ul class="pagination">
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="list-group list-group-flush list-group-hoverable list-selected-products"
                                        style="{{ !empty($productCrossSell) && $productCrossSell->isNotEmpty() ? 'display: block' : 'display: none' }}">
                                        <label class="form-label">Sản phẩm đã chọn</label>

                                        @if (!empty($productCrossSell) && $productCrossSell->isNotEmpty())
                                            @foreach ($productCrossSell as $productItem)
                                                <div class="list-group-item" data-id="{{ $productItem->id }}">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="avatar"
                                                                style="background-image: url('{{ showImage($productItem->image) }}')"></span>
                                                        </div>
                                                        <div class="col text-truncate">
                                                            <a href="javascript:void(0);"
                                                                class="text-body d-block text-truncate fs-6">{{ $productItem->name }}</a>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="javascript:void(0)"
                                                                data-bb-toggle="product-delete-item" data-bb-target="1"
                                                                class="text-decoration-none list-group-item-actions btn-trigger-remove-selected-product"
                                                                title="Xóa bỏ">
                                                                <svg class="icon text-secondary svg-icon-ti-ti-x"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                    </path>
                                                                    <path d="M18 6l-12 12"></path>
                                                                    <path d="M6 6l12 12"></path>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
                                <p class="default-seo-description" style="{{ $product ? 'display: none;' : '' }}">
                                    Thiết lập tiêu đề và mô tả meta để trang web của bạn dễ dàng được phát hiện trên
                                    các công cụ tìm kiếm như Google
                                </p>
                                <div class="existed-seo-meta" style="{{ $product ? 'display: block;' : '' }}">

                                    <h4 class="page-title-seo text-truncate">
                                        {{ optional($product)->seo_title ?? optional($product)->name }}
                                    </h4>

                                    <div class="page-url-seo">
                                        <p>
                                            {{ env('APP_URL') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}
                                        </p>
                                    </div>

                                    <div>
                                        <span
                                            style="color: #70757a;">{{ $product ? optional($product)->created_at->format('M d, Y') : '' }}
                                            - </span>
                                        <span class="page-description-seo">
                                            {{ \Str::words(optional($product)->seo_description ?? optional($product)->description, 45, '...') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="seo-edit-section">
                                <hr class="my-4">
                                <div class="row">
                                    <div class="mb-3 position-relative col-lg-12">
                                        <label for="seo_title" class="form-label">Tiêu đề SEO</label>
                                        <input type="text" placeholder="Tiêu đề SEO" class="form-control"
                                            name="seo_title" id="seo_title" value="{{ optional($product)->seo_title }}">
                                    </div>
                                    <div class="mb-3 position-relative col-lg-12">
                                        <label for="seo_description" class="form-label">Mô tả SEO</label>
                                        <textarea placeholder="Mô tả SEO" class="form-control" name="seo_description" id="seo_description" rows="3">{{ optional($product)->seo_description }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.products.index')])

                    <x-status :status="optional($product)->status" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                Là nổi bật?
                            </h4>
                        </div>
                        <div class="card-body">
                            <label class="switch">
                                <input type="checkbox" value="1" @checked(optional($product)->is_featured)>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <label for="category_id" class="form-label required">
                                    Danh mục
                                </label>
                            </h4>
                        </div>
                        <div class="card-body">
                            <select id="category_id" name="category_id" class="form-control form-select">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cId => $cName)
                                    <option value="{{ $cId }}" @selected($cId == optional($product)->category_id)>{{ $cName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <label for="category_id" class="form-label required">
                                    Thương hiệu
                                </label>
                            </h4>
                        </div>
                        <div class="card-body">
                            <select id="brand_id" name="brand_id" class="form-control form-select">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach ($brands as $bId => $bName)
                                    <option value="{{ $bId }}" @selected($bId == optional($product)->brand_id)>{{ $bName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Nhãn sản phẩm</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-check p-0 ms-0">
                                <input class="form-check-input" name="" type="checkbox" value=""
                                    id="">
                                <label class="form-check-label" for="">
                                    HOT
                                </label>
                            </div>
                            <div class="form-check p-0 ms-0">
                                <input class="form-check-input" name="" type="checkbox" value=""
                                    id="">
                                <label class="form-check-label" for="">
                                    NEW
                                </label>
                            </div>
                            <div class="form-check p-0 ms-0">
                                <input class="form-check-input" name="" type="checkbox" value=""
                                    id="">
                                <label class="form-check-label" for="">
                                    SALE
                                </label>
                            </div>
                        </div>
                    </div> --}}

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Hiển thị trang chủ?</h4>
                        </div>

                        <div class="card-body">
                            <select name="is_show_home" class="form-control form-select" id="is_show_home">
                                <option value="1" @selected(optional($product)->is_show_home == 1)>Có</option>
                                <option value="0" @selected(optional($product)->is_show_home == 0)>Không</option>
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
                                src="{{ showImage(optional($product)->image) }}" alt=""
                                onclick="document.getElementById('image').click();">

                            <input type="file" name="image" id="image" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_image')">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tags</h4>
                        </div>
                        <div class="card-body">
                            <input type="text" class="form-control" name="tags" id="tags"
                                placeholder="tags sản phẩm" value="{{ $product ? implode(', ', $product->tags) : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/image-uploader/image-uploader.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/tagify/tagify.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>
    <script>
        let ids = [];

        let debounceTimer;

        $('#searchInput').on('focus', function() {
            $('#popup-dropdown').show();

            if (!$('.selectable-item').length > 0) fetchSearchResults('');
        });

        $('#searchInput').on('input', function() {
            let query = $(this).val();

            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function() {
                fetchSearchResults(query);
            }, 500);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchInput').length && !$(e.target).closest('#popup-dropdown')
                .length) {
                $('#popup-dropdown').hide();
            }
        });


        function loadPage(page) {
            let query = $('#searchInput').val();
            fetchSearchResults(query, page);
        };

        function fetchSearchResults(query, page = 1) {
            $.ajax({
                url: '{{ route('admin.products.search.products') }}', // Địa chỉ API của bạn
                method: 'GET',
                data: {
                    query: query,
                    page: page,
                    per_page: 10 // Giới hạn 10 sản phẩm mỗi trang
                },
                success: function(response) {
                    displaySearchResults(response.data, response.pagination);
                },
                error: function() {
                    console.log("Lỗi khi gọi API tìm kiếm.");
                }
            });
        }

        function displaySearchResults(products, pagination) {
            let resultList = $('.list-search-data .list-group');
            resultList.empty(); // Xóa nội dung cũ

            // Hiển thị sản phẩm tìm thấy
            products.forEach(function(product) {
                let path = "{{ env('APP_URL') }}/storage/" + product.image

                resultList.append(`
                <a href="javascript:void(0);" class="list-group-item list-group-item-action selectable-item"
                    data-id="${product.id}" data-name="${product.name}" data-image="${path}" data-price="${product.price}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url('${path}')"></span>
                        </div>
                        <div class="col text-truncate">
                            <h4 class="text-body d-block mb-0">${product.name}</h4>
                        </div>
                    </div>
                </a>
            `);
            });

            // Hiển thị phân trang
            let paginationList = $('.pagination');
            paginationList.empty();

            if (pagination.prev_page_url) {
                paginationList.append(
                    `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage(${pagination.current_page - 1})">Trước</a></li>`
                );
            } else {
                paginationList.append(
                    '<li class="page-item disabled"><span class="page-link">Trước</span></li>');
            }

            if (pagination.next_page_url) {
                paginationList.append(
                    `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage(${pagination.current_page + 1})">Kế tiếp</a></li>`
                );
            } else {
                paginationList.append(
                    '<li class="page-item disabled"><span class="page-link">Kế tiếp</span></li>');
            }
        }

        $(document).on('click', '.selectable-item', function() {
            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let productImage = $(this).data('image');
            let productPrice = $(this).data('price');

            if (ids.includes(productId)) {
                // Nếu sản phẩm đã tồn tại, xóa nó khỏi danh sách
                $('.list-selected-products .list-group-item[data-id="' + productId + '"]').remove();

                // Loại bỏ productId khỏi mảng ids
                ids = ids.filter(id => id !== productId);

            } else {
                ids.push(productId);
                // Cập nhật danh sách sản phẩm đã chọn
                $('.list-selected-products').append(`
                    <div class="list-group-item" data-id="${productId}">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar" style="background-image: url('${productImage}')"></span>
                            </div>
                            <div class="col text-truncate">
                                <a href="javascript:void(0);" class="text-body d-block text-truncate fs-6">${productName}</a>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0)" data-bb-toggle="product-delete-item" data-bb-target="1" class="text-decoration-none list-group-item-actions btn-trigger-remove-selected-product" title="Xóa bỏ">
                                    <svg class="icon text-secondary svg-icon-ti-ti-x" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M18 6l-12 12"></path>
                                        <path d="M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                `);
            }

            checkListSelected()

            $('input[name="cross_sell"]').val(ids)

            // Ẩn dropdown khi đã chọn sản phẩm
            // $('#popup-dropdown').hide();
        });

        $(document).on('click', '.btn-trigger-remove-selected-product', function() {
            let item = $(this).closest('.list-group-item');
            item.remove();

            checkListSelected()
        });

        function checkListSelected() {
            if ($('.list-selected-products .list-group-item').length > 0) {
                $('.list-selected-products').css('display', 'block');
            } else {
                $('.list-selected-products').css('display', 'none');
            }
        }

        $('#toggle-seo-fields').click(function() {
            $('.seo-edit-section').toggle(); // Ẩn/hiện các trường SEO
        });

        const inputIds = [{
                id: 'name',
                maxLength: 250
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
                id: 'sku',
                maxLength: 50
            },
            {
                id: 'slug',
                maxLength: 150
            }
        ];

        $.each(inputIds, function(index, value) {
            updateCharCount(`#${value.id}`, value.maxLength);
        });

        let on = $('.turn-on-schedule')
        let off = $('.turn-off-schedule')

        on.click(function() {
            off.show();
            on.hide();
            $('.scheduled-time').show();
        })

        off.click(function() {
            on.show();
            off.hide();
            $('.scheduled-time').hide();
        })

        $('#sku').on('input', function() {
            let value = $(this).val();

            // Chuyển thành chữ IN HOA và loại bỏ dấu tiếng Việt
            value = value.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            $(this).val(value);
        });

        $('#type').on('change', function() {

            if ($(this).val() == 'variant') {
                $('li#tabs-attribute').show();
                $('li#tabs-variant').show();
                $('li#tabs-overview').hide();

                // $('.nav-tabs .nav-link').removeClass('active');
                // $('.tab-content .tab-pane').removeClass('active show');

                $('#inventory-tab').addClass('active');
                $('#inventory').addClass('active show');

                $('#overview-tab').removeClass('active');
                $('#overview').removeClass('active show');
            } else {
                $('li#tabs-attribute').hide();
                $('li#tabs-variant').hide();
                $('li#tabs-overview').show();

                // Xóa active và show của tất cả các tab
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-content .tab-pane').removeClass('active show');

                // Kích hoạt tab đầu tiên
                $('#overview-tab').addClass('active');
                $('#overview').addClass('active show');
            }
        });


        $(document).ready(function() {

            $('.accordion-body select.form-select.select2').select2({
                width: '100%',
                placeholder: "Chọn giá trị",
                allowClear: true
            });

            $('#type').trigger('change');

            let preloaded = [];

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'images',
                preloadedInputName: 'old',
                maxSize: 2 * 1024 * 1024,
                maxFiles: 15,
            });

            $("#category_id").select2({
                placeholder: "Tìm kiếm danh mục...",
                allowClear: true
            });

            $("#brand_id").select2({
                placeholder: "Tìm kiếm thương hiệu...",
                allowClear: true
            });

            $('#attribute-select').select2({
                placeholder: "Chọn thuộc tính",
                allowClear: true,
                width: '100%'
            });

            var attributeNames = @json($attributes);

            $('#attribute-select').on('change', function() {
                let selectedAttributes = $(this).val() || []; // Lấy danh sách ID thuộc tính đã chọn
                let accordionContainer = $('#selected-attribute');

                // Lấy tất cả các ID accordion hiện tại
                let existingAccordions = accordionContainer.children('.accordion-item').map(function() {
                    return $(this).attr('id').replace('accordion-', '');
                }).get();

                // Xóa những accordion không còn được chọn
                existingAccordions.forEach(attributeId => {
                    if (!selectedAttributes.includes(attributeId)) {
                        $('#accordion-' + attributeId).remove();
                        checkIfAnyValueSelected
                            ();
                    }
                });

                // Thêm những accordion chưa có
                selectedAttributes.forEach(attributeId => {
                    if (!$('#accordion-' + attributeId)
                        .length) { // Nếu chưa có accordion thì mới thêm
                        let attributeName = attributeNames[attributeId] || "Không xác định";

                        // Gọi API để lấy danh sách giá trị của thuộc tính
                        $.ajax({
                            url: '{{ route('admin.products.selected.attributes', '__id__') }}'
                                .replace('__id__', attributeId),
                            method: 'GET',
                            success: function(response) {

                                let valuesArray = Object.entries(response).map(([id,
                                    name
                                ]) => ({
                                    id: id,
                                    name: name
                                }));

                                let valuesOptions = valuesArray.map(value =>
                                    `<option value="${value.id}-${value.name}">${value.name}</option>`
                                ).join('');

                                let accordionItem = `
                                    <div class="accordion-item" id="accordion-${attributeId}">
                                        <h2 class="accordion-header" id="heading-${attributeId}">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-${attributeId}" aria-expanded="true"
                                                aria-controls="collapse-${attributeId}">
                                                <span class="fw-bold">${attributeName.toUpperCase()}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-${attributeId}" class="accordion-collapse collapse show"
                                            aria-labelledby="heading-${attributeId}" data-bs-parent="#accordionExample">
                                            <div class="accordion-body position-relative">
                                                <label class="form-label">Giá trị</label>
                                                <a href="javascript:void(0)" class="select-all position-absolute">Chọn tất cả</a>
                                                <select class="form-select select2 form-control" name="attributes[${attributeId}][]" id="select-${attributeId}" multiple>
                                                    ${valuesOptions}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                // Thêm vào accordion container
                                accordionContainer.append(accordionItem);

                                $('#select-' + attributeId).select2({
                                    width: '100%',
                                    placeholder: "Chọn giá trị",
                                    allowClear: true
                                });

                                $('#accordion-' + attributeId).find('.select-all').on(
                                    'click',
                                    function() {
                                        let selectElement = $('#select-' +
                                            attributeId);
                                        selectElement.find('option').prop(
                                            'selected', true);
                                        selectElement.trigger(
                                            'change'
                                        ); // Cập nhật select2 sau khi chọn tất cả
                                    });

                                $('#select-' + attributeId).on('change', function() {
                                    checkIfAnyValueSelected
                                        (); // Kiểm tra trạng thái của nút "Lưu thuộc tính"
                                });
                            },

                            error: function() {
                                console.log('Lỗi khi lấy dữ liệu thuộc tính: ' +
                                    attributeId);
                            }
                        });
                    }
                });
            });

            function checkIfAnyValueSelected() {
                let anySelected = false;
                $('#selected-attribute .accordion-item').each(function() {
                    let selectElement = $(this).find('select');
                    if (selectElement.val() && selectElement.val().length > 0) {
                        anySelected = true;
                    }
                });

                // Nếu có giá trị được chọn, bỏ disable nút "Lưu thuộc tính"
                if (anySelected) {
                    $('#save-attributes').prop('disabled', false).removeClass('btn-light text-dark').addClass(
                        'btn-primary');
                } else {
                    $('#save-attributes').prop('disabled', true).removeClass('btn-primary').addClass(
                        'btn-light text-dark');
                }
            }

            $('#save-attributes').on('click', function() {
                let selectedAttributesData = {}; // Để lưu dữ liệu thuộc tính đã chọn

                // Lặp qua tất cả các accordion và lấy các giá trị đã chọn
                $('#selected-attribute .accordion-item').each(function() {
                    let attributeId = $(this).attr('id').replace('accordion-', '');
                    let selectedValues = $(this).find('select').val(); // Các giá trị đã chọn

                    if (selectedValues && selectedValues.length > 0) {
                        selectedAttributesData[attributeId] = selectedValues;
                    }
                });

                // Lưu vào localStorage
                if (Object.keys(selectedAttributesData).length > 0) {
                    localStorage.setItem('selectedAttributes', JSON.stringify(selectedAttributesData));
                    alert('Thuộc tính đã được lưu vào localStorage!');
                } else {
                    alert('Vui lòng chọn ít nhất một thuộc tính và giá trị!');
                }
            });

            $('#generateVariants').on('click', function() {
                let storedAttributes = JSON.parse(localStorage.getItem('selectedAttributes') || '{}');
                let keys = Object.keys(storedAttributes);
                let values = Object.values(storedAttributes);

                if (keys.length === 0) {
                    alert("Vui lòng chọn thuộc tính và lưu trước khi tạo biến thể!");
                    return;
                }

                let result = [];

                // Đệ quy tạo tất cả các biến thể
                function combine(prefix, prefixIds, index) {
                    if (index === keys.length) {
                        result.push({
                            name: prefix.join(' - '),
                            id: prefixIds.join('-')
                        });
                        return;
                    }
                    for (let val of values[index]) {
                        let [id, name] = val.split('-');
                        combine([...prefix, name], [...prefixIds, id], index + 1);
                    }
                }
                combine([], [], 0);

                // Hiển thị biến thể trong Bootstrap Accordion
                let accordionHtml = '';
                result.forEach((variant, index) => {
                    accordionHtml += `
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button type="button" class="accordion-button collapsed position-relative" data-bs-toggle="collapse" data-bs-target="#v${index}">
                                    <span>${variant.name}</span>
                                    <span class="ms-2 delete-variant text-danger position-absolute" data-index="${index}">Xóa</span>
                                </button>
                            </h2>
                            <div id="v${index}" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-sku" class="form-label required">Mã sản phẩm</label>
                                            <input type="text" class="form-control" id="variants-${index}-sku" name="variants[${variant.id}][sku]"
                                                aria-required="true" required="required">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-sale-price" class="form-label required">Giá</label>
                                            <input type="text" class="form-control" id="variants-${index}-sale-price" name="variants[${variant.id}][sale_price]"
                                                aria-required="true" required="required">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-product-unit" class="form-label">Đơn vị</label>
                                            <input type="text" class="form-control" id="variants-${index}-product-unit" name="variants[${variant.id}][product_unit]">
                                        </div>
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="variants-${index}-discount-price" class="form-label">Giá ưu đãi
                                                <span class="form-label-description">
                                                    <a href="javascript:void(0)" class="variant-turn-on-schedule">Lên lịch</a>
                                                    <a class="variant-turn-off-schedule" style="display: none"
                                                        href="javascript:void(0)">
                                                        Hủy
                                                    </a>
                                                </span>
                                            </label>

                                            <input type="text" class="form-control"
                                                name="variants[${variant.id}][discount_price]" id="variants-${index}-discount-price">
                                        </div>
                                        <div class="col-md-6 variant-scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="variants-${index}-discount-start">
                                                    Từ ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="variants[${variant.id}][discount_start]" id="variants-${index}-discount-start">
                                            </div>
                                        </div>
                                        <div class="col-md-6 variant-scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="variants-${index}-discount-end">
                                                    Đến ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="variants[${variant.id}][discount_end]" id="variants-${index}-discount-end">
                                            </div>
                                        </div>
                                        <div class="mb-3 position-relative col-md-12">
                                            <label for="variants-${index}-stock-status" class="form-label">Trạng thái kho hàng</label>

                                            <select name="variants[${variant.id}][stock_status]" id="variants-${index}-stock-status" class="form-control form-select">
                                                <option value="in_stock" selected>Còn hàng</option>
                                                <option value="out_of_stock">Hết hàng</option>
                                                <option value="waiting_for_goods">Chờ hàng</option>
                                            </select>
                                        </div>
                                        <div class="position-relative col-md-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="variants-${index}-status">
                                                <label class="form-check-label mb-0" for="variants-${index}-status">
                                                    Ẩn trên giao diện
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#variantAccordion').html(accordionHtml);
            });

            $(document).on('click', '.delete-variant', function() {
                let index = $(this).data('index'); // Lấy index của biến thể
                $('#v' + index).closest('.accordion-item').remove(); // Xóa biến thể khỏi giao diện
            });

            $(document).on('click', '.variant-turn-on-schedule', function() {
                let parent = $(this).closest('.accordion-body'); // Tìm phần tử cha
                parent.find('.variant-scheduled-time').show(); // Hiển thị input ngày
                $(this).hide(); // Ẩn nút "Lên lịch"
                parent.find('.variant-turn-off-schedule').show(); // Hiển thị nút "Hủy"

                parent.find('.form-date-time').flatpickr({
                    enableTime: false,
                    dateFormat: "d-m-Y",
                    locale: "vn"
                });
            });

            $(document).on('click', '.variant-turn-off-schedule', function() {
                let parent = $(this).closest('.accordion-body'); // Tìm phần tử cha
                parent.find('.variant-scheduled-time').hide(); // Ẩn input ngày
                parent.find('.variant-turn-on-schedule').show(); // Hiển thị lại nút "Lên lịch"
                $(this).hide(); // Ẩn nút "Hủy"

                // Xóa dữ liệu trong input khi hủy
                // parent.find('input[name*="discount_start"]').val('');
                // parent.find('input[name*="discount_end"]').val('');
            });


            $(window).on('beforeunload', function() {
                localStorage.removeItem('selectedAttributes');
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

            submitForm('#myForm', function(response) {
                console.log(response);

            })

            flatpickr(".form-date-time", {
                enableTime: false, // Ẩn giờ
                dateFormat: "d-m-Y", // Định dạng YYYY-MM-DD
                locale: "vn" // Dùng tiếng Việt
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/image-uploader.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/tagify.css') }}">

    <style>
        .list-group-item {
            display: block !important;
        }

        .delete-variant {
            right: 35px;
        }
    </style>
@endpush
