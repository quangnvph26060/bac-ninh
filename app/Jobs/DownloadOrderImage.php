<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use Exception;

class DownloadOrderImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;
    protected $mockupUrl;
    protected $designUrl;

    public function __construct($orderId, $mockupUrl, $designUrl)
    {
        $this->orderId = $orderId;
        $this->mockupUrl = $mockupUrl;
        $this->designUrl = $designUrl;
    }

    public function handle()
    {
        $order = Order::find($this->orderId);

        if (!$order || !$order->orderItems()->exists()) {
            return;
        }

        $folder = now()->format('Y-m-d');
        $paths = [];

        if ($this->isValidDriveUrl($this->mockupUrl)) {
            $mockupPath = $this->downloadDriveImage($this->mockupUrl, $folder);
            if ($mockupPath) {
                $paths['model_image'] = $mockupPath;
            }
        }

        if ($this->isValidDriveUrl($this->designUrl)) {
            $designPath = $this->downloadDriveImage($this->designUrl, $folder);
            if ($designPath) {
                $paths['design_image'] = $designPath;
            }
        }

        if (!empty($paths)) {
            $orderItem = $order->orderItems()->first();
            $orderItem->update($paths);
        }
    }

    protected function isValidDriveUrl($url): bool
    {
        return preg_match('/^https:\/\/drive\.google\.com\/(file\/d\/[^\/]+\/view|open\?id=[^&]+|uc\?id=[^&]+)/', $url);
    }

    protected function convertDriveUrl($url): ?string
    {
        if (
            preg_match('/file\/d\/([^\/]+)\//', $url, $matches) ||
            preg_match('/open\?id=([^&]+)/', $url, $matches) ||
            preg_match('/uc\?id=([^&]+)/', $url, $matches)
        ) {
            $fileId = $matches[1];
            return "https://drive.google.com/uc?export=download&id={$fileId}";
        }
        return null;
    }

    protected function downloadDriveImage($url, $folder): ?string
    {
        $downloadUrl = $this->convertDriveUrl($url);
        if (!$downloadUrl) return null;

        try {
            $response = Http::timeout(10)->get($downloadUrl);

            if (!$response->ok()) return null;

            $ext = $this->getImageExtension($response);
            $filename = Str::random(20) . ($ext ? ".{$ext}" : ".jpg");
            $path = "orders/{$folder}/{$filename}";

            Storage::disk('public')->put($path, $response->body());

            return "storage/{$path}";
        } catch (Exception $e) {
            return null;
        }
    }

    protected function getImageExtension($response)
    {
        $contentType = $response->header('Content-Type');
        return match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => null,
        };
    }
}
