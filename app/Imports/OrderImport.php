<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Jobs\DownloadOrderImage;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithChunkReading;


// WithChunkReading
class OrderImport implements ToCollection, WithHeadingRow
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

    // public function chunkSize(): int
    // {
    //     return 5;
    // }

    public function collection(Collection $rows)
    {
        $this->total = $rows->count();

        $tax = Config::query()->first()->tax_rate;

        foreach ($rows as $index => $row) {
            $line = $index + 1;

            $this->updateProgress('processing');

            $info = [];

            $errors = $this->validateRow($row, $line);

            // $mockup = trim($row['mockup_image']);

            // $design = trim($row['design_image']);

            if (!empty($errors)) {
                $this->failures = array_merge($this->failures, $errors);
                $this->updateProgress('processing');
                continue;
            }

            if (Order::where('order_code', $row['order_code'])->orWhere('order_name', $row['order_name'])->exists()) {
                $this->failures[] = "Dòng $line: Mã hoặc tên đơn hàng đã tồn tại: {$row['order_code']} / {$row['order_name']}";
                $this->updateProgress('processing');
                continue;
            }

            $product = Product::where('name', $row['product_name'])->first();
            $variant = ProductVariant::where('sku', $row['product_variant_sku'])->first();

            if (!$product || !$variant || $variant->product_id !== $product->id) {
                $this->failures[] = "Dòng $line: Không tìm thấy sản phẩm hoặc biến thể khớp với SKU: {$row['product_variant_sku']}";
                $this->updateProgress('processing');
                continue;
            }

            // $mockupImage = $this->downloadAndSaveImage($mockup, 'mockup');
            // $designImage = $this->downloadAndSaveImage($design, 'design');

            // $absolutePath = Storage::disk('public')->path($designImage);

            // $invalid = $this->checkInfoDesignImage($absolutePath, $variant, $info);

            // if (!$invalid) {
            //     $this->failures[] = "Dòng $line: Invalid image specifications. Expected: width: {$variant->design_width}px, height: {$variant->design_height}px, PPI: {$variant->design_ppi}, format: {$variant->design_format}. Your image: width: {$info['width']}px, height: {$info['height']}px, PPI: {$info['x_dpi']}, format: {$info['format']}. ";
            //     deleteImage($mockupImage);
            //     deleteImage($designImage);
            //     $this->updateProgress('processing');
            //     continue;
            // }

            $quantity = (int)$row['quantity'];
            $target = $variant ?? $product;
            $price = isOnSale($target) ? $target->discount_price : $target->sale_price;

            $deliveryMethod = $this->toSnakeCase($row['delivery_method'] ?? 'standard shipping');

            $shippingFee = $this->calculateShippingFee($deliveryMethod, $product, $variant ?? null);

            $lineTotal = $price * $quantity + $shippingFee + $tax;

            $this->createOrder($row, $product, $variant, $quantity, $price, $lineTotal, $shippingFee, $deliveryMethod, $tax);

            $this->current++;
            $this->updateProgress('processing');
        }

        $this->updateProgress('done');
    }

    private function checkInfoDesignImage($url, $variant, &$info)
    {
        if (
            empty($variant->design_width) ||
            empty($variant->design_height) ||
            empty($variant->design_ppi) ||
            empty($variant->design_format)
        ) {
            return true;
        }

        $info = getImageInfo($url, true);

        return $info['width'] === (int) $variant->design_width &&
            $info['height'] === (int) $variant->design_height &&
            abs($info['x_dpi'] - (float) $variant->design_ppi) <= 5 &&
            strtolower($info['format']) === strtolower($variant->design_format);
    }

    private function calculateShippingFee($deliveryMethod, $product, $variant = null)
    {
        // Ưu tiên biến thể nếu có
        $source = $variant ?? $product;

        $method = strtolower($deliveryMethod); // ví dụ: 'standard_shipping'

        return (float)($source->{$method} ?? 0);
    }

    private function toSnakeCase($string)
    {
        return strtolower(preg_replace('/\s+/', '_', trim($string)));
    }

    protected function validateRow($row, $line): array
    {
        $messages = [];

        $requiredFields = [
            'order_code' => 'mã đơn hàng',
            'order_name' => 'tên đơn hàng',
            'product_name' => 'tên sản phẩm',
            'product_variant_sku' => 'SKU',
            'quantity' => 'số lượng',
            'first_name' => 'họ người nhận',
            'last_name' => 'tên người nhận',
            'nation' => 'quốc gia',
            'state' => 'tỉnh/Bang',
            'city' => 'thành phố',
            'street_address' => 'địa chỉ',
            'delivery_method' => 'phương thức giao hàng',
            // 'design_image' => 'Ảnh thiết kế',
        ];

        foreach ($requiredFields as $field => $label) {
            if (!isset($row[$field]) || trim($row[$field]) === '') {
                $messages[] = "Thiếu $label";
            }
        }

        // if (!empty($row['design_image'])) {
        //     $originalUrl = trim($row['design_image']);

        //     if (!$this->isValidDriveUrl($originalUrl)) {
        //         $messages[] = "Ảnh thiết kế không đúng định dạng Google Drive";
        //     } else {
        //         $converted = $this->convertGoogleDriveUrl($originalUrl);

        //         $imageUrl = $converted ?: $originalUrl;

        //         if (!$this->urlExists($imageUrl)) {
        //             $messages[] = "Ảnh thiết kế không truy cập được (có thể bị 404 hoặc cấm quyền truy cập)";
        //         } else {
        //             $row->put('design_image', $imageUrl);
        //         }
        //     }
        // }

        // if (!empty($row['mockup_image'])) {
        //     $originalUrl = trim($row['mockup_image']);

        //     if (!$this->isValidDriveUrl($originalUrl)) {
        //         $messages[] = "Ảnh mockup không đúng định dạng Google Drive";
        //     } else {
        //         $converted = $this->convertGoogleDriveUrl($originalUrl);

        //         $imageUrl = $converted ?: $originalUrl;

        //         if (!$this->urlExists($imageUrl)) {
        //             $messages[] = "Ảnh mockup không truy cập được (có thể bị 404 hoặc cấm quyền truy cập)";
        //         } else {
        //             $row->put('mockup_image', $imageUrl);
        //         }
        //     }
        // }

        if (!empty($messages)) {
            return ["Dòng $line: " . implode(', ', $messages)];
        }

        return [];
    }

    protected function isValidDriveUrl($url): bool
    {
        return preg_match('/^https:\/\/drive\.google\.com\/(file\/d\/[^\/]+\/view|open\?id=[^&]+|uc\?id=[^&]+)/', $url);
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

    protected function convertGoogleDriveUrl($url): ?string
    {
        if (
            preg_match('/drive\.google\.com\/file\/d\/([^\/]+)\/view/', $url, $matches)
            || preg_match('/drive\.google\.com\/open\?id=([^&]+)/', $url, $matches)
            || preg_match('/drive\.google\.com\/uc\?id=([^&]+)/', $url, $matches)
        ) {
            return "https://drive.google.com/uc?export=download&id={$matches[1]}";
        }

        return null;
    }

    protected function urlExists(string $url): bool
    {
        try {
            $response = Http::timeout(5)->head($url);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function downloadAndSaveImage(string $url, string $folder = 'design')
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

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
            logger($e->getMessage());
            return null;
        }
    }

    protected function createOrder($row, $product, $variant, $qty, $price, $lineTotal, $shipping, $deliveryMethod, $tax)
    {
        DB::transaction(function () use ($row, $product, $variant, $qty, $price, $lineTotal, $shipping, $deliveryMethod, $tax) {
            $order = Order::create([
                'user_id' => $this->userId,
                'order_code' => $row['order_code'],
                'order_name' => $row['order_name'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'phone_number' => $this->formatPhone($row['phone'] ?? null),
                'shipping_address' => $row['street_address'],
                'zip_code' => $row['zip_code'],
                'note' => $row['note'],
                'shipping_fee' => $shipping,
                'total' => $lineTotal,
                'shipping_method' => $deliveryMethod,
                'nation' => $row['nation'],
                'state' => $row['state'],
                'city' => $row['city'],
                'tax' => $tax,
                'tracking' => $row['tracking_number'],
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
                // 'model_image' => $mockup,
                // 'design_image' => $design,
            ]);
        });
    }

    private function formatPhone($phone)
    {
        if (!$phone) return '';

        $phone = trim($phone);
        return str_starts_with($phone, '0') ? $phone : "0{$phone}";
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}
