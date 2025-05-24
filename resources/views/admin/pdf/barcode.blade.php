<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            text-align: center;
            background: white;
            font-family: Arial, sans-serif;
            margin: 50px;
        }

        .barcode {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>
    <div class="barcode">
        {!! $barcodeHtml !!}
    </div>
    <div class="number">{{ chunk_split($barcode, 4, ' ') }}</div>
</body>

</html>
