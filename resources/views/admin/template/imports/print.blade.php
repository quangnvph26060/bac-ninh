<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phiếu nhập kho</title>
</head>

<body style="margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; font-size: 14px;">

    <div style="max-width: 800px; margin: auto; padding: 20px; border: 1px solid #ccc; line-height: 1.6;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">PHIẾU NHẬP KHO</h2>
            <p style="margin: 0;">Ngày nhập: {{ $materialImport->date->format('d/m/Y') }}</p>
        </div>

        <div style="margin-bottom: 20px;">
            <p><strong>Mã phiếu:</strong> {{ $materialImport->code }}</p>
            <p><strong>Nhà cung cấp:</strong> {{ $materialImport->supplier->company_name }}</p>
            <p><strong>Người lập phiếu:</strong> {{ $materialImport->employee->full_name }}</p>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" border="1" cellspacing="0"
            cellpadding="5">
            <thead style="background-color: #f0f0f0;">
                <tr>
                    <th style="text-align: center;">STT</th>
                    <th style="text-align: left;">Nguyên vật liệu</th>
                    <th style="text-align: center;">ĐVT</th>
                    <th style="text-align: right;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá (USD)</th>
                    <th style="text-align: right;">Thành tiền (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materialImport->details as $detail)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $detail->material->name }}</td>
                        <td style="text-align: center;">{{ $detail->material->unit }}</td>
                        <td style="text-align: right;">{{ number_format($detail->quantity, 0, ',', '') }}</td>
                        <td style="text-align: right;">{{ formatPrice($detail->unit_price) }}</td>
                        <td style="text-align: right;">{{ formatPrice($detail->total_price) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Tổng cộng:</strong></td>
                    <td style="text-align: right;"><strong>{{ formatPrice($materialImport->debt->total_amount) }} USD</strong></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 40px;">
            <div style="width: 30%; float: left; text-align: center;">
                <p><strong>Người lập phiếu</strong></p>
                <p style="height: 60px;"></p>
                <p>{{ $materialImport->employee->full_name }}</p>
            </div>
            <div style="width: 30%; float: right; text-align: center;">
                <p><strong>Người nhận hàng</strong></p>
                <p style="height: 60px;"></p>
                <p>..............</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

</body>

</html>
