<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadOrderImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageUrl;
    protected $folder;
    protected $orderId;

    public function __construct($imageUrl, $folder, $orderId)
    {
        $this->imageUrl = $imageUrl;
        $this->folder = $folder;
        $this->orderId = $orderId;
    }

    public function handle()
    {
        if (!$this->imageUrl || !filter_var($this->imageUrl, FILTER_VALIDATE_URL)) {
            return;
        }

        // Convert Google Drive links to direct download
        if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)\/view/', $this->imageUrl, $matches)) {
            $fileId = $matches[1];
            $this->imageUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        } else if (preg_match('/drive\.google\.com\/open\?id=([^&]+)/', $this->imageUrl, $matches)) {
            $fileId = $matches[1];
            $this->imageUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        } else if (preg_match('/drive\.google\.com\/uc\?id=([^&]+)/', $this->imageUrl, $matches)) {
            $fileId = $matches[1];
            $this->imageUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
        }

        // Get file extension
        $ext = pathinfo(parse_url($this->imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) {
            $ext = 'jpg';
        }

        $filename = $this->folder . '/' . Str::random(20) . '.' . $ext;

        try {
            $response = Http::timeout(15)->get($this->imageUrl);

            if ($response->successful()) {
                Storage::put($filename, $response->body());

                // Update order item with downloaded image path
                $order = Order::find($this->orderId);
                if ($order) {
                    $orderItem = $order->orderItems()
                        ->where($this->folder . '_image_url', $this->imageUrl)
                        ->first();

                    if ($orderItem) {
                        $orderItem->update([
                            $this->folder . '_image' => $filename
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
    }
}
