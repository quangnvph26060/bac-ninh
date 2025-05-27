<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Barcode PDF</title>
</head>

<body style="background: white; font-family: Arial, sans-serif; margin: 50px; text-align: center;">

    <!-- Barcode hiển thị ở giữa -->
    <div style="width: 100%; text-align: center; margin-bottom: 20px;">
        <div style="display: inline-block;">
            {!! $barcodeHtml !!}
        </div>
    </div>

    <!-- Mã số hiển thị dưới barcode -->
    <div style="font-family: Courier, monospace; font-size: 18px;">
        {{ chunk_split($barcode, 4, ' ') }}
    </div>

    <div style="font-family: Courier, monospace; font-size: 16px; margin-top: 10px;">
        {{ $orderName }}
    </div>

</body>

</html>
