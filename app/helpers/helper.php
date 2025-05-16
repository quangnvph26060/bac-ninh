<?php

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

if (!function_exists('uploadImages')) {
    // function uploadImages($flieName, string $directory = 'images', $resize = false, $width = 150, $height = 150, $isArray = false)
    // {
    //     $paths = [];

    //     $images = request()->file($flieName);
    //     if (!is_array($images)) {
    //         $images = [$images];
    //     }

    //     $manager = new ImageManager(['driver' => 'gd']);
    //     $storagePath = storage_path('app/public/' . trim($directory, '/'));

    //     if (!file_exists($storagePath)) {
    //         mkdir($storagePath, 0777, true);
    //     }

    //     foreach ($images as $key => $image) {

    //         if ($image instanceof \Illuminate\Http\UploadedFile) {
    //             $img = $manager->make($image->getRealPath());

    //             // Resize nếu $resize = true
    //             if ($resize) {
    //                 $img->resize($width, $height);
    //             }

    //             $filename = time() . uniqid() . '.' . 'webp';

    //             Storage::disk('public')->put($directory . '/' . $filename, $img->encode());

    //             $paths[$key] = $directory . '/' . $filename;
    //         }
    //     }
    //     return $isArray ? $paths : $paths[0] ?? null;
    // }

    function uploadImages($flieName, string $directory = 'images', $resize = false, $width = 150, $height = 150, $isArray = false, $quality = 80)
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

                // Resize nếu $resize = true, giữ tỷ lệ
                if ($resize) {
                    logger("Before resize: " . $img->width() . "x" . $img->height());
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize(); // Không phóng to ảnh nhỏ
                    });
                }

                $filename = time() . uniqid() . '.webp';

                // Encode với chất lượng 80 (bạn có thể chỉnh từ 60 đến 90)
                Storage::disk('public')->put($directory . '/' . $filename, $img->encode('webp', $quality));

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

        return asset('images/image-default.png');
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
    function transaction($callback, $onError = null)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($onError && is_callable($onError)) {
                $onError($e);
            }

            Log::error('Exception Details:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'function' => getErrorFunction($e),
                'trace' => $e->getTraceAsString(),
            ]);

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
        $response = ['success' => true, 'message' => $message, 'data' => $data, 'code' => $code];
        return  $isResponse ? response()->json($response, $code) : $response;
    }
}

if (!function_exists('handleResponse')) {
    function handleResponse($message, $success, $code = 200, $data = [], $isToast  = true)
    {
        $type = $success ? 'success' : 'error';

        if ($isToast) sessionFlash($type, $message);

        return response()->json(['success' => $success, 'message' => $message, 'data' => $data], $code);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message, bool $isResponse = false,  $code = 500)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
        return $isResponse ? response()->json($response, $code) : $response;
    }
}

if (!class_exists('sessionFlash')) {
    function sessionFlash($key, $message)
    {
        session()->flash($key, $message);
    }
}

if (!class_exists('formatNumber')) {
    function formatNumber($number)
    {
        if (!empty($number)) {
            return  number_format((float)$number, 2, '.', ',');
        }
        return 0.00;
    }
}


function isOnSale($record)
{
    // Kiểm tra xem có discount_price không
    if ($record->discount_price > 0) {
        // Nếu không có discount_start và discount_end, có nghĩa là giảm giá vô thời gian
        if (empty($record->discount_start) && empty($record->discount_end)) {
            return true; // Giảm giá vô thời gian
        }

        // Nếu có discount_start và discount_end, kiểm tra theo thời gian
        if ($record->discount_start && $record->discount_end) {
            // Chuyển đổi discount_start và discount_end thành định dạng Carbon (d-m-Y)
            $discountStart = $record->discount_start;
            $discountEnd =  $record->discount_end;
            $now = Carbon::now(); // Thời gian hiện tại

            // Kiểm tra điều kiện giảm giá hợp lệ
            if ($discountStart->lte($now) && $discountEnd->gte($now)) {
                return true;
            }
        }

        if ($record->discount_start && empty($record->discount_end)) {
            $discountStart = $record->discount_start;
            $now = Carbon::now(); // Thời gian hiện tại
            if ($discountStart->gt($now)) {
                return true; // Giảm giá bắt đầu trong tương lai
            }
        }

        // Nếu chỉ có discount_end và không có discount_start, kiểm tra discount_end < thời gian hiện tại
        if (empty($record->discount_start) && $record->discount_end) {
            $discountEnd = $record->discount_end;
            $now = Carbon::now(); // Thời gian hiện tại
            if ($discountEnd->lt($now)) {
                return true; // Giảm giá đã kết thúc
            }
        }
    }

    // Trả về false nếu không thỏa mãn điều kiện giảm giá
    return false;
}

function finalPrice($discountPrice)
{
    return formatPrice($discountPrice);
}

function formatPrice($price)
{
    if (!empty($price)) {
        // Format số với 2 chữ số thập phân
        $formatted = number_format((float)$price, 2, '.', ',');

        // Nếu phần thập phân là .00 thì bỏ đi
        if (substr($formatted, -3) === '.00') {
            return substr($formatted, 0, -3);
        }

        // Nếu phần thập phân kết thúc bằng 0 thì bỏ số 0 đó đi
        if (substr($formatted, -1) === '0') {
            return substr($formatted, 0, -1);
        }

        return $formatted;
    }
    return '0';
}

function generateEmployeeCode($table = 'employees', $column = 'employee_code', $prefix = 'PH', $length = 5)
{
    // Lấy bản ghi có ID lớn nhất
    $latestRecord = DB::table($table)->orderByDesc('id')->first();

    if ($latestRecord && isset($latestRecord->$column)) {
        // Tách phần số từ mã (VD: PH03668 -> 3668)
        $number = (int) substr($latestRecord->$column, strlen($prefix));
        $number++;
    } else {
        $number = 1;
    }

    // Tạo mã mới: PH03669
    return $prefix . str_pad($number, $length, '0', STR_PAD_LEFT);
}


if (!function_exists('isActiveMenu')) {
    function isActiveMenu($menuItem)
    {
        $currentRoute = request()->route()->getName(); // Lấy route hiện tại

        // Kiểm tra nếu menuItem có key 'inRoutes' và route hiện tại có trong danh sách
        if (isset($menuItem['inRoutes']) && in_array($currentRoute, $menuItem['inRoutes'])) {
            return 'show';
        }

        return '';
    }
}

function generateTransactionCode($length = 13)
{
    // Kết hợp chữ hoa và số
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $transactionCode = '';

    // Tạo mã giao dịch ngẫu nhiên
    for ($i = 0; $i < $length; $i++) {
        $transactionCode .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $transactionCode;
}


if (!function_exists('generateOrderCode')) {
    /**
     * Tạo mã đơn hàng.
     *
     * @param string $prefix Tiền tố, mặc định 'ORD'
     * @return string
     */
    function generateOrderCode($prefix = 'ORD')
    {
        $datePart = date('ymd'); // yymmdd => 6 số
        $randomPart = strtoupper(Str::random(6)); // 6 ký tự random chữ+ số

        return $prefix . $datePart . $randomPart;
    }
}
