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
                                            <span class="input-group-text" id="basic-addon3">{{ config('app.url') }}</span>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                aria-describedby="basic-addon3" value="{{ optional($product)->slug }}">
                                        </div>
                                        <small class="form-hint mt-n2 text-truncate">Xem trước:
                                            <a href=" {{ config('app.url') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}"
                                                target="_blank">
                                                {{ config('app.url') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}</a></small>
                                    </div>

                                    <div class="mb-3 col-lg-3">
                                        <label for="design_width" class="form-label required">Chiều rộng ảnh thiết kế
                                            (px)</label>
                                        <input type="text" class="form-control" name="design_width" id="design_width"
                                            value="{{ optional($product)->design_width }}">
                                    </div>

                                    <div class="mb-3 col-lg-3">
                                        <label for="design_height" class="form-label required">Chiều cao ảnh thiết kế
                                            (px)</label>
                                        <input type="text" class="form-control" name="design_height" id="design_height"
                                            value="{{ optional($product)->design_height }}">
                                    </div>

                                    <div class="mb-3 col-lg-3">
                                        <label for="design_ppi" class="form-label required">Độ phân giải
                                            (PPI)</label>
                                        <input type="text" class="form-control" name="design_ppi" id="design_ppi"
                                            value="{{ optional($product)->design_ppi }}">
                                    </div>

                                    <div class="mb-3 col-lg-3">
                                        <label for="design_format" class="form-label required">Định dạng ảnh thiết
                                            kế</label>
                                        <input list="design_format_list" type="text" class="form-control"
                                            name="design_format" id="design_format"
                                            value="{{ optional($product)->design_format }}">
                                        <datalist id="design_format_list">
                                            <option value="jpg">
                                            <option value="png">
                                            <option value="gif">
                                            <option value="jpeg">
                                            <option value="webp">
                                        </datalist>
                                    </div>


                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả ngắn</label>
                                        <textarea rows="3" name="description" class="form-control" id="description" placeholder="Mô tả ngắn">{!! optional($product)->description !!}</textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="content" class="form-label">Nội dung</label>
                                        <textarea name="content" class="ckeditor" id="content">{!! optional($product)->content !!}</textarea>
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
                                        role="tab" aria-controls="inventory" aria-selected="false">Kiểm kê kho
                                        hàng</a>
                                </li>
                                <li class="nav-item" role="presentation" style="display: none" id="tabs-attribute">
                                    <a class="nav-link" id="attribute-tab" data-bs-toggle="tab" href="#attribute"
                                        role="tab" aria-controls="attribute" aria-selected="false">Thuộc tính</a>
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
                                <li class="nav-item" role="presentation" id="tabs-shipping">
                                    <a class="nav-link" id="shipping-tab" data-bs-toggle="tab" href="#shipping"
                                        role="tab" aria-controls="shipping" aria-selected="false">Vận chuyển</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                    aria-labelledby="overview-tab">
                                    <div class="row mt-3">
                                        <div class="mb-3 position-relative col-md-3">
                                            <label for="sale_price" class="form-label required">Giá bán</label>
                                            <input type="text" placeholder="Giá bán"
                                                class="form-control usd-price-format" name="sale_price" id="sale_price"
                                                value="{{ $product ? optional($product)->sale_price : '' }}">
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

                                            <input type="text" placeholder="Giá ưu đãi"
                                                class="form-control usd-price-format" name="discount_price"
                                                id="discount_price"
                                                value="{{ $product ? optional($product)->discount_price : '' }}">
                                        </div>
                                        <div class="col-md-6 scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="discount_start">
                                                    Từ ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="discount_start" id="discount_start" placeholder="d-m-Y H:i"
                                                    value="{{ $product && $product->discount_start ? $product->discount_start->format('d-m-Y') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 scheduled-time" style="display: none;">
                                            <div class="mb-3 position-relative">
                                                <label class="form-label" for="discount_end">
                                                    Đến ngày
                                                </label>
                                                <input class="form-control form-date-time" type="text"
                                                    name="discount_end" id="discount_end" placeholder="d-m-Y H:i"
                                                    value="{{ $product && $product->discount_end ? $product->discount_end->format('d-m-Y') : '' }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="inventory" role="tabpanel"
                                    aria-labelledby="inventory-tab">
                                    <div class="row">
                                        <div class="mb-3 position-relative col-md-6 mt-3">
                                            <label for="sku" class="form-label">SKU</label>
                                            <input type="text" class="form-control" name="sku" id="sku"
                                                value="{{ optional($product)->sku }}">
                                        </div>
                                        <div class="mb-3 position-relative col-md-6 mt-3">
                                            <label for="stock_status" class="form-label">Trạng thái kho
                                                hàng:</label>

                                            <select name="stock_status" id="stock_status"
                                                class="form-control form-select">
                                                <option value="in_stock">Còn hàng</option>
                                                <option value="out_of_stock">Hết hàng hàng</option>
                                                <option value="waiting_for_goods">Chờ hàng</option>
                                            </select>
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
                                        id="save-attributes">Lưu</button>
                                </div>

                                <div class="tab-pane fade" id="variant" role="tabpanel" aria-labelledby="variant-tab">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Lọc theo giá trị thuộc tính</label>
                                                    <select class="form-select" id="filter-attribute-values" multiple>
                                                        <option value="all">Tất cả</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">Giá</label>
                                                    <input type="text" class="form-control usd-price-format" id="filter-price">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">Số lượng</label>
                                                    <input type="text" class="form-control" id="filter-stock">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">Vận chuyển tiêu chuẩn</label>
                                                    <input type="text" class="form-control usd-price-format" id="filter-standard-shipping">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">Vận chuyển nhanh</label>
                                                    <input type="text" class="form-control usd-price-format" id="filter-express-shipping">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Vận chuyển quốc tế</label>
                                                    <input type="text" class="form-control usd-price-format" id="filter-international-shipping">
                                                </div>
                                                <div class="col-md-2 mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-primary w-100" id="apply-filter">Áp dụng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion" id="variantAccordion">
                                        @foreach ($variants ?? [] as $variantItem)
                                            <div class="accordion-item"
                                                data-variant-id="{{ $variantItem['attribute_value_ids'] }}">
                                                <h2 class="accordion-header">
                                                    <button type="button"
                                                        class="accordion-button collapsed position-relative"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#v{{ $variantItem['attribute_value_ids'] }}">
                                                        <span class="fw-bold">{{ $variantItem['variant_name'] }}</span>
                                                        <span class="ms-2 delete-variant text-danger position-absolute"
                                                            data-index="{{ $variantItem['attribute_value_ids'] }}">Xóa</span>
                                                    </button>
                                                </h2>
                                                <div id="v{{ $variantItem['attribute_value_ids'] }}"
                                                    class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-sku"
                                                                    class="form-label required">Mã sản phẩm</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['attribute_value_ids'] }}-sku"
                                                                    name="variants[{{ $variantItem['attribute_value_ids'] }}][sku]"
                                                                    aria-required="true" required="required"
                                                                    value="{{ $variantItem['sku'] }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-sale-price"
                                                                    class="form-label required">Giá</label>
                                                                <input type="text"
                                                                    class="form-control usd-price-format"
                                                                    id="variants-{{ $variantItem['attribute_value_ids'] }}-sale-price"
                                                                    name="variants[{{ $variantItem['attribute_value_ids'] }}][sale_price]"
                                                                    aria-required="true" required="required"
                                                                    value="{{ formatPrice($variantItem['sale_price']) }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-product-unit"
                                                                    class="form-label">Đơn vị</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['attribute_value_ids'] }}-product-unit"
                                                                    name="variants[{{ $variantItem['attribute_value_ids'] }}][product_unit]"
                                                                    value="{{ $variantItem['product_unit'] }}">
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-discount-price"
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

                                                                <input type="text"
                                                                    class="form-control usd-price-format"
                                                                    name="variants[{{ $variantItem['attribute_value_ids'] }}][discount_price]"
                                                                    id="variants-{{ $variantItem['attribute_value_ids'] }}-discount-price"
                                                                    value="{{ formatPrice($variantItem['discount_price']) }}">
                                                            </div>
                                                            <div class="col-md-6 variant-scheduled-time"
                                                                style="display: none;">
                                                                <div class="mb-3 position-relative">
                                                                    <label class="form-label"
                                                                        for="variants-{{ $variantItem['attribute_value_ids'] }}-discount-start">
                                                                        Từ ngày
                                                                    </label>
                                                                    <input class="form-control form-date-time"
                                                                        type="text"
                                                                        name="variants[{{ $variantItem['attribute_value_ids'] }}][discount_start]"
                                                                        id="variants-{{ $variantItem['attribute_value_ids'] }}-discount-start"
                                                                        value="{{ $variantItem['discount_start'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 variant-scheduled-time"
                                                                style="display: none;">
                                                                <div class="mb-3 position-relative">
                                                                    <label class="form-label"
                                                                        for="variants-{{ $variantItem['attribute_value_ids'] }}-discount-end">
                                                                        Đến ngày
                                                                    </label>
                                                                    <input class="form-control form-date-time"
                                                                        type="text"
                                                                        name="variants[{{ $variantItem['attribute_value_ids'] }}][discount_end]"
                                                                        id="variants-{{ $variantItem['attribute_value_ids'] }}-discount-end"
                                                                        value="{{ $variantItem['discount_end'] }}">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-stock"
                                                                    class="form-label">Số
                                                                    lượng</label>
                                                                <input type="text" class="form-control"
                                                                    id="variants-{{ $variantItem['attribute_value_ids'] }}-stock"
                                                                    name="variants[{{ $variantItem['attribute_value_ids'] }}][stock]"
                                                                    value="{{ $variantItem['stock'] }}">
                                                            </div>

                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-stock-status"
                                                                    class="form-label">Vận chuyển tiêu chuẩn (Mỹ)</label>
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control usd-price-format"
                                                                        name="variants[{{ $variantItem['attribute_value_ids'] }}][standard_shipping]"
                                                                        id="variants-{{ $variantItem['attribute_value_ids'] }}-standard_shipping"
                                                                        value="{{ formatPrice($variantItem['standard_shipping']) }}"
                                                                        placeholder="Nhập giá vận chuyển">
                                                                    <span class="input-group-text">USD</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-stock-status"
                                                                    class="form-label">Vận chuyển nhanh (Mỹ)</label>
                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control usd-price-format"
                                                                        name="variants[{{ $variantItem['attribute_value_ids'] }}][express_shipping]"
                                                                        id="variants-{{ $variantItem['attribute_value_ids'] }}-express_shipping"
                                                                        value="{{ formatPrice($variantItem['express_shipping']) }}"
                                                                        placeholder="Nhập giá vận chuyển">
                                                                    <span class="input-group-text">USD</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3 position-relative col-md-3">
                                                                <label
                                                                    for="variants-{{ $variantItem['attribute_value_ids'] }}-stock-status"
                                                                    class="form-label">Vận chuyển quốc tế</label>

                                                                <div class="input-group">
                                                                    <input type="text"
                                                                        class="form-control usd-price-format"
                                                                        name="variants[{{ $variantItem['attribute_value_ids'] }}][international_shipping]"
                                                                        id="variants-{{ $variantItem['attribute_value_ids'] }}-international_shipping"
                                                                        value="{{ formatPrice($variantItem['international_shipping']) }}"
                                                                        placeholder="Nhập giá vận chuyển">
                                                                    <span class="input-group-text">USD</span>
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

                                    <input name="cross_sell" type="hidden"
                                        value="{{ $product && $product->cross_sell ? implode(',', $product->cross_sell) : '' }}">

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

                                <div class="tab-pane fade" id="shipping" role="tabpanel"
                                    aria-labelledby="shipping-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="mb-3 position-relative col-md-4">
                                                    <label for="standard_shipping" class="form-label">Vận chuyển tiêu
                                                        chuẩn (Mỹ)</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control usd-price-format"
                                                            name="standard_shipping" id="standard_shipping"
                                                            value="{{ formatPrice(optional($product)->standard_shipping) }}"
                                                            placeholder="Nhập giá vận chuyển">
                                                        <span class="input-group-text">USD</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3 position-relative col-md-4">
                                                    <label for="express_shipping" class="form-label">Vận chuyển nhanh
                                                        (Mỹ)</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control usd-price-format"
                                                            name="express_shipping" id="express_shipping"
                                                            value="{{ formatPrice(optional($product)->express_shipping) }}"
                                                            placeholder="Nhập giá vận chuyển">
                                                        <span class="input-group-text">USD</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3 position-relative col-md-4">
                                                    <label for="international_shipping" class="form-label">Vận chuyển quốc
                                                        tế</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control usd-price-format"
                                                            name="international_shipping" id="international_shipping"
                                                            value="{{ formatPrice(optional($product)->international_shipping) }}"
                                                            placeholder="Nhập giá vận chuyển">
                                                        <span class="input-group-text">USD</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                            {{ config('app.url') . '/' . ($product && $product->category ? $product->category->slug . '/' : '') . optional($product)->slug }}
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
                                <input name="is_featured" type="checkbox" value="1" @checked(optional($product)->is_featured)>
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
                                placeholder="tags sản phẩm"
                                value="{{ $product ? implode(', ', $product->tags ?? []) : '' }}">
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

                resultList.append(`
                <a href="javascript:void(0);" class="list-group-item list-group-item-action selectable-item"
                    data-id="${product.id}" data-name="${product.name}" data-image="${product.image}" data-price="${product.price}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url('${product.image}')"></span>
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
                id: 'description',
                maxLength: 160
            },
            {
                id: 'seo_description',
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

        autoGenerateSlug('#name', '#slug')

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
                $('li#tabs-shipping').hide();

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
                $('li#tabs-shipping').show();
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

            let preloaded = @json($preloadedImages ?? []);


            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'images',
                preloadedInputName: 'old',
                maxSize: 10 * 1024 * 1024,
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

            $(document).ready(function() {
                $('.select-all').each(function() {
                    // Lấy ra ID từ phần tử accordion cha gần nhất
                    let accordionItem = $(this).closest('.accordion-item');
                    let attributeId = accordionItem.attr('id').replace('accordion-', '');

                    // Gán sự kiện click cho từng nút "Chọn tất cả"
                    $(this).on('click', function() {
                        let selectElement = $('#select-' + attributeId);
                        selectElement.find('option').prop('selected', true);
                        selectElement.trigger('change'); // Cập nhật lại Select2
                    });

                    // Gán sự kiện khi thay đổi select
                    $('#select-' + attributeId).on('change', function() {
                        checkIfAnyValueSelected
                            (); // Hàm kiểm tra để kích hoạt nút "Lưu thuộc tính"
                    });
                });
            });


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
                    generateVariants();
                    $('#attribute').removeClass('active show');
                    $('#attribute-tab').removeClass('active');

                    $('#variant').addClass('active show');
                    $('#variant-tab').addClass('active');
                } else {
                    alert('Vui lòng chọn ít nhất một thuộc tính và giá trị!');
                }
            });

            function generateVariants() {
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

                // Lấy danh sách ID hợp lệ sau khi combine
                const validVariantIds = result.map(v => v.id);

                // Xóa các DOM biến thể không còn hợp lệ
                $('#variantAccordion .accordion-item').each(function() {
                    const variantId = String($(this).data('variant-id'));

                    if (!validVariantIds.includes(variantId)) {
                        $(this).remove();
                    }
                });

                // Cập nhật select filter với tất cả các giá trị thuộc tính
                let allAttributeValues = [];
                values.forEach(attributeValues => {
                    attributeValues.forEach(value => {
                        let [id, name] = value.split('-');
                        allAttributeValues.push({id, name});
                    });
                });

                // Cập nhật select filter
                let filterSelect = $('#filter-attribute-values');
                filterSelect.empty();
                filterSelect.append('<option value="all">Tất cả</option>');
                allAttributeValues.forEach(value => {
                    filterSelect.append(`<option value="${value.name}">${value.name}</option>`);
                });

                // Khởi tạo select2 cho filter
                filterSelect.select2({
                    placeholder: "Chọn giá trị thuộc tính",
                    allowClear: true,
                    width: '100%'
                });

                // Xử lý sự kiện khi chọn option "All"
                let isChanging = false;
                filterSelect.on('change', function(e) {
                    if (isChanging) return;

                    let selectedValues = $(this).val();

                    isChanging = true;

                    // Enable tất cả các option trước
                    $(this).find('option').prop('disabled', false);

                    if (!selectedValues || selectedValues.length === 0) {
                        // Nếu không có giá trị nào được chọn, enable tất cả
                        isChanging = false;
                        return;
                    }

                    if (selectedValues.includes('all')) {
                        // Nếu chọn "All", disable tất cả các option khác
                        $(this).find('option:not([value="all"])').prop('disabled', true);
                        $(this).val(['all']).trigger('change');
                    } else {
                        // Nếu chọn các option khác, disable option "All"
                        $(this).find('option[value="all"]').prop('disabled', true);
                    }

                    isChanging = false;
                });

                // Hiển thị hoặc cập nhật biến thể
                result.forEach((variant, index) => {
                    if ($(`#variantAccordion [data-variant-id="${variant.id}"]`).length > 0) {
                        return;
                    }

                    let newHtml = `
                            <div class="accordion-item" data-variant-id="${variant.id}">
                                <h2 class="accordion-header">
                                    <button type="button" class="accordion-button collapsed position-relative" data-bs-toggle="collapse" data-bs-target="#v${variant.id}">
                                        <span class="fw-bold">${variant.name}</span>
                                        <span class="ms-2 delete-variant text-danger position-absolute" data-index="${variant.id}">Xóa</span>
                                    </button>
                                </h2>
                                <div id="v${variant.id}" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="mb-3 position-relative col-md-3">
                                                <label for="variants-${variant.id}-sku" class="form-label required">Mã sản phẩm</label>
                                                <input type="text" class="form-control" id="variants-${variant.id}-sku" name="variants[${variant.id}][sku]"
                                                    aria-required="true" required="required" value="${convertToSKU(variant.name)}">
                                            </div>
                                            <div class="mb-3 position-relative col-md-3">
                                                <label for="variants-${variant.id}-sale-price" class="form-label required">Giá</label>
                                                <input type="text" class="form-control usd-price-format" id="variants-${variant.id}-sale-price" name="variants[${variant.id}][sale_price]"
                                                    aria-required="true" required="required">
                                            </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                        <label for="variants-${variant.id}-product-unit" class="form-label required">Đơn vị</label>
                                                        <input type="text" class="form-control" id="variants-${variant.id}-product-unit" name="variants[${variant.id}][product_unit]">
                                                    </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                        <label for="variants-${variant.id}-discount-price" class="form-label">Giá ưu đãi
                                                            <span class="form-label-description">
                                                                <a href="javascript:void(0)" class="variant-turn-on-schedule">Lên lịch</a>
                                                                <a class="variant-turn-off-schedule" style="display: none"
                                                                    href="javascript:void(0)">
                                                                    Hủy
                                                                </a>
                                                            </span>
                                                        </label>

                                                        <input type="text" class="form-control usd-price-format"
                                                            name="variants[${variant.id}][discount_price]" id="variants-${variant.id}-discount-price">
                                                    </div>
                                                    <div class="col-md-6 variant-scheduled-time" style="display: none;">
                                                        <div class="mb-3 position-relative">
                                                            <label class="form-label" for="variants-${variant.id}-discount-start">
                                                                Từ ngày
                                                            </label>
                                                            <input class="form-control form-date-time" type="text"
                                                                name="variants[${variant.id}][discount_start]" id="variants-${variant.id}-discount-start">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 variant-scheduled-time" style="display: none;">
                                                        <div class="mb-3 position-relative">
                                                            <label class="form-label" for="variants-${variant.id}-discount-end">
                                                                Đến ngày
                                                            </label>
                                                            <input class="form-control form-date-time" type="text"
                                                                name="variants[${variant.id}][discount_end]" id="variants-${variant.id}-discount-end">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                            <label for="variants-${variant.id}-stock" class="form-label">Số lượng</label>
                                                            <input type="text" class="form-control"
                                                                id="variants-${variant.id}-stock"
                                                                name="variants[${variant.id}][stock]">
                                                    </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                        <label
                                                            for="variants-${variant.id}-stock-status"
                                                            class="form-label">Vận chuyển tiêu chuẩn (Mỹ)</label>
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control usd-price-format"
                                                                name="variants[${variant.id}][standard_shipping]"
                                                                id="variants-${variant.id}-standard_shipping">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                        <label
                                                            for="variants-${variant.id}-stock-status"
                                                            class="form-label">Vận chuyển nhanh (Mỹ)</label>
                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control usd-price-format"
                                                                name="variants[${variant.id}][express_shipping]"
                                                                id="variants-${variant.id}-express_shipping">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 position-relative col-md-3">
                                                        <label
                                                            for="variants-${variant.id}-stock-status"
                                                            class="form-label">Vận chuyển quốc tế</label>

                                                        <div class="input-group">
                                                            <input type="text"
                                                                class="form-control usd-price-format"
                                                                name="variants[${variant.id}][international_shipping]"
                                                                id="variants-${variant.id}-international_shipping">
                                                            <span class="input-group-text">USD</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                    $('#variantAccordion').append(newHtml);
                });
            }

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
                    enableTime: true,
                    dateFormat: "d-m-Y H:i",
                    altInput: true,
                    altFormat: "d-m-Y H:i",
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
                window.location.href = "{{ route('admin.products.index') }}"
            })

            flatpickr(".form-date-time", {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                altInput: true,
                altFormat: "d-m-Y H:i",
                locale: "vn"
            });

            // Thêm xử lý sự kiện cho nút áp dụng filter
            $('#apply-filter').on('click', function() {
                let selectedValues = $('#filter-attribute-values').val();
                let price = $('#filter-price').val();
                let stock = $('#filter-stock').val();
                let standardShipping = $('#filter-standard-shipping').val();
                let expressShipping = $('#filter-express-shipping').val();
                let internationalShipping = $('#filter-international-shipping').val();

                if (!selectedValues || selectedValues.length === 0) {
                    alert('Vui lòng chọn ít nhất một giá trị thuộc tính!');
                    return;
                }

                // Lặp qua tất cả các biến thể
                $('#variantAccordion .accordion-item').each(function() {
                    let variantName = $(this).find('.accordion-button span.fw-bold').text();
                    let variantValues = variantName.split(' - ');
                    let shouldUpdate = false;

                    // Kiểm tra xem có phải chọn "All" không
                    if (selectedValues.includes('all')) {
                        shouldUpdate = true;
                    } else {
                        // Kiểm tra xem biến thể có chứa bất kỳ giá trị nào được chọn không
                        shouldUpdate = selectedValues.some(value => variantValues.includes(value));
                    }

                    if (shouldUpdate) {
                        // Cập nhật giá nếu có
                        if (price) {
                            $(this).find(`input[name*="[sale_price]"]`).val(price);
                        }
                        // Cập nhật số lượng nếu có
                        if (stock) {
                            $(this).find(`input[name*="[stock]"]`).val(stock);
                        }
                        // Cập nhật vận chuyển tiêu chuẩn nếu có
                        if (standardShipping) {
                            $(this).find(`input[name*="[standard_shipping]"]`).val(standardShipping);
                        }
                        // Cập nhật vận chuyển nhanh nếu có
                        if (expressShipping) {
                            $(this).find(`input[name*="[express_shipping]"]`).val(expressShipping);
                        }
                        // Cập nhật vận chuyển quốc tế nếu có
                        if (internationalShipping) {
                            $(this).find(`input[name*="[international_shipping]"]`).val(internationalShipping);
                        }
                    }
                });
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
