<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phiếu công nợ</title>
</head>

<body style="font-family: DejaVu Sans, sans-serif; font-size: 13px; margin: 40px; color: #000;">

    <div style="text-align: center;">
        <h2 style="margin: 0;">{{ $config['company'] }}</h2>
        <p style="margin: 2px 0;">Địa chỉ: {{ $config['address'] }}</p>
        <p style="margin: 2px 0;">Điện thoại: {{ $config['hotline'] }}</p>
    </div>

    <div style="text-align: center; font-size: 20px; font-weight: bold; margin: 10px 0 20px;">
        PHIẾU CÔNG NỢ NHÀ CUNG CẤP
    </div>

    <div style="margin-bottom: 20px;">
        <p style="margin: 4px 0;"><strong>Nhà cung cấp: </strong> {{ $supplierDebt->supplier->company_name }}</p>
        <p style="margin: 4px 0;"><strong>Ngày phát sinh: </strong> {{ $supplierDebt->created_at->format('d/m/Y') }}</p>
        <p style="margin: 4px 0;"><strong>Tổng tiền: </strong> {{ formatPrice($supplierDebt->total_amount) }} USD</p>
        <p style="margin: 4px 0;"><strong>Đã thanh toán: </strong> {{ formatPrice($supplierDebt->paid_amount) }} USD</p>
        <p style="margin: 4px 0;"><strong>Còn nợ:
            </strong>{{ formatPrice($supplierDebt->total_amount - $supplierDebt->paid_amount) }} USD</p>
        <p style="margin: 4px 0;"><strong>Trạng thái: </strong>
            @if ($supplierDebt->status == 'paid')
                Đã thanh toán
            @elseif ($supplierDebt->status == 'partial')
                Thanh toán một phần
            @else
                Chưa thanh toán
            @endif
        </p>
    </div>

    <div>
        <strong>Chi tiết vật tư:</strong>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>STT</th>
                    <th style="text-align: left;">Mã vật tư</th>
                    <th style="text-align: left;">Tên vật tư</th>
                    <th style="text-align: left;">Đơn giá (USD)</th>
                    <th style="text-align: left;">Số lượng</th>
                    <th style="text-align: left;">Thành tiền (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supplierDebt->import->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->material->code }}</td>
                        <td>{{ $detail->material->name }}</td>
                        <td>{{ formatPrice($detail->unit_price) }}</td>
                        <td>{{ number_format($detail->quantity, 0, '.', '') }}</td>
                        <td>{{ formatPrice($detail->total_price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <strong>Lịch sử thanh toán:</strong>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" border="1" cellpadding="6">
            <thead>
                <tr>
                    <th style="text-align: center;">Ngày</th>
                    <th style="text-align: center;">Số tiền (USD)</th>
                    <th style="text-align: center;">Ghi chú</th>
                    <th style="text-align: center;">Người tạo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supplierDebt->import->debt->payments as $payment)
                    <tr>
                        <td style="text-align: center; width: 10%">{{ $payment->date->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ formatPrice($payment->amount) }}</td>
                        <td style="text-align: center; width: 35%">{{ $payment->note }}</td>
                        <td style="text-align: center;">{{ $payment->employee->full_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 30%; text-align: center;">
                    <strong>Người lập phiếu</strong><br><br><br>
                    (Ký và ghi rõ họ tên)
                </td>
                <td style="width: 30%; text-align: center;">
                    <strong>Người duyệt</strong><br><br><br>
                    (Ký và ghi rõ họ tên)
                </td>
                <td style="width: 30%; text-align: center;">
                    <strong>Nhà cung cấp</strong><br><br><br>
                    (Ký và ghi rõ họ tên)
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
