<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

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

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
