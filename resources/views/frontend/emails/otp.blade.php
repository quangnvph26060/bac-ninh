<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Mã OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
            text-align: center;
            color: #333333;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            display: inline-block;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .warning {
            font-size: 14px;
            color: #666666;
            margin-top: 20px;
        }

        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #666666;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 600px) {
            .container {
                margin: 10px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 3px;
            }

            .content {
                padding: 20px;
            }

            .logo {
                max-width: 120px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('frontend/assets/img/art-logo.png') }}" alt="Company Logo" class="logo">
            <h1>Xác Thực Tài Khoản</h1>
        </div>
        <div class="content">
            <p>Chào bạn, {{ $user->name }}</p>
            <p>Chúng tôi đã nhận được yêu cầu xác thực tài khoản của bạn. Dưới đây là mã OTP của bạn:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>Vui lòng nhập mã này vào trang xác thực để tiếp tục. Mã OTP có hiệu lực trong vòng <strong>5
                    phút</strong>.</p>
            <p class="warning">Lưu ý: Không chia sẻ mã OTP này với bất kỳ ai.</p>
        </div>
        <div class="footer">
            <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi tại <a
                    href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p>
            <p>© 2025 Công ty của bạn. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>

</html>
