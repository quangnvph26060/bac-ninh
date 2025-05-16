<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Hủy Đơn Hàng</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
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
            background-color: #4a90e2;
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        кал {
            margin: 0 0 15px;
        }

        .content {
            padding: 30px;
            line-height: 1.6;
        }

        .content p {
            margin: 0 0 15px;
        }

        .order-details,
        .wallet-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .order-details p,
        .wallet-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #e74c3c;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background-color: #c0392b;
        }

        .footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content {
                padding: 20px;
            }

            .button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Thông Báo Hủy Đơn Hàng</h1>
        </div>
        <div class="content">
            <p>Kính gửi Quý khách,</p>
            <p>Chúng tôi xin thông báo rằng đơn hàng của Quý khách đã được hủy theo yêu cầu của quản trị viên. Dưới đây
                là thông tin chi tiết:</p>
            <div class="order-details">
                <p><strong>Mã đơn hàng:</strong> #{{ $order->order_code }}</p>
                <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('F j, Y \a\t g:i a') }}</p>
                <p><strong>Lý do hủy:</strong> {{ $order->reason }}</p>
                <p><strong>Số tiền hoàn lại:</strong> ${{ formatPrice($order->total) }}</p>
            </div>
            <div class="wallet-details">
                <p><strong>Số dư ví trước khi hoàn:</strong> ${{ formatPrice($balanceBefore) }}</p>
                <p><strong>Số dư ví sau khi hoàn:</strong> ${{ formatPrice($balanceAfter) }}</p>
            </div>
            <p>Nếu Quý khách có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email<a
                    href="mailto:hotro@congty.com">hotro@congty.com</a> hoặc số điện thoại <strong>0123 456 789</strong>.
            </p>

            <p>Số tiền này đã được hoàn lại vào ví của bạn và có thể sử dụng cho đơn hàng kế tiếp. Chúng tôi rất tiếc vì
                sự bất tiện này và hy vọng sẽ tiếp tục phục vụ Quý khách trong tương lai.</p>
            <p>Trân trọng,<br>Đội ngũ Công ty</p>
        </div>
        <div class="footer">
            <p>© 2025 Công ty. Mọi quyền được bảo lưu.</p>
            <p><a href="https://congty.com">congty.com</a> | <a href="mailto:hotro@congty.com">hotro@congty.com</a></p>
        </div>
    </div>
</body>

</html>
