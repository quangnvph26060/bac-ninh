@extends('Themes.main.main')

@section('content')
    <style>
        @media (max-width: 768px) {
            .auth-one-bg {
                display: none;
            }
        }
    </style>


    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card overflow-hidden">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100">
                                    <div class="bg-overlay"></div>
                                    <div class="position-relative h-100 d-flex flex-column">
                                        <div class="mb-4">
                                            <a href="index.html" class="d-block">
                                                <img src="{{ asset('backend/auth/assets/images/logo-light.png') }}" alt="" height="18">
                                            </a>
                                        </div>
                                        <div class="mt-auto">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <div>
                                        <h5 class="text-primary">Xin chào!</h5>
                                        <p class="text-muted">Đăng nhập để sử dụng ứng dụng.</p>
                                    </div>

                                    <div class="mt-4">
                                        <form action="{{ route('admin.login') }}" method="post">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Email hoặc Số điện thoại</label>
                                                <input type="text" class="form-control" id="email" name="email"
                                                    placeholder="Tên đăng nhập">
                                                @error('email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password-input">Mật Khẩu</label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password" id="password" name="password"
                                                        class="form-control pe-5 password-input" placeholder="Mật khẩu">
                                                    @error('password')
                                                        <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    value="1" id="auth-remember-check">
                                                <label class="form-check-label" for="auth-remember-check">Lưu thông
                                                    tin</label>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Đăng nhập</button>
                                            </div>
                                            <div
                                                style="margin: 0px; display: flex; justify-content: flex-end; padding: 10px 5px;">
                                                <a href="{{ route('forget-password') }}" class="text-muted">Quên mật
                                                    khẩu?</a>
                                            </div>


                                        </form>
                                    </div>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0">Tạo tài khoản dùng thử miễn phí ? <a
                                                href="{{ route('register.index') }}"
                                                class="fw-semibold text-primary text-decoration-underline"> Đăng ký</a> </p>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->

            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">

                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->


    <!-- Add necessary icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/2.5.0/remixicon.css" rel="stylesheet">
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#password-addon').on('click', function() {
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');
                var eyeIcon = $('#eye-icon');

                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('ri-eye-fill').addClass('ri-eye-off-fill');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('ri-eye-off-fill').addClass('ri-eye-fill');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alertSuccess = document.getElementById('alert-success');
            if (alertSuccess) {
                setTimeout(function() {
                    alertSuccess.style.display = 'none';
                }, 3000);
            }
        });
    </script>
@endpush
