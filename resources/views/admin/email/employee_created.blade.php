<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản thành công</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .email-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .email-content {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
        }

        .email-content p {
            margin-bottom: 20px;
        }

        .email-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #888;
        }

        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        .cta-button {
            background-color: #007bff;
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
        }

        .cta-button:hover {
            background-color: #0056b3;
        }

        .info-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 10px;
            text-align: left;
        }

        .info-table th {
            background-color: #f8f9fa;
            color: #333;
        }

        .info-table td {
            background-color: #f1f1f1;
        }

        .info-table tr:nth-child(even) td {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Chúc mừng, tài khoản của bạn đã được tạo thành công!</h2>
        </div>

        <div class="email-content">
            <p>Xin chào <strong>{{ $employee->full_name }}</strong>,</p>
            <p>Chúng tôi xin thông báo rằng tài khoản nhân viên của bạn đã được tạo thành công trên hệ thống. Vui lòng
                sử dụng thông tin đăng nhập bên dưới để truy cập hệ thống:</p>

            <table class="info-table">
                <tr>
                    <th>Mã nhân viên</th>
                    <td>{{ $employee->employee_code }}</td>
                </tr>
                <tr>
                    <th>Tài khoản đăng nhập</th>
                    <td>{{ $employee->email }}</td>
                </tr>
                <tr>
                    <th>Mật khẩu</th>
                    <td>{{ $password }}</td>
                </tr>
            </table>

            <p>Vui lòng thay đổi mật khẩu sau lần đăng nhập đầu tiên để đảm bảo an toàn cho tài khoản của bạn.</p>
{{-- 
            <p>Trân trọng,</p>
            <p>Đội ngũ hỗ trợ</p> --}}

            <a href="{{ url('/') }}" class="cta-button">Đăng nhập ngay</a>
        </div>

        <div class="email-footer">
            <p>Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ với chúng tôi qua <a
                    href="mailto:support@example.com">support@example.com</a>.</p>
            <p>© 2025 Công ty XYZ. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>

</html>
