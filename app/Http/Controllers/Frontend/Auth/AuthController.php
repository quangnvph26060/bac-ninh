<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendOtpEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return view('frontend.pages.auth.login');
    }

    public function authenticate(Request $request)
    {
        $payloads = $request->validate(
            [
                'email' => 'required|email|exists:users',
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
            ],
            __('request.messages'),
            [
                'email' => 'Email',
                'password' => 'Mật khẩu'
            ]
        );

        $remember = $request->has('remember');

        if (Auth::attempt($payloads, $remember)) {
            $request->session()->regenerate();
            /**
             * @var User $account
             */
            $account = auth('web')->user();

            if ($account->isAdmin()) {
                return errorResponse('Tài khoản admin không thể truy cập!', true, 400);
            }

            return handleResponse('Đăng nhập thành công!', true, 200, [
                'redirect' => redirect()->intended()->getTargetUrl()
            ]);
        };

        return errorResponse('Mật khẩu không chính xác!', true, 400);
    }

    public function register()
    {
        return view('frontend.pages.auth.register');
    }

    public function storeRegister(Request $request)
    {
        $payloads = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'phone' => 'required|string|max:20|unique:users,phone',
                'g-recaptcha-response' => 'required',
            ],
            __('request.messages'),
            [
                'name' => 'Họ tên',
                'email' => 'Email',
                'password' => 'Mật khẩu',
                'phone' => 'Số điện thoại',
                'g-recaptcha-response' => 'reCAPTCHA',
            ]
        );

        $payloads['password'] = Hash::make($payloads['password']);
        $payloads['role_id'] = 2; // ROLE USER

        $user = User::create($payloads);

        Auth::login($user);

        return handleResponse('Đăng ký tài khoản thành công!', true, 200, [
            'redirect' => redirect()->intended()->getTargetUrl()
        ]);
    }

    public function forgotPassword()
    {
        return view('frontend.pages.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $payloads = $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'g-recaptcha-response' => 'required',
            ],
            __('request.messages'),
            [
                'email' => 'Email',
                'g-recaptcha-response' => 'reCAPTCHA',
            ]
        );

        $user = User::where('email', $payloads['email'])->first();

        // Kiểm tra nếu đã gửi trong vòng 5 phút
        // if ($user->last_otp_sent_at && $user->last_otp_sent_at->diffInMinutes(now()) < 5) {
        //     $remaining = 5 - $user->last_otp_sent_at->diffInMinutes(now());
        //     return errorResponse("Bạn chỉ có thể yêu cầu lại sau {$remaining} phút.", true, 429);
        // }

        $otp = rand(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
            'last_otp_sent_at' => now(),
        ]);

        SendOtpEmail::dispatch($user, $otp);

        return successResponse('Đã gửi email khôi phục mật khẩu!', [], 200, true);
    }

    public function resendOtp(Request $request)
    {
        $payloads = $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ],
            __('request.messages'),
            [
                'email' => 'Email',
            ]
        );

        $user = User::where('email', $payloads['email'])->first();

        $otp = rand(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(1),
            'last_otp_sent_at' => now(),
        ]);

        SendOtpEmail::dispatch($user, $otp);

        return successResponse('Đã gửi email xác thực OTP!', [], 200, true);
    }

    public function resetPassword(Request $request)
    {
        $payloads = $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'otpCode' => 'required|digits:6',
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'passwordConfirmation' => 'required|same:password',
            ],
            __('request.messages'),
            [
                'email' => 'Email',
                'otpCode' => 'Mã OTP',
                'password' => 'Mật khẩu mới',
                'passwordConfirmation' => 'Mật khẩu mới',
            ]
        );

        $user = User::where(
            'email',
            $payloads['email']
        )->first();

        if (!$user) {
            return errorResponse('Người dùng không tồn tại!', true, 404);
        }

        if ($payloads['otpCode'] != $user->otp_code) {
            return errorResponse('Mã OTP không chính xác!', true, 400);
        }

        if ($user->otp_expires_at < now()) {
            return errorResponse('Mã OTP đã hết hạn!', true, 400);
        }

        $user->update([
            'password' => Hash::make($payloads['password']),
            'otp_code' => null,
            'otp_expires_at' => null,
            'last_otp_sent_at' => null,
        ]);

        Auth::login($user);

        return successResponse('Đặt lại mật khẩu thành công!', [], 200, true);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(), // thêm dòng này
                'password' => Hash::make(uniqid()),
                'role_id' => 2, // ROLE USER
            ]);
        } else {
            // Nếu người dùng đã tồn tại, có thể cập nhật google_id nếu cần
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        Auth::login($user);

        return redirect()->intended('/');
    }


    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
