<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            // Lấy lỗi đầu tiên
            $error = $exception->errors();
            $firstError = reset($error); // Lấy lỗi đầu tiên từ mảng lỗi

            // Nếu request là Ajax, trả về lỗi đầu tiên dưới dạng JSON
            if ($request->ajax()) {
                return response()->json([
                    'message' => $firstError[0] // Trả về lỗi đầu tiên
                ], 422);
            }

            // Nếu là request thông thường, trả về trang lỗi mặc định
            return parent::render($request, $exception);
        }


        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->view('errors.403', [], 403);
            }
            return response()->view('errors.403', [], 403);
        }

        // Bắt các HttpException 403 khác
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->view('errors.403', [], 403);
            }

            return response()->view('errors.403', [], 403);
        }



        if ($request->expectsJson()) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }


        return parent::render($request, $exception);
    }

    public function report(Throwable $exception)
    {
        Log::error('>>Exception occurred<<', [
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        parent::report($exception);
    }
}
