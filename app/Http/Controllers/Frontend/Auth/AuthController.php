<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
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
            $account = Auth::user();

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

        return redirect()->route('home');
    }


    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
