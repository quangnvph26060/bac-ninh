@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = [
                    ['name' => 'thương hiệu', 'url' => route('admin.employees.index')],
                    ['name' => !empty($employee) ? "{$title} - {$employee->full_name}" : $title],
                ];
            @endphp
            <x-breadcrumb :items="$items" />
        </div>


        <form action="" method="post" id="myForm" enctype="multipart/form-data">
            @isset($employee)
                @method('PUT')
            @endisset

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="full_name" class="form-label required">Tên đầy đủ</label>
                                        <input type="text" placeholder="Tên đầy đủ" class="form-control" name="full_name"
                                            id="full_name" value="{{ $employee->full_name ?? '' }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="phone" class="form-label">Số điện thoại</label>
                                        <input type="text" placeholder="Số điện thoại" class="form-control"
                                            name="phone" id="phone" value="{{ $employee->phone ?? '' }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-6">
                                        <label for="email" class="form-label required">Địa chỉ email</label>
                                        <input type="text" placeholder="Địa chỉ email" class="form-control"
                                            name="email" id="email" value="{{ $employee->email ?? '' }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="gender" class="form-label required">Giới tính</label>

                                        <select name="gender" id="gender" class="form-select form-control">
                                            <option value="other" @selected(($employee->gender ?? '') == 'other')>Khác</option>
                                            <option value="male" @selected(($employee->gender ?? '') == 'male')>Nam</option>
                                            <option value="female" @selected(($employee->gender ?? '') == 'female')>Nữ</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="date_of_birth" class="form-label">Ngày sinh</label>
                                        <input type="text" placeholder="Ngày sinh d-m-Y" class="form-control"
                                            name="date_of_birth" id="date_of_birth"
                                            value="{{ isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->format('d-m-Y') : '' }}">
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="address" class="form-label">Địa chỉ</label>
                                        <textarea rows="3" name="address" class="form-control" id="address" placeholder="Địa chỉ">{{ $employee->address ?? '' }}</textarea>
                                    </div>

                                    @if (!isset($employee))
                                        <div class="mb-3 col-md-6 position-relative">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="password" class="form-label required">Mật khẩu</label>
                                                <small id="generatePassword" class="text-primary cursor">Gợi ý mật
                                                    khẩu</small>
                                            </div>

                                            <input type="password" placeholder="Mật khẩu" class="form-control"
                                                name="password" id="password" value="">
                                            <i class="fa-regular fa-eye"></i>
                                            <i class="fa-regular fa-eye-slash" style="display: none"></i>
                                        </div>

                                        <div class="mb-3 col-md-6 position-relative">
                                            <label for="confirm_password" class="form-label required">Nhập lại mật
                                                khẩu</label>
                                            <input type="password" placeholder="Nhập lại mật khẩu" class="form-control"
                                                name="confirm_password" id="confirm_password" value="">
                                            <i class="fa-regular fa-eye"></i>
                                            <i class="fa-regular fa-eye-slash" style="display: none"></i>
                                        </div>
                                    @endif


                                    <div class="mb-3 col-md-6">
                                        <label for="identity_card_number" class="form-label">Số CCCD</label>
                                        <input type="text" placeholder="Số CCCD" class="form-control"
                                            name="identity_card_number" id="identity_card_number"
                                            value="{{ $employee->identity_card_number ?? '' }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="contract_type" class="form-label required"> Loại hợp đồng</label>

                                        <select name="contract_type" id="contract_type" class="form-select form-control">
                                            <option value="full-time" @selected(($employee->contract_type ?? '') == 'full-time')>Toàn thời gian</option>
                                            <option value="part-time" @selected(($employee->contract_type ?? '') == 'part-time')>Bán thời gian</option>
                                            <option value="probation" @selected(($employee->contract_type ?? '') == 'probation')>Thử việc</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 position-relative col-md-12">
                                        <label for="note" class="form-label">Ghi chú</label>
                                        <textarea rows="3" name="note" class="form-control" id="note" placeholder="Ghi chú">{{ $employee->note ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($permissions as $groupName => $permission)
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                                <div>
                                    <strong>{{ $groupName }}</strong>
                                    <span class="badge bg-light text-dark ms-2">{{ count($permission) }} quyền</span>
                                </div>
                                <div>
                                    <input type="checkbox" class="form-check-input select-all cursor"
                                        id="selectAll-{{ \Str::slug($groupName) }}">
                                    <label for="selectAll-{{ \Str::slug($groupName) }}"
                                        class="form-check-label ms-1 text-white cursor">Chọn tất cả</label>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-wrap gap-3">
                                @foreach ($permission as $item)
                                    <div class="form-check">
                                        <input class="form-check-input cursor" type="checkbox" name="permissions[]"
                                            id="{{ \Str::slug($item->name) }}" value="{{ $item->name }}"
                                            @checked(in_array($item->name, !empty($assignedPermissions) ? $assignedPermissions : []))>
                                        <label class="form-check-label mb-0 cursor"
                                            for="{{ \Str::slug($item->vi_name) }}">{{ $item->vi_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button', ['redirect' => route('admin.employees.index')])

                    <x-status :status="$employee->status ?? ''" />

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title fs-6 fw-bold">Avatar</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_avatar"
                                style="cursor: pointer; width: 100%; height: auto; object-fit: cover;"
                                src="{{ showImage($employee->avatar ?? '') }}" alt=""
                                onclick="document.getElementById('avatar').click();">

                            <input type="file" name="avatar" id="avatar" class="form-control d-none"
                                accept="image/*" onchange="previewImage(event, 'show_avatar')">
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title fs-6 fw-bold fs-6 fw-bold">Ảnh CCCD mặt trước</h4>
                        </div>
                        <div class="card-body">
                            <img class="img-thumbnail" id="show_identity_card_image"
                                style="cursor: pointer; width: 100%; height: auto; object-fit: cover;"
                                src="{{ showImage($employee->identity_card_image ?? '') }}" alt=""
                                onclick="document.getElementById('identity_card_image').click();">

                            <input type="file" name="identity_card_image" id="identity_card_image"
                                class="form-control d-none" accept="image/*"
                                onchange="previewImage(event, 'show_identity_card_image')">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/plugin/flatpickr/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.card.mb-3').each(function() {
                const card = $(this);


                const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])').length > 0 &&
                    card.find('input[type="checkbox"]:not([id^="selectAll"])').length ===
                    card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;

                card.find('.select-all').prop('checked', allChecked);

                // Sự kiện khi nhấn "Chọn tất cả"
                card.find('.select-all').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    card.find('input[type="checkbox"]:not([id^="selectAll"])').prop('checked',
                        isChecked);
                });

                // Sự kiện khi checkbox con thay đổi
                card.find('input[type="checkbox"]:not([id^="selectAll"])').on('change', function() {
                    const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])')
                        .length ===
                        card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;
                    card.find('.select-all').prop('checked', allChecked);
                });
            });

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.employees.index') }}"
            })

            $('.fa-eye, .fa-eye-slash').on('click', function() {
                var input = $(this).siblings('input');
                var isPasswordVisible = input.attr('type') === 'text';

                // Chuyển đổi giữa loại mật khẩu và loại văn bản
                input.attr('type', isPasswordVisible ? 'password' : 'text');

                // Chuyển đổi giữa biểu tượng mắt mở và mắt đóng
                $(this).toggle();
                $(this).siblings('.fa-eye, .fa-eye-slash').toggle();
            });

            function generateStrongPassword(length = 20) {
                const lowercase = "abcdefghijklmnopqrstuvwxyz";
                const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                const numbers = "0123456789";
                const symbols = "!@#$%^&*()_+-=[]{}|;:',.<>?/";

                const all = lowercase + uppercase + numbers + symbols;

                let password = '';
                password += lowercase[Math.floor(Math.random() * lowercase.length)];
                password += uppercase[Math.floor(Math.random() * uppercase.length)];
                password += numbers[Math.floor(Math.random() * numbers.length)];
                password += symbols[Math.floor(Math.random() * symbols.length)];

                for (let i = 4; i < length; i++) {
                    password += all[Math.floor(Math.random() * all.length)];
                }

                // Shuffle mật khẩu
                return password.split('').sort(() => 0.5 - Math.random()).join('');
            }

            $('#generatePassword').on('click', function() {
                const newPassword = generateStrongPassword(20);
                $('#password').val(newPassword);
            });

            $('#role').select2({
                placeholder: 'Chọn quyền hạn',
                allowClear: true,
                tags: false, // không cho nhập linh tinh
            });

            flatpickr("#date_of_birth", {
                enableTime: false,
                dateFormat: "d-m-Y",
                altInput: true,
                altFormat: "d-m-Y",
                locale: "vn"
            });
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">


    <style>
        .fa-regular {
            position: absolute;
            top: 40px;
            right: 25px;
            cursor: pointer;
        }
    </style>
@endpush
