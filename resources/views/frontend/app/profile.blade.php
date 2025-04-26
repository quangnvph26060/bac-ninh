@extends('frontend.app')

@section('content')
    <div class="container-wrapper">
        <div class="billing__title__wrapper d-flex align-items-center justify-content-between flex-wrap gap-4">
            <h1 class="billing__title__content">Hồ sơ cá nhân</h1>
        </div>
    </div>

    <form id="myForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Avatar Section -->
            <div class="col-lg-3 col-md-4 avatar-section">
                <div class="avatar-container rounded-circle">
                    <img src="{{ showImage($account->img_url) }}" alt="Avatar" id="avatarPreview" class="avatar-preview">
                    <div class="avatar-overlay" onclick="document.getElementById('avatarInput').click()">
                        <i class="bi bi-camera-fill"></i> <!-- Sử dụng icon của Bootstrap -->
                    </div>
                    <input type="file" name="img_url" class="d-none" id="avatarInput" accept="image/*">
                </div>
                <div class="w-100 position-relative">
                    <label for="oldPassword" class="form-label">Mật khẩu cũ</label>
                    <input type="password" class="form-control pe-5" name="old_password" id="oldPassword"
                        placeholder="Nhập mật khẩu cũ">
                    <i class="bi bi-eye-slash position-absolute translate-middle-y me-3 toggle-password"
                        data-target="#oldPassword" style="cursor: pointer;"></i>
                </div>

                <div class="w-100 mt-3 position-relative">
                    <label for="newPassword" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control pe-5" name="new_password" id="newPassword"
                        placeholder="Nhập mật khẩu mới">
                    <i class="bi bi-eye-slash position-absolute translate-middle-y me-3 toggle-password"
                        data-target="#newPassword" style="cursor: pointer;"></i>
                </div>

                <button type="button" class="ant-btn ant-btn-primary w-100 mt-3" id="change-password">Đổi mật khẩu</button>
            </div>
            <!-- Form Section -->
            <div class="col-lg-9 col-md-8 form-section">
                <div class="row g-3">
                    <!-- Profile Info -->
                    <div class="col-md-12">
                        <label for="name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Họ tên"
                            value="{{ $account->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                            value="{{ $account->email }}">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại"
                            value="{{ $account->phone }}">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label me-3">Giới tính</label>
                        @php
                            $gender = [
                                'male' => 'Nam',
                                'female' => 'Nữ',
                                'other' => 'Khác',
                            ];
                        @endphp
                        <select name="gender" id="gender" class="form-select">
                            <option value="">--- Chọn giới tính ---</option>
                            @foreach ($gender as $key => $value)
                                <option value="{{ $key }}" @selected($account->gender == $key)>{{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="day_of_birth" class="form-label">Ngày sinh</label>
                        <input type="text" id="day_of_birth" name="day_of_birth" class="form-control"
                            placeholder="Chọn ngày sinh"
                            value="{{ $account->day_of_birth ? $account->day_of_birth->format('d-m-Y') : '' }}" />
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" id="address" rows="4" placeholder="Địa chỉ">{{ $account->address }}</textarea>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="ant-btn ant-btn-primary">Lưu thông tin</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.getElementById('avatarInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        $('.toggle-password').on('click', function() {
            const input = $($(this).data('target'));
            const icon = $(this);

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            }
        });

        $('#myForm').on('submit', function(e) {
            e.preventDefault();

            let $form = new FormData(this);

            $.ajax({
                url: '{{ route('profile.update') }}',
                method: 'POST',
                data: $form,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(response) {
                    notyf.success(response.message);
                    $('.avatar__info .avata_image img').attr('src', response.data.image)
                    $('.user__info_name .info_name').text(response.data.name)
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                    notyf.error(xhr.responseJSON.message);
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        })

        $('#change-password').on('click', function() {
            let $old_password = $('input[name="old_password"]');
            let $new_password = $('input[name="new_password"]');

            $.ajax({
                url: '{{ route('change.password') }}',
                method: 'POST',
                data: {
                    old_password: $old_password.val(),
                    new_password: $new_password.val()
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(response) {
                    notyf.success(response.message);
                    $old_password.val(''); // Xóa input
                    $new_password.val(''); // Xóa input
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.message);
                    notyf.error(xhr.responseJSON.message);
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });


        $(function() {
            $('#day_of_birth').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Xóa',
                    applyLabel: 'Chọn',
                    format: 'DD-MM-YYYY'
                }
            });

            $('#day_of_birth').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            });

            $('#day_of_birth').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

        })
    </script>
@endpush


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        @media(max-width: 768px) {
            .form-section {
                margin-top: 1rem;
            }
        }

        .toggle-password {
            top: 50px;
            right: 0px;
        }

        .avatar-section {
            background: #f8f9fd;
            border-right: 1px solid #e9ecef;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin-bottom: 1.5rem;
        }

        .avatar-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            /* Hình tròn */
            border: 2px solid #e9ecef;
        }

        .avatar-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 40px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            transition: background 0.3s ease;
        }

        .avatar-overlay:hover {
            background: #0056b3;
        }

        .avatar-overlay i {
            color: white;
            font-size: 1rem;
        }

        .form-label {
            font-weight: 600;
            color: #42526e !important;
            font-size: 14px !important;
        }
    </style>
@endpush
