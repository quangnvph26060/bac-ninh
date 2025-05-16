<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            color: #000;
            font-size: 14px;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container-invoice {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #invoiceContent {
            width: 190mm;
            max-width: 100%;
            padding: 20px 20px 100px  20px;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Times New Roman', serif;
            color: #000;
            font-size: 14px;
            display: flex;
            flex-direction: column;
        }

        .no-print {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .no-print button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .no-print button:hover {
            background-color: #45a049;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td {
            text-align: center;
        }

        td.text-start {
            text-align: left;
        }

        tr:last-child td {
            font-weight: bold;
        }

        .date {
            display: flex;
            justify-content: end;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container-wrap">
        <div class="no-print">
            <button onclick="window.print()">🖨 Print Invoice</button>
            <button onclick="exportPDF()">📄 Export PDF</button>
        </div>

        <div class="container-invoice">
            <div id="invoiceContent">
                <!-- Invoice content -->
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <strong>STORE NAME</strong><br>
                        Address: .................................................<br>
                        Phone: ......................................................
                    </div>
                    <div style="text-align: center;">
                        <strong>SALES INVOICE</strong><br>
                        <span>Sold Items (Or Business Category)</span>
                    </div>
                </div>

                <br>

                <div>
                    <span><strong>Customer Name:</strong>
                        ............................................................</span><br>
                    <span><strong>Address:</strong>
                        ..........................................................................</span>
                </div>

                <table border="1" cellspacing="0" cellpadding="4"
                    style="width:100%; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 40%;">ITEM NAME</th>
                            <th style="width: 15%;">QUANTITY</th>
                            <th style="width: 20%;">UNIT PRICE</th>
                            <th style="width: 20%;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sum = 0; @endphp
                        @foreach ($order->orderItems as $item)
                            @php $sum += $item->price * $item->quantity; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    <p class="">{{ $item->product_name }}</p>
                                    <small>
                                        {{ implode(' - ', $item->productVariant?->attributeValues->pluck('value')->toArray() ?? []) }}
                                    </small>
                                </td>
                                <td>
                                    ${{ formatPrice($item->price) }}
                                </td>
                                <td>
                                    <small>x</small>{{ $item->quantity }}
                                </td>
                                <td>
                                    ${{ formatPrice($item->price * $item->quantity) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            <td style="text-align: right;"><strong>SUBTOTAL</strong></td>
                            <td></td>
                            <td></td>
                            <td>${{ formatPrice($sum) }}</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td style="text-align: right;"><strong>SHIPPING</strong></td>
                            <td></td>
                            <td></td>
                            <td>${{ formatNumber($order->shipping_fee) }}</td> <!-- Example shipping fee -->
                        </tr>

                        <tr>
                            <td></td>
                            <td style="text-align: right;"><strong>DISCOUNT</strong></td>
                            <td></td>
                            <td></td>
                            <td>-${{ formatNumber($order->discount) }}</td> <!-- Example discount -->
                        </tr>

                        <tr>
                            <td></td>
                            <td style="text-align: right;"><strong>TOTAL</strong></td>
                            <td></td>
                            <td></td>
                            <td>${{ formatNumber($order->total) }}</td> <!-- Adjusted total -->
                        </tr>

                    </tbody>
                </table>

                <br>

                <div>
                    <span><strong>Amount Due:</strong>
                        ........................................................................</span>
                </div>

                <br><br>

                <div class="date"> Date ...... month ...... year 20......</div>

                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <strong>CUSTOMER</strong>
                    </div>
                    <div style="text-align: right;">
                        <strong>SALES PERSON</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        async function exportPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const element = document.getElementById("invoiceContent");

            const canvas = await html2canvas(element, {
                scale: 2,
                useCORS: true
            });

            const imgData = canvas.toDataURL("image/png");
            const pdf = new jsPDF("p", "mm", "a4");

            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

            pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
            pdf.save("sales-invoice.pdf");
        }
    </script>
</body>

</html>
