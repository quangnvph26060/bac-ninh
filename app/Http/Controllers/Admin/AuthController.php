<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login()
    {
        return view('admin.auth.login');
    }
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:employees,email',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            Auth::guard('admin')->user();

            toastr()->success('Đăng nhập thành công.');
            return redirect()->route('admin.dashboard');
        }

        toastr()->error('Tài khoản hoặc mật khẩu không chính xác!');
        return back()->withInput(['email']);
    }


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        return redirect()->route('admin.login');
    }
}
