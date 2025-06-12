@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'Tra cứu đơn hàng']]" />
        </div>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="text-light">Quét mã vạch để tìm đơn hàng</h4>
            </div>
            <div class="card-body text-center">
                <div id="reader" class="mx-auto" style="max-width: 500px;"></div>
                <div id="order-details" class="mt-3"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let isScanning = false; // ➡️ Cờ kiểm soát quét

        const onScanSuccess = (decodedText) => {
            if (isScanning) return; // ➡️ Nếu đang quét thì không chạy tiếp
            isScanning = true; // ➡️ Đánh dấu là đã quét

            console.log(`Barcode found: ${decodedText}`);

            $.ajax({
                url: "{{ route('admin.orders.get.by.barcode') }}",
                type: 'POST',
                data: {
                    barcode: decodedText,
                },
                success: function(response) {
                    window.open(response.data, '_blank');
                },
                error: function(xhr) {
                    Notifications(xhr.responseJSON.message, 'danger');
                },
                complete: function() {
                    document.getElementById('html5-qrcode-button-camera-stop').click();
                    isScanning = false; // ➡️ Reset lại cờ sau khi dừng camera
                }
            });
        };


        let config = {
            fps: 10,
            qrbox: {
                width: 350,
                height: 350
            },
            rememberLastUsedCamera: true,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", config
        );
        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endpush

@push('styles')
    <style>
        /* Stop Button - Màu đỏ cam hiện đại */
        #html5-qrcode-button-camera-stop {
            background-color: #ff6b6b !important;
            border-color: #ff6b6b !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 0.875rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #html5-qrcode-button-camera-stop:hover {
            background-color: #ff4d4d !important;
            border-color: #ff4d4d !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #html5-qrcode-button-camera-stop:active {
            background-color: #e84343 !important;
            border-color: #e84343 !important;
            transform: scale(0.98);
        }

        /* Start Button - Màu xanh dương gradient */
        #html5-qrcode-button-camera-start {
            background: linear-gradient(45deg, #42a5f5, #2196f3) !important;
            border-color: #2196f3 !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 0.875rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #html5-qrcode-button-camera-start:hover {
            background: linear-gradient(45deg, #1e88e5, #1976d2) !important;
            border-color: #1976d2 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #html5-qrcode-button-camera-start:active {
            background: #1565c0 !important;
            border-color: #1565c0 !important;
            transform: scale(0.98);
        }

        /* File Selection Button - Màu xanh lá cây pastel */
        #html5-qrcode-button-file-selection {
            background-color: #66bb6a !important;
            border-color: #66bb6a !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 0.875rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #html5-qrcode-button-file-selection:hover {
            background-color: #57a05b !important;
            border-color: #57a05b !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #html5-qrcode-button-file-selection:active {
            background-color: #4b8d4f !important;
            border-color: #4b8d4f !important;
            transform: scale(0.98);
        }

        /* Permission Button - Màu vàng cam gradient */
        #html5-qrcode-button-camera-permission {
            background: linear-gradient(45deg, #fbc02d, #f9a825) !important;
            border-color: #f9a825 !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 0.875rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #html5-qrcode-button-camera-permission:hover {
            background: linear-gradient(45deg, #f57f17, #fbc02d) !important;
            border-color: #f57f17 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #html5-qrcode-button-camera-permission:active {
            background-color: #e65100 !important;
            border-color: #e65100 !important;
            transform: scale(0.98);
        }
    </style>
@endpush
