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

        $failures = [];
        $jobKey = "import_progress_{$this->jobId}";
        $total = $rows->count();
        $current = 0;

        foreach ($rows as $index => $row) {
            $current++;

            // Bỏ qua dòng trống hoàn toàn
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $line = $index + 1;

            // Bắt buộc có các cột: mã đơn, tên đơn, họ
            if (empty($row[0]) || empty($row[1]) || empty($row[7])) {
                $failures[] = 'Dòng ' . $line . ': Thiếu thông tin đơn hàng bắt buộc (mã đơn, tên đơn, họ)';
                continue;
            }

            // Kiểm tra thông tin sản phẩm
            if (empty($row[2]) || empty($row[3]) || empty($row[4])) {
                $failures[] = 'Dòng ' . $line . ': Thiếu thông tin sản phẩm (tên, SKU, số lượng)';
                continue;
            }

            // Validate ảnh thiết kế
            if (!$this->isValidImage($row[6])) {
                $failures[] = 'Dòng ' . $line . ': Ảnh thiết kế không hợp lệ tại SKU: ' . $row[3];
                continue;
            }

            // Check đơn hàng tồn tại
            if (Order::where('order_code', $row[0])->exists()) {
                $failures[] = 'Dòng ' . $line . ': Mã đơn hàng đã tồn tại: ' . $row[0];
                continue;
            }

            if (Order::where('order_name', $row[1])->exists()) {
                $failures[] = 'Dòng ' . $line . ': Tên đơn hàng đã tồn tại: ' . $row[1];
                continue;
            }

            // Tìm sản phẩm
            $product = Product::where('name', $row[2])->first();
            if (!$product) {
                $failures[] = 'Dòng ' . $line . ': Không tìm thấy sản phẩm: ' . $row[2];
                continue;
            }

            // Tìm biến thể
            $variant = ProductVariant::where([
                'sku' => $row[3],
                'product_id' => $product->id
            ])->first();

            if (!$variant) {
                $failures[] = 'Dòng ' . $line . ': Không tìm thấy biến thể SKU: ' . $row[3];
                continue;
            }

            // Kiểm tra tồn kho
            $qty = (int) $row[4];
            if ($variant->stock_status === "out_of_stock" || $variant->stock < $qty) {
                $failures[] = 'Dòng ' . $line . ': Biến thể SKU: ' . $row[3] . ' hết hàng hoặc không đủ số lượng';
                continue;
            }

            // Validate ảnh thiết kế
            $tempDesign = $this->downloadImage($row[6], 'temp_design');
            if (!$tempDesign) {
                $failures[] = 'Dòng ' . $line . ': Không thể tải ảnh thiết kế';
                continue;
            }

            $error = $this->validateDesignImage($tempDesign, $variant);
            if ($error) {
                $failures[] = 'Dòng ' . $line . ': ' . $error;
                Storage::delete($tempDesign);
                continue;
            }


            $design = str_replace('temp_design', 'design', $tempDesign);
            Storage::move($tempDesign, $design);

            $mockup = !empty($row[5]) ? $this->downloadImage($row[5], 'mockup') : null;

            // Tính giá
            $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;
            $lineTotal = $price * $qty;
            $shipping = $this->switchDeliveryMethod($row[17], $variant);

            // Tạo đơn hàng
            DB::transaction(function () use ($row, $price, $lineTotal, $shipping, $product, $variant, $mockup, $design, $qty) {
                $order = Order::create([
                    'user_id' => $this->userId,
                    'order_code' => $row[0],
                    'order_name' => $row[1],
                    'first_name' => $row[7],
                    'last_name' => $row[8],
                    'email' => $row[9],
                    'phone_number' => !str_starts_with($row[10], '0') ? "0$row[10]" : $row[10],
                    'shipping_address' => "$row[14] $row[13] $row[12] $row[11]",
                    'zip_code' => $row[15],
                    'note' => $row[16],
                    'shipping_fee' => $shipping,
                    'total' => $lineTotal,
                    'delivery_method' => $row[17],
                ]);

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'product_name' => $product->name,
                    'price' => $price,
                    'original_price' => $product->sale_price,
                    'quantity' => $qty,
                    'image' => $product->image,
                    'model_image' => $mockup,
                    'design_image' => $design,
                ]);

                if ($mockup) {
                    DownloadOrderImage::dispatch($mockup, 'mockup', $order->id);
                }
                DownloadOrderImage::dispatch($design, 'design', $order->id);
            });

            // Cập nhật tiến trình
            Cache::put($jobKey, [
                'current' => $current,
                'total' => $total,
                'percent' => round($current / $total * 100),
                'status' => 'processing'
            ], 3600);
        }

        // Kết thúc
        Cache::put($jobKey, [
            'current' => $total,
            'total' => $total,
            'percent' => 100,
            'status' => empty($failures) ? 'success' : 'completed_with_errors',
            'failures' => $failures
        ], 3600);

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
