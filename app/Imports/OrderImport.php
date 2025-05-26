<?php

namespace App\Imports;

use App\Jobs\DownloadOrderImage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class OrderImport implements ToCollection
{
    protected $userId;
    protected $jobId;

    public function __construct($userId, $jobId)
    {
        $this->userId = $userId;
        $this->jobId = $jobId;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Bỏ dòng tiêu đề

        $filledRows = collect();
        $lastFullData = null;
        $failures = [];
        $orders = [];

        foreach ($rows as $row) {
            // Bỏ qua dòng hoàn toàn trống
            if ($row->filter()->isEmpty()) {
                continue;
            }

            // Lưu dòng cuối đầy đủ
            if (!empty($row[0]) && !empty($row[1]) && !empty($row[7])) {
                $lastFullData = $row;
            }

            if ($lastFullData) {
                foreach ([0, 1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17] as $index) {
                    if (empty($row[$index])) {
                        $row[$index] = $lastFullData[$index];
                    }
                }

                // Nếu thiếu thông tin sản phẩm → không đẩy vào xử lý
                if (empty($row[2]) || empty($row[3]) || empty($row[4])) {
                    continue;
                }
            }

            $filledRows->push($row);
        }

        $total = $filledRows->count();

        $current = 0;

        foreach ($filledRows as $row) {

            $current++;

            // Lưu tiến trình
            Cache::put("import_progress_{$this->jobId}", [
                'current' => $current,
                'total' => $total,
                'percent' => round($current / $total * 100),
                'status' => 'processing'
            ], 3600);

            $orderCode = $row[0];
            $orderName = $row[1];
            $productName = $row[2];
            $sku = $row[3];
            $qty = (int) $row[4];

            if (!$this->isValidImage($row[6])) {
                $failures[] = "Ảnh thiết kế không hợp lệ tại biến thể: $sku";
                continue;
            }

            // Kiểm tra đơn hàng đã tồn tại
            if (Order::where('order_code', $orderCode)->exists()) {
                $failures[] = "Mã đơn hàng đã tồn tại: {$orderCode}";
                continue;
            }

            if (Order::where('order_name', $orderName)->exists()) {
                $failures[] = "Tên đơn hàng đã tồn tại: {$orderName}";
                continue;
            }

            // Tìm sản phẩm
            $product = Product::where('name', $productName)->first();

            if (!$product) {
                $failures[] = "Không tìm thấy sản phẩm: {$productName}";
                continue;
            }

            // Tìm biến thể
            $variant = ProductVariant::where([
                'sku' => $sku,
                'product_id' => $product->id
            ])->first();

            if (!$variant) {
                $failures[] = "Không tìm thấy biến thể với SKU: {$sku} trong sản phẩm {$productName}";
                continue;
            }

            if (!empty($row[5])) {
                $mockup = $this->downloadImage($row[5], 'mockup');
            }

            // Tải tạm ảnh design để validate
            $tempDesign = $this->downloadImage($row[6], 'temp_design');

            if ($tempDesign) {
                $error = $this->validateDesignImage($tempDesign, $variant);
                if ($error) {
                    $failures[] = $error;
                    // Xóa ảnh tạm
                    Storage::delete($tempDesign);
                    continue;
                }
                // Nếu validate thành công, di chuyển ảnh từ temp sang thư mục design
                $design = str_replace('temp_design', 'design', $tempDesign);
                Storage::move($tempDesign, $design);
            }

            if ($variant->stock_status === "out_of_stock" || $variant->stock <= 0) {
                $failures[] = "Biến thể {$sku} trong sản phẩm {$productName} đã hết hàng";
                continue;
            }

            if ($variant->stock < $qty) {
                $failures[] = "Số lượng tồn kho không đủ với biến thể {$sku} trong sản phẩm {$productName}";
                continue;
            }

            // Tính giá
            $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;
            $lineTotal = $price * $qty;
            $shipping = $this->switchDeliveryMethod($row[17], $variant);

            // Nếu đơn hàng đã có, cộng dồn
            if (!isset($orders[$orderCode])) {
                $orders[$orderCode] = [
                    'order_name' => $orderName,
                    'full_name' => "{$row[7]} {$row[8]}",
                    'email' => $row[9],
                    'phone_number' => !str_starts_with($row[10], '0') ? "0$row[10]" : $row[10],
                    'shipping_address' => "$row[14] $row[13] $row[12] $row[11]",
                    'zip_code' => $row[15],
                    'note' => $row[16],
                    'delivery_method' => $row[17],
                    'total' => 0,
                    'shipping_fee' => 0,
                    'items' => [],
                ];
            }

            // Cộng dồn vào đơn hàng
            $orders[$orderCode]['total'] += $lineTotal;
            $orders[$orderCode]['shipping_fee'] += $shipping;

            $orders[$orderCode]['items'][] = [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'sku' => $sku,
                'product_name' => $productName,
                'price' => $price,
                'original_price' => $product->sale_price,
                'quantity' => $qty,
                'image' => $product->image,
                'model_image' => $mockup ?? null,
                'design_image' => $design,
            ];
        }

        // Lưu tiến trình hoàn thành
        Cache::put("import_progress_{$this->jobId}", [
            'current' => $total,
            'total' => $total,
            'percent' => 100,
            'status' => empty($failures) ? 'success' : 'completed_with_errors',
            'failures' => $failures
        ], 3600);

        foreach ($orders as $code => $item) {
            $userId = $this->userId;
            DB::transaction(function () use ($code, $item, $userId) {

                // Tạo đơn hàng
                $order = Order::create([
                    'user_id' => $userId,
                    'full_name' => $item['full_name'],
                    'email' => $item['email'],
                    'order_code' => $code,
                    'order_name' => $item['order_name'],
                    'total' => $item['total'],
                    'phone_number' => $item['phone_number'],
                    'shipping_address' => $item['shipping_address'],
                    'note' => $item['note'],
                    'zip_code' => $item['zip_code'],
                    'shipping_fee' => $item['shipping_fee'],
                ]);

                // Tạo các item trong đơn hàng
                $order->orderItems()->createMany($item['items']);

                // Dispatch job to download images
                if (!empty($item['items'])) {
                    foreach ($item['items'] as $orderItem) {
                        if (!empty($orderItem['model_image'])) {
                            DownloadOrderImage::dispatch($orderItem['model_image'], 'mockup', $order->id);
                        }
                        if (!empty($orderItem['design_image'])) {
                            DownloadOrderImage::dispatch($orderItem['design_image'], 'design', $order->id);
                        }
                    }
                }
            });
        }

        return $failures;
    }

    private function switchDeliveryMethod($method, $variant)
    {
        return match ($method) {
            'Standard Shipping (US)' => $variant->standard_shipping,
            'Express Shipping (US)' => $variant->express_shipping,
            'International Shipping' => $variant->international_shipping,
            default => $variant->standard_shipping,
        };
    }

    protected function downloadImage($url, $folder)
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Nếu là link Google Drive dạng view, chuyển sang direct download
        if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)\/view/', $url, $matches)) {
            $fileId = $matches[1];
            $url = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        // Nếu là link Google Drive dạng sharing (gửi qua "open?id=" hoặc "uc?id="), xử lý lấy id
        else if (preg_match('/drive\.google\.com\/open\?id=([^&]+)/', $url, $matches)) {
            $fileId = $matches[1];
            $url = "https://drive.google.com/uc?export=download&id={$fileId}";
        } else if (preg_match('/drive\.google\.com\/uc\?id=([^&]+)/', $url, $matches)) {
            $fileId = $matches[1];
            $url = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        // Lấy đuôi file (mặc định jpg nếu không lấy được)
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) {
            $ext = 'jpg';
        }

        $filename = $folder . '/' . Str::random(20) . '.' . $ext;

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                Storage::put($filename, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            // Có thể log lỗi ở đây nếu muốn
        }

        return null;
    }


    private function isValidImage($image): bool
    {
        if (empty($image)) {
            return false;
        }

        if (!filter_var($image, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Kiểm tra nếu là link Google Drive
        if (
            preg_match('/drive\.google\.com\/file\/d\/[^\/]+\/view/', $image) ||
            preg_match('/drive\.google\.com\/open\?id=/', $image) ||
            preg_match('/drive\.google\.com\/uc\?id=/', $image)
        ) {
            return true;
        }

        // Kiểm tra đuôi file ảnh hợp lệ cho các link bình thường
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i', $image)) {
            return false;
        }

        // Nếu cần kiểm tra HTTP status code, có thể mở lại đoạn dưới đây:
        // $headers = @get_headers($image);
        // if (!$headers || !str_starts_with($headers[0], 'HTTP/1.1 200')) {
        //     return false;
        // }

        return true;
    }

    protected function validateDesignImage(string $designPath, ProductVariant $variant): ?string
    {
        if (!$designPath || !Storage::exists($designPath)) {
            return "Thiết kế không tồn tại trong hệ thống.";
        }

        $fullPath = Storage::path($designPath);

        try {
            $info = getImageInfo($fullPath);

            $isValid =
                $info['width'] === (int) $variant->design_width &&
                $info['height'] === (int) $variant->design_height &&
                abs($info['x_dpi'] - (float) $variant->design_ppi) <= 5 &&
                strtolower($info['format']) === strtolower($variant->design_format);

            if (!$isValid) {
                deleteImage($designPath);

                return "Design không đúng mẫu." .
                    "Yêu cầu: Width: {$variant->design_width} px, Height: {$variant->design_height} px, PPI: {$variant->design_ppi}, File format: .{$variant->design_format}\n" .
                    "Thiết kế của bạn: Width: {$info['width']} px, Height: {$info['height']} px, PPI: {$info['x_dpi']}, File format: {$info['format']}. Trong biến thể: {$variant->sku}";
            }
        } catch (\Exception $e) {
            deleteImage($designPath);
            return "Không thể đọc thông tin ảnh thiết kế: " . $e->getMessage();
        }

        return null; // hợp lệ
    }
}
