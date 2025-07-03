<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Phiếu Thu chi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .signature-table td {
            padding: 20px;
            text-align: center;
            vertical-align: bottom;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @foreach ($transactions as $transaction)
        <div>

            <div class="text-center mt-4">
                <h2 class="text-center mt-4">
                    {{ $transaction->type === 'income' ? 'PHIẾU THU' : 'PHIẾU CHI' }}
                </h2>

                <p>Ngày {{ \Carbon\Carbon::parse($transaction->date)->format('d') }} tháng
                    {{ \Carbon\Carbon::parse($transaction->date)->format('m') }} năm
                    {{ \Carbon\Carbon::parse($transaction->date)->format('Y') }}</p>
            </div>

            <div class="mt-2">
                <p>
                    Lý do {{ $transaction->type === 'income' ? 'thu' : 'chi' }}:
                    {{ $transaction->description ?? '..........................................' }}
                </p>
                <p>Số tiền: {{ formatPrice($transaction->amount) }} USD</p>
                <p>Viết bằng chữ: {{ ucfirst(numberToVietnameseWords($transaction->amount)) }}</p>
            </div>

            <div class="mt-4 text-right">
                <p>Ngày...... tháng...... năm......</p>
            </div>

            <table width="100%" class="signature-table">
                <tr>
                    <td>Thủ trưởng đơn vị<br>(Ký, họ tên, đóng dấu)</td>
                    <td>Kế toán trưởng<br>(Ký, họ tên)</td>
                    <td>Thủ quỹ<br>(Ký, họ tên)</td>
                    <td>Người lập phiếu<br>(Ký, họ tên)</td>
                </tr>
            </table>

            <p>Đã nhận đủ số tiền (Viết bằng chữ): ..........................................</p>
        </div>

        <div class="page-break"></div>
    @endforeach
</body>

</html>
