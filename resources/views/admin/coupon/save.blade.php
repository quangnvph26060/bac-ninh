@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $breadcrumbItems = [
                    ['name' => 'mã giảm giá', 'url' => route('admin.coupons.index')],
                    ['name' => $coupon ? "$title - {$coupon->name}" : $title],
                ];
            @endphp

            <div class="page-header">
                <x-breadcrumb :items="$breadcrumbItems" />
            </div>
        </div>

        <form id="myForm">

            @if ($coupon)
                @method('PUT')
            @endif

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="code" class="form-label required">CODE</label>
                                        <input type="text" placeholder="Code" aria-required="true" required
                                            class="form-control" name="code" id="code"
                                            value="{{ optional($coupon)->code }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="value" class="form-label required">Giá trị giảm <code>($)</code></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="value"
                                                placeholder="Nhập giá trị giảm"
                                                value="{{ formatPrice(optional($coupon)->value) }}">
                                            <select class="form-select text-white" name="type"
                                                style="max-width: 75px; background-color: rgb(212, 212, 212);">
                                                <option value="fixed">USD</option>
                                                <option value="percent">%</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="max_discount" class="form-label">Giá trị giảm tối đa <code>($)</code></label>
                                        <input type="text" placeholder="Giá trị giảm tối đa" class="form-control"
                                            name="max_discount" id="max_discount"
                                            value="{{ formatPrice(optional($coupon)->max_discount) }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu <code>($)</code></label>
                                        <input type="text" placeholder="Giá trị đơn hàng tối thiểu" class="form-control"
                                            name="min_order_value" id="min_order_value"
                                            value="{{ formatPrice(optional($coupon)->min_order_value) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" for="">
                                                Bắt đầu
                                            </label>
                                            <input class="form-control form-date-time" type="text" name="start_date"
                                                id="start_date" placeholder="d-m-Y H:i"
                                                value="{{ $coupon && $coupon->start_date ? $coupon->start_date->format('d-m-Y H:i') : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 position-relative">
                                            <label class="form-label" for="">
                                                Kết thúc
                                            </label>
                                            <input class="form-control form-date-time" type="text" name="end_date"
                                                id="end_date" placeholder="d-m-Y H:i"
                                                value="{{ $coupon && $coupon->end_date ? $coupon->end_date->format('d-m-Y H:i') : '' }}">
                                        </div>
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="usage_limit" class="form-label">Số lượt sử dụng</label>
                                        <input type="number" placeholder="Số lượt sử dụng" class="form-control"
                                            name="usage_limit" id="usage_limit"
                                            value="{{ optional($coupon)->usage_limit }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="usage_per_user" class="form-label">Lượt dùng mỗi người</label>
                                        <input type="number" placeholder="Lượt dùng mỗi người" class="form-control"
                                            name="usage_per_user" id="usage_per_user"
                                            value="{{ optional($coupon)->usage_per_user }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" id="description" placeholder="Mô tả">{!! optional($coupon)->description !!}</textarea>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.categories.index')])

                    <x-status :status="optional($coupon)->status" />
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('select[name="type"]').on('change', function() {
                const type = $(this).val();
                const $oldInput = $('input[name="value"]');
                const placeholder = type === "percent" ? "Nhập phần trăm giảm" : "Nhập số tiền giảm";

                // Tạo input mới với type tương ứng
                const $newInput = $('<input>', {
                    type: type === "percent" ? 'number' : 'text',
                    class: 'form-control',
                    name: 'value',
                    placeholder: placeholder,
                });

                // Nếu percent thì set max 100
                if (type === "percent") {
                    $newInput.attr('max', 100);
                    $newInput.on('input', function() {
                        if (parseFloat(this.value) > 100) {
                            this.value = 100;
                        }
                    });
                }

                $oldInput.replaceWith($newInput);
            });

            flatpickr(".form-date-time", {
                enableTime: true, // Bật chọn giờ
                dateFormat: "d-m-Y H:i", // Định dạng ngày + giờ
                time_24hr: true, // Hiển thị giờ theo định dạng 24h
                locale: "vn" // Ngôn ngữ tiếng Việt
            });

            updateCharCount('#code', 250)

            convertToAsciiUpper('#code')

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.coupons.index') }}"
            })
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">
@endpush
