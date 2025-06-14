<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Jobs\DownloadOrderImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class OrderImport implements ToCollection
{
    protected $userId;
    protected $jobId;
    protected $failures = [];

    protected int $current = 0;
    protected int $total = 0;

    public function __construct($userId, $jobId)
    {
        $this->userId = $userId;
        $this->jobId = $jobId;
    }

    public function collection(Collection $rows)
    {
        $this->total = $rows->count() - 1; // Trừ dòng tiêu đề

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $line = $index + 1;
            $errors = $this->validateRow($row, $line);

            if (!empty($errors)) {
                $this->failures = array_merge($this->failures, $errors);
                continue;
            }

            if (Order::where('order_code', $row[0])->orWhere('order_name', $row[1])->exists()) {
                $this->failures[] = "Dòng $line: Mã hoặc tên đơn hàng đã tồn tại: {$row[0]} / {$row[1]}";
                continue;
            }

            $product = Product::where('name', $row[2])->first();
            $variant = ProductVariant::where('sku', $row[3])->first();

            if (!$product || !$variant || $variant->product_id !== $product->id) {
                $this->failures[] = "Dòng $line: Không tìm thấy sản phẩm hoặc biến thể khớp với SKU: {$row[3]}";
                continue;
            }

            // Nếu không có biến thể nhưng SKU trùng với SKU của sản phẩm, ta giả lập 1 "biến thể mặc định"
            if (!$variant && $product->sku === $row[3]) {
                $variant = (object)[
                    'id' => null,
                    'sku' => $product->sku,
                    'price' => $product->price ?? 0,
                ];
            }

            if (!$variant || ($variant->id && $variant->product_id !== $product->id)) {
                $this->failures[] = "Dòng $line: Không tìm thấy biến thể phù hợp với SKU: {$row[3]}";
                continue;
            }

            $quantity = (int)$row[4];
            $price = (float)$variant->price;
            $lineTotal = $price * $quantity;
            $shippingFee = (float)$row[5];

            $mockup = $this->convertGoogleDriveUrl(trim($row[6]));
            $design = $this->convertGoogleDriveUrl(trim($row[18]));

            $this->createOrder($row, $product, $variant, $quantity, $price, $lineTotal, $shippingFee, $mockup, $design);
        }
    }

    protected function updateProgress($status)
    {
        Cache::put("import_progress_{$this->jobId}", [
            'status' => $status,
            'current' => $this->current,
            'total' => $this->total,
            'percent' => $this->total > 0 ? round($this->current * 100 / $this->total) : 0,
            'failures' => $this->failures,
        ], now()->addMinutes(10));
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    protected function validateRow($row, $line): array
    {
        $errors = [];

        if (empty($row[0]) || empty($row[1]) || empty($row[7])) {
            $errors[] = "Dòng $line: Thiếu thông tin đơn hàng bắt buộc (mã đơn, tên đơn, họ)";
        }

        if (empty($row[2]) || empty($row[3]) || empty($row[4])) {
            $errors[] = "Dòng $line: Thiếu thông tin sản phẩm (tên, SKU, số lượng)";
        }

        if (!$this->isValidImage($row[6])) {
            $errors[] = "Dòng $line: Ảnh thiết kế không hợp lệ tại SKU: {$row[3]}";
        }

        return $errors;
    }

    protected function isValidImage($image): bool
    {
        if (!$image) return false;

        return preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $image)
            || preg_match('/drive\.google\.com\/.*(file\/d\/|open\?id=|uc\?id=)/', $image);
    }

    protected function convertGoogleDriveUrl($url): string
    {
        if (
            preg_match('/drive\.google\.com\/file\/d\/([^\/]+)\/view/', $url, $matches)
            || preg_match('/drive\.google\.com\/open\?id=([^&]+)/', $url, $matches)
            || preg_match('/drive\.google\.com\/uc\?id=([^&]+)/', $url, $matches)
        ) {
            return "https://drive.google.com/uc?export=download&id={$matches[1]}";
        }

        return $url;
    }

    protected function createOrder($row, $product, $variant, $qty, $price, $lineTotal, $shipping, $mockup, $design)
    {
        DB::transaction(function () use ($row, $product, $variant, $qty, $price, $lineTotal, $shipping, $mockup, $design) {
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

            if ($design) {
                DownloadOrderImage::dispatch($design, 'design', $order->id);
            }
        });
    }
}
