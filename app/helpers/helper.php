<?php

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('uploadImages')) {
    function uploadImages($flieName, string $directory = 'images', $resize = false, $width = 150, $height = 150, $isArray = false)
    {
        $paths = [];

        $images = request()->file($flieName);
        if (!is_array($images)) {
            $images = [$images];
        }

        $manager = new ImageManager(['driver' => 'gd']);
        $storagePath = storage_path('app/public/' . trim($directory, '/'));

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        foreach ($images as $key => $image) {

            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $img = $manager->make($image->getRealPath());

                // Resize nếu $resize = true
                if ($resize) {
                    $img->resize($width, $height);
                }

                $filename = time() . uniqid() . '.' . 'webp';

                Storage::disk('public')->put($directory . '/' . $filename, $img->encode());

                $paths[$key] = $directory . '/' . $filename;
            }
        }
        return $isArray ? $paths : $paths[0] ?? null;
    }
}

if (!function_exists('hasFile')) {
    function hasFile($filename)
    {
        return request()->hasFile($filename);
    }
}

if (!function_exists('showImage')) {
    function showImage($image)
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($image && $storage->exists($image)) {
            return $storage->url($image);
        }

        return asset('backend/assets/img/image-default.jpg');
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

if (!function_exists('pluralModelName')) {
    function pluralModelName($row)
    {
        return Str::plural(Str::lower(class_basename($row)));
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug(string $text)
    {
        return Str::slug($text);
    }
}


if (!function_exists('transaction')) {
    function transaction($callback)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            Log::error('Exception Details:', [
                'error' => $e->getMessage(),  // Tên lỗi
                'file' => $e->getFile(),      // File xảy ra lỗi
                'line' => $e->getLine(),      // Dòng xảy ra lỗi
                'function' => getErrorFunction($e), // Function xảy ra lỗi
                'trace' => $e->getTraceAsString(), // Stack trace (tùy chọn)
            ]);
            DB::rollBack();

            if (session()->has('executeError') && session('executeError')) {
                $imagePath = session('executeError');
                deleteImage($imagePath);
            }

            return errorResponse('Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    }
}

if (!function_exists('getErrorFunction')) {
    function getErrorFunction(Throwable $exception): ?string
    {
        // Kiểm tra nếu có trace và function gọi lỗi
        $trace = $exception->getTrace();
        return isset($trace[0]['function']) ? $trace[0]['function'] : null;
    }
}


if (!function_exists('successResponse')) {
    function successResponse($message, $data = null, $code = 200, bool $isResponse = false)
    {
        return  $isResponse ? response()->json(['success' => true, 'message' => $message, 'data' => $data], $code) : $message;
    }
}

if (!function_exists('handleResponse')) {
    function handleResponse($message, $code = 200, $isToastr = false)
    {
        $isToastr ? sessionFlash('success', $message) : '';
        return response()->json(['success' => true, 'message' => $message], $code);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message, bool $isResponse = false,  $code = 500)
    {

        return $isResponse ? response()->json([
            'success' => false,
            'message' => $message
        ], $code) : $message;
    }
}

if (!class_exists('sessionFlash')) {
    function sessionFlash($key, $message)
    {
        session()->flash($key, $message);
    }
}
