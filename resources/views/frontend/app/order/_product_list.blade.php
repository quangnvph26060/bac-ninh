<div class="row g-3">
    @foreach ($products as $product)
        <div class="col-6 col-lg-3 col-xl-2 product_item" data-id="{{ $product->id }}">
            <div class="product_card h-100">
                <div class="product_image">
                    <img src="{{ showImage($product->image) }}" alt="{{ $product->name }}">
                </div>
                <div class="product_info">
                    <p class="product_name fw-semibold">{{ $product->name }}</p>

                    <p class="product_price">
                        @php
                            $record = $product->variants->isNotEmpty() ? $product->variants->first() : $product;
                        @endphp

                        @if (isOnSale($record))
                            <span class="">${{ finalPrice($record->discount_price) }}</span>

                            <small class="text-muted"> <del
                                    class="ms-2">${{ formatPrice($record->sale_price) }}</del></small>
                        @else
                            <span class="">${{ formatPrice($record->sale_price) }}</span>
                        @endif
                    </p>

                    <p class="product_attribute">
                        @if ($product->attributes->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 align-items-start h-10">
                                @foreach ($product->attributes as $attribute)
                                    @php
                                        $valueIds = $valueIds = json_decode(
                                            $attribute->pivot->attribute_values_ids,
                                            true,
                                        );
                                        $count = is_array($valueIds) ? count($valueIds) : 0;
                                    @endphp

                                    @if ($count > 0)
                                        <p class="title text-muted small">
                                            {{ $count }}
                                            {{ $attribute->name }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endforeach
    {{ $products->links('vendor.pagination.custom') }}
</div>
