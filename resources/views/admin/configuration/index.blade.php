@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'cấu hình chung']]" />
        </div>


        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @method('PUT')

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" placeholder="Tiêu đề" class="form-control" name="title"
                                            id="title" value="{{ $config['title'] }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="company" class="form-label">Tên công ty</label>
                                        <input type="text" placeholder="Tên công ty" class="form-control" name="company"
                                            id="company" value="{{ $config['company'] }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" placeholder="Email" class="form-control" name="email"
                                            id="email" value="{{ $config['email'] }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="hotline" class="form-label">Hotline</label>
                                        <input type="text" placeholder="Hotline" class="form-control" name="hotline"
                                            id="hotline" value="{{ $config['hotline'] }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <textarea name="address" id="address" class="form-control" rows="3">{{ $config['address'] }}</textarea>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="copyright" class="form-label">Trân trang</label>
                                        <input type="text" placeholder="Trân trang" class="form-control" name="copyright"
                                            id="copyright" value="{{ $config['copyright'] }}">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Mạng xã hội</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 position-relative col-md-12">
                                    <label for="groups" class="form-label">groups</label>
                                    <input type="text" placeholder="Groups" class="form-control" name="groups"
                                        id="groups" value="{{ $config['groups'] }}">
                                </div>

                                <div class="mb-3 position-relative col-md-12">
                                    <label for="facebook" class="form-label">facebook</label>
                                    <input type="text" placeholder="Facebook" class="form-control" name="facebook"
                                        id="facebook" value="{{ $config['facebook'] }}">
                                </div>

                                <div class="mb-3 position-relative col-md-12">
                                    <label for="youtobe" class="form-label">youtobe</label>
                                    <input type="text" placeholder="Youtobe" class="form-control" name="youtobe"
                                        id="youtobe" value="{{ $config['youtobe'] }}">
                                </div>

                                <div class="mb-3 position-relative col-md-12">
                                    <label for="tiktok" class="form-label">tiktok</label>
                                    <input type="text" placeholder="Tiktok" class="form-control" name="tiktok"
                                        id="tiktok" value="{{ $config['tiktok'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-seo :model="$config" />
                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">

                    @include('admin.components.button')

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Logo</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_logo"
                                style="cursor: pointer; width: 100%; height: auto; object-fit: cover;"
                                src="{{ showImage($config['logo']) }}" alt=""
                                onclick="document.getElementById('logo').click();">

                            <input type="file" name="logo" id="logo" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_logo')">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Favicon</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_favicon"
                                style="cursor: pointer; width: 100%; height: auto; object-fit: cover;"
                                src="{{ showImage($config['favicon']) }}" alt=""
                                onclick="document.getElementById('favicon').click();">

                            <input type="file" name="favicon" id="favicon" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_favicon')">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thuế</h4>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text" name="tax_rate" id="tax_rate"
                                    class="form-control usd-price-format" placeholder="Nhập thuế"
                                    value="{{ formatPrice($config['tax_rate']) }}">
                                <span class="input-group-text">USD</span>
                            </div>
                        </div>
                    </div>

                    @php
                        $presetHours = [1, 6, 12, 24];
                        $selectedValue = $config['order_send_delay_hours'] ?? '';
                    @endphp

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Phê duyệt đơn hàng</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($presetHours as $hour)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="order_send_delay_hours"
                                        id="delay{{ $hour }}" value="{{ $hour }}"
                                        {{ $selectedValue == $hour ? 'checked' : '' }}>
                                    <label class="form-check-label" for="delay{{ $hour }}">{{ $hour }}
                                        tiếng</label>
                                </div>
                            @endforeach

                            <div class="input-group mb-3 mt-2">
                                <input type="text" class="form-control" id="custom-order-send-delay-hours"
                                    name="custom_order_send_delay_hours"
                                    value="{{ !in_array($selectedValue, $presetHours) ? $selectedValue : '' }}">
                                <label class="input-group-text" for="custom-order-send-delay-hours">Tiếng</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#custom-order-send-delay-hours').on('focus', function() {
            $('input[name="order_send_delay_hours"]').prop('checked', false);
        });

        $('input[name="order_send_delay_hours"]').on('change', function() {
            $('#custom-order-send-delay-hours').val('');
        });

        $('#toggle-seo-fields').click(function() {
            $('.seo-edit-section').toggle(); // Ẩn/hiện các trường SEO
        });

        const inputIds = [{
                id: 'title',
                maxLength: 250
            },
            {
                id: 'company',
                maxLength: 250
            },
            {
                id: 'copyright',
                maxLength: 250
            },
            {
                id: 'seo_title',
                maxLength: 250
            },
            {
                id: 'seo_description',
                maxLength: 500
            },
            {
                id: 'email',
                maxLength: 250
            },
            {
                id: 'address',
                maxLength: 255
            }
        ];

        $.each(inputIds, function(index, value) {
            updateCharCount(`#${value.id}`, value.maxLength);
        });

        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                Notifications(response.message, "success");
            })
        })
    </script>
@endpush
