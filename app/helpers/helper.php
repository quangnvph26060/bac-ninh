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

if (!function_exists('uploadExcel')) {
    function uploadExcel(string $fieldName, string $directory = 'excels'): ?string
    {
        $file = request()->file($fieldName);

        if ($file && $file->isValid()) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['xlsx', 'xls'])) {
                $filename = time() . uniqid() . '.' . $extension;
                $path = $file->storeAs($directory, $filename, 'public');
                return $path;
            }
        }

        return null; // Không có file hoặc file không hợp lệ
    }
}


if (!class_exists('formatNumber')) {

    function formatNumber($number)
    {
        if (fmod($number, 1) == 0) {
            return (int)$number; // Trả về số nguyên, không thêm ,
        }

        return number_format($number, 2, '.', ''); // giữ lại phần thập phân nếu có
    }
}


function removeVietnameseTones($str)
{
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", "y", $str);
    $str = preg_replace("/(đ)/u", "d", $str);

    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/u", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u", "Y", $str);
    $str = preg_replace("/(Đ)/u", "D", $str);

    return $str;
}


function uploadZipFile($fileName, $directory = 'guideline_file')
{
    if (!request()->hasFile($fileName)) {
        return null;
    }

    $file = request()->file($fileName);
    $filename = $file->getClientOriginalName();
    // time() . '_' .
    // Lưu vào storage/app/public/guideline_file
    $path = $file->storeAs($directory, $filename, 'public');

    return $path; // trả về đường dẫn để lưu vào DB
}


if (!function_exists('hasFile')) {
    function hasFile($filename)
    {
        return request()->hasFile($filename);
    }
}

function getImageInfo($filePathOrFile, bool $fullInfo = true): array
{
    // Nhận vào instance hoặc đường dẫn string
    $filePath = is_string($filePathOrFile) ? $filePathOrFile : $filePathOrFile->getRealPath();

    $result = [];

    // Lấy kích thước ảnh (width, height)
    if ($fullInfo) {
        $info = @getimagesize($filePath);
        if (!$info) {
            throw new \Exception("Không thể đọc thông tin ảnh.");
        }

        $width = $info[0];
        $height = $info[1];

        // Giới hạn ảnh quá lớn
        if ($width > 8000 || $height > 8000) {
            throw new \Exception("Ảnh vượt quá kích thước tối đa cho phép.");
        }

        $result['width'] = $width;
        $result['height'] = $height;
    }

    // Dùng Imagick để lấy DPI & định dạng
    try {
        $image = new \Imagick();
        $image->pingImage($filePath);

        $resolution = $image->getImageResolution();
        $unit = $image->getImageUnits();

        $x_dpi = $resolution['x'] ?? 72;
        $y_dpi = $resolution['y'] ?? 72;

        // Nếu đơn vị là pixels/cm, chuyển đổi sang DPI
        if ($unit === \Imagick::RESOLUTION_PIXELSPERCENTIMETER) {
            $x_dpi = round($x_dpi * 2.54, 2);
            $y_dpi = round($y_dpi * 2.54, 2);
        }

        $result['x_dpi'] = $x_dpi;
        $result['y_dpi'] = $y_dpi;

        if ($fullInfo) {
            $result['format'] = $image->getImageFormat(); // JPEG, PNG...
            $result['unit'] = $unit === 2 ? 'dpi' : ($unit === 3 ? 'pixels/cm' : 'unknown');
        }

        return $result;
    } catch (\Exception $e) {
        throw new \Exception("Lỗi khi đọc thông tin ảnh: " . $e->getMessage());
    }
}

if (! function_exists('getDomainFromUrl')) {
    function getDomainFromUrl()
    {
        $host = parse_url(request()->url(), PHP_URL_HOST);

        // Trả về null nếu không đúng URL
        if (!$host) {
            return null;
        }

        // Bỏ 'www.' nếu có
        return preg_replace('/^www\./', '', $host);
    }
}

function generateTicketCode()
{
    return 'TICK-' . strtoupper(Str::random(8));
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
        return $isResponse ? response()->json($response, $code) : $response;
    }
}

if (!function_exists('handleResponse')) {
    function handleResponse($message, $success, $code = 200, $data = [], $isToast = true)
    {
        $type = $success ? 'success' : 'error';

        if ($isToast)
            sessionFlash($type, $message);

        return response()->json(['success' => $success, 'message' => $message, 'data' => $data], $code);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message, bool $isResponse = false, $code = 500)
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
            $discountEnd = $record->discount_end;
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

function numberToVietnameseWords($number)
{
    $hyphen      = ' ';
    $conjunction = ' ';
    $separator   = ' ';
    $negative    = 'âm ';
    $dollar_unit = ' đô la'; // Đơn vị cho phần nguyên (đô la)
    $cent_unit   = ' cent';   // Đơn vị cho phần thập phân (cent)

    $dictionary  = [
        0 => 'không',
        1 => 'một',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười lăm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'nghìn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
    ];

    // Hàm trợ giúp để chuyển đổi một số (phần nguyên) thành chữ tiếng Việt mà không có đơn vị tiền tệ.
    // Hàm này sẽ được gọi đệ quy và cho phần cent.
    $convertNumberToWords = function ($num) use (&$convertNumberToWords, $dictionary, $hyphen) {
        if (!is_numeric($num)) {
            return '';
        }
        $num = (int)$num; // Đảm bảo đây là số nguyên cho hàm trợ giúp này

        $str = '';
        switch (true) {
            case $num < 21:
                $str = $dictionary[$num];
                break;
            case $num < 100:
                $tens = ((int)($num / 10)) * 10;
                $units = $num % 10;
                $str = $dictionary[$tens];
                if ($units) {
                    if ($units == 1) {
                        $str .= $hyphen . 'mốt';
                    } elseif ($units == 5) {
                        $str .= $hyphen . 'lăm';
                    } else {
                        $str .= $hyphen . $dictionary[$units];
                    }
                }
                break;
            case $num < 1000:
                $hundreds = (int)($num / 100);
                $remainder = $num % 100;
                $str = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    if ($remainder < 10) {
                        $str .= ' lẻ ' . $convertNumberToWords($remainder);
                    } else {
                        $str .= ' ' . $convertNumberToWords($remainder);
                    }
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($num, 1000)));
                $numBaseUnits = (int)($num / $baseUnit);
                $remainder = $num % $baseUnit;
                $str = $convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $str .= $remainder < 100 ? ' lẻ ' : ' ';
                    $str .= $convertNumberToWords($remainder);
                }
                break;
        }
        return trim($str);
    };

    if (!is_numeric($number)) {
        return false;
    }

    // Kiểm tra tràn số (đặc biệt đối với các số rất lớn có thể vượt quá PHP_INT_MAX)
    if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
        return false;
    }

    $is_negative = false;
    if ($number < 0) {
        $is_negative = true;
        $number = abs($number); // Chuyển số âm thành dương để xử lý
    }

    $integer_part = null;
    $fraction_str = null;

    // Tách phần nguyên và phần thập phân
    if (strpos((string)$number, '.') !== false) {
        [$integer_part, $fraction_str] = explode('.', (string)$number);
    } else {
        $integer_part = (string)$number;
    }

    $result_words = '';

    // Chuyển đổi phần nguyên
    $integer_words = $convertNumberToWords($integer_part);
    if ($integer_words === '' && (int)$integer_part === 0) {
        $integer_words = $dictionary[0]; // Đảm bảo "không" cho phần nguyên bằng 0
    }

    $result_words .= $integer_words . $dollar_unit;

    // Xử lý phần thập phân (cents)
    if ($fraction_str !== null && is_numeric($fraction_str)) {
        // Đảm bảo phần thập phân luôn có hai chữ số cho cents (ví dụ: '5' thành '50', '05' giữ nguyên)
        $fraction_str = str_pad($fraction_str, 2, '0', STR_PAD_RIGHT);
        $cents_value = (int)substr($fraction_str, 0, 2); // Chỉ lấy hai chữ số đầu cho cents

        if ($cents_value > 0) {
            $cent_words = $convertNumberToWords($cents_value);
            $result_words .= ' ' . $cent_words . $cent_unit;
        } else if ($cents_value === 0 && strlen($fraction_str) > 0) {
            // Nếu phần thập phân là '00', không thêm "0 cent"
            // Phần đô la đã được thêm vào.
        }
    }

    // Thêm tiền tố "âm" nếu là số âm
    if ($is_negative) {
        $result_words = $negative . $result_words;
    }

    return trim($result_words);
}


// function numberToVietnameseWords($number)
// {
//     $hyphen      = ' ';
//     $conjunction = ' ';
//     $separator   = ' ';
//     $negative    = 'âm ';
//     $decimal     = ' phẩy ';
//     $dictionary  = [
//         0 => 'không',
//         1 => 'một',
//         2 => 'hai',
//         3 => 'ba',
//         4 => 'bốn',
//         5 => 'năm',
//         6 => 'sáu',
//         7 => 'bảy',
//         8 => 'tám',
//         9 => 'chín',
//         10 => 'mười',
//         11 => 'mười một',
//         12 => 'mười hai',
//         13 => 'mười ba',
//         14 => 'mười bốn',
//         15 => 'mười lăm',
//         16 => 'mười sáu',
//         17 => 'mười bảy',
//         18 => 'mười tám',
//         19 => 'mười chín',
//         20 => 'hai mươi',
//         30 => 'ba mươi',
//         40 => 'bốn mươi',
//         50 => 'năm mươi',
//         60 => 'sáu mươi',
//         70 => 'bảy mươi',
//         80 => 'tám mươi',
//         90 => 'chín mươi',
//         100 => 'trăm',
//         1000 => 'nghìn',
//         1000000 => 'triệu',
//         1000000000 => 'tỷ',
//         1000000000000 => 'nghìn tỷ',
//     ];

//     if (!is_numeric($number)) {
//         return false;
//     }

//     // Check for overflow (especially for very large numbers that might exceed PHP_INT_MAX)
//     // This check is a bit simplified; for very precise large number handling, libraries like GMP or BCMath are better.
//     if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
//         // overflow detection for positive numbers becoming negative due to int cast, or negative numbers exceeding min int
//         return false;
//     }

//     if ($number < 0) {
//         // Handle negative numbers recursively
//         return $negative . numberToVietnameseWords(abs($number));
//     }

//     $string = $fraction = null;

//     // Check if the number has a decimal part
//     if (strpos((string)$number, '.') !== false) {
//         [$number, $fraction] = explode('.', (string)$number);
//     }

//     // Convert the integer part to Vietnamese words
//     switch (true) {
//         case $number < 21:
//             $string = $dictionary[$number];
//             break;
//         case $number < 100:
//             $tens = ((int)($number / 10)) * 10;
//             $units = $number % 10;
//             $string = $dictionary[$tens];
//             if ($units) {
//                 if ($units == 1) {
//                     $string .= $hyphen . 'mốt'; // Special case for 'mốt' (one)
//                 } elseif ($units == 5) {
//                     $string .= $hyphen . 'lăm'; // Special case for 'lăm' (five)
//                 } else {
//                     $string .= $hyphen . $dictionary[$units];
//                 }
//             }
//             break;
//         case $number < 1000:
//             $hundreds = (int)($number / 100);
//             $remainder = $number % 100;
//             $string = $dictionary[$hundreds] . ' ' . $dictionary[100]; // e.g., "hai trăm"
//             if ($remainder) {
//                 if ($remainder < 10) {
//                     $string .= ' lẻ ' . numberToVietnameseWords($remainder); // e.g., "hai trăm lẻ năm"
//                 } else {
//                     $string .= ' ' . numberToVietnameseWords($remainder); // e.g., "hai trăm hai mươi lăm"
//                 }
//             }
//             break;
//         default:
//             // For numbers 1,000 and above, recursively break them down
//             $baseUnit = pow(1000, floor(log($number, 1000)));
//             $numBaseUnits = (int)($number / $baseUnit);
//             $remainder = $number % $baseUnit;
//             $string = numberToVietnameseWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
//             if ($remainder) {
//                 // Add 'lẻ' for remainders less than 100 when breaking down large numbers
//                 $string .= $remainder < 100 ? ' lẻ ' : ' ';
//                 $string .= numberToVietnameseWords($remainder);
//             }
//             break;
//     }

//     // Handle the fractional part
//     if ($fraction !== null && is_numeric($fraction)) {
//         $string .= $decimal;
//         $digits = str_split((string)$fraction);
//         foreach ($digits as $digit) {
//             $string .= $dictionary[$digit] . ' ';
//         }
//         $string = trim($string); // Remove trailing space
//     }

//     return $string;
// }

function getRealSql($query)
{
    $sql = $query->toSql();
    foreach ($query->getBindings() as $binding) {
        $value = is_numeric($binding) ? $binding : "'{$binding}'";
        $sql = preg_replace('/\?/', $value, $sql, 1);
    }
    return $sql;
}

function formatPrice($price)
{
    if (!empty($price)) {
        // Format số với 2 chữ số thập phân
        $formatted = number_format((float) $price, 2, '.', ',');

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

// if (!function_exists('generateCode')) {

//     function generateCode(int $length = 12): string
//     {
//         $base = microtime(true) . bin2hex(random_bytes(5));
//         $hash = strtoupper(substr(hash('sha256', $base), 0, $length));
//         return $hash;
//     }
// }

if (!function_exists('generateUniqueCode')) {
    function generateUniqueCode(string $table, string $column = 'code', int $length = 12): string
    {
        do {
            $code = generateCode($length);
        } while (DB::table($table)->where($column, $code)->exists());

        return $code;
    }
}

if (!function_exists('generateCode')) {

    function generateCode($table, $prefix, $length = 10)
    {
        // Lấy số lượng ký tự còn lại sau prefix
        $padLength = $length - strlen($prefix);

        do {
            // Tạo số random độ dài phù hợp
            $number = mt_rand(1, pow(10, $padLength) - 1);
            $code = $prefix . str_pad($number, $padLength, '0', STR_PAD_LEFT);
        } while (DB::table($table)->where('code', $code)->exists());

        return $code;
    }
}

if (!function_exists('removePrefix')) {
    function removePrefix(mixed $value, string $prefix): string
    {
        if ($value === null) return "";

        if (str_starts_with($value, $prefix)) {
            return substr($value, strlen($prefix));
        }

        return $value;
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
