<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: {{ $status === 'approved' ? '#1E3A8A' : '#B91C1C' }};
            color: white;
            text-align: center;
            padding: 20px;
        }

        .email-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 20px 30px;
            color: #333333;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #2563eb;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #1e40af;
        }

        .email-footer {
            text-align: center;
            padding: 15px;
            background-color: #e9eff6;
            color: #888888;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>
                {{ $status === 'approved' ? 'Yêu Cầu Đổi Mật Khẩu Thành Công' : 'Yêu Cầu Đổi Mật Khẩu Bị Từ Chối' }}
            </h2>
        </div>
        <div class="email-body">
            <p>Xin chào <strong>{{ $employee->full_name }}</strong>,</p>

            @if ($status === 'approved')
                <p>Yêu cầu đổi mật khẩu của bạn đã được Admin phê duyệt thành công. Bạn có thể sử dụng mật khẩu mới để
                    đăng nhập vào hệ thống ngay bây giờ.</p>
                <p style="text-align: center;">
                    <a href="{{ route('admin.login', ['token' => base64_encode($employee->email)]) }}" class="btn">Đăng
                        Nhập Ngay</a>
                </p>
            @else
                <p>Yêu cầu đổi mật khẩu của bạn đã bị từ chối.
                    @if ($reason)
                        Lý do: <strong>{{ $reason }}</strong>.
                    @endif
                </p>
                <p>Nếu bạn cho rằng đây là sai sót, vui lòng liên hệ bộ phận IT để kiểm tra lại thông tin.</p>
            @endif

            <p>Trân trọng,<br>Phòng Quản Trị Hệ Thống</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} Công ty TNHH ABC. Mọi quyền được bảo lưu.
        </div>
    </div>
</body>

</html>
