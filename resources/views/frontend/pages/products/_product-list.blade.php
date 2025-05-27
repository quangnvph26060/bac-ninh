@if ($products->isNotEmpty())
    <div class="list_prd_catalogs w-100">
        @foreach ($products as $product)
            <div class="box_product_item border rounded">
                <a href="{{ route('products.detail', [$product->category->slug, $product->slug]) }}">
                    <div class="mockup_prd_wrapper rounded">
                        <img loading="lazy" class="img-fluid w-100 rounded-top" src="{{ showImage($product->image) }}"
                            alt="Table Mat" style="border-radius: 4px 4px 0px 0px" />
                    </div>
                    <div class="content_prd_card rounded-bottom" style="border-radius: 0px 0px 4px 4px">
                        <h3 class="name_prd">{{ $product->name }}</h3>

                        <p class="price-product pb-0">

                            @if (isOnSale($product))
                                <span class="text_color text-sm mb-2">
                                    {{ formatPrice($product->discount_price) }} USD</span>

                                <small class="text-muted"> <del
                                        class="ms-2">{{ formatPrice($product->sale_price) }} USD</del></small>
                            @else
                                <span class="text_color text-sm mb-2">
                                    {{ formatPrice($product->sale_price) }} USD</span>
                            @endif
                        </p>

                        @if ($product->attributes->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 align-items-start">

                                @foreach ($product->attributes as $attribute)
                                    @php
                                        $valueIds = json_decode($attribute->pivot->attribute_values_ids, true);
                                        $count = is_array($valueIds) ? count($valueIds) : 0;
                                    @endphp

                                    @if ($count > 0)
                                        <p class="title text-muted small mb-0">{{ $count }}
                                            {{ $attribute->name }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                    </div>
                </a>
            </div>
        @endforeach

    </div>
@else
    <div class="no-product-wrapper text-center py-5">
        <div class="empty-cart-animation">
            <div class="cart-icon">
                <div class="wheel left"></div>
                <div class="wheel right"></div>
                <div class="cart-body"></div>
            </div>
        </div>
        <h4 class="mt-4 text-muted">Không tìm thấy sản phẩm nào</h4>
        <p class="text-secondary">Vui lòng thử lại với bộ lọc khác nhé!</p>
    </div>
@endif
