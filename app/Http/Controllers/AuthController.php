<?php

namespace App\Http\Controllers;

use App\Events\CustomerLogin;
use App\Models\User;
use App\Models\OTP;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(Request $request)
    {

        $credentials = $request->validate(
            [
                'email' => 'required|email|exists:users',
                'password' => 'required'
            ]
        );

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user();

            toastr()->success('Đăng nhập thành công.');
            return match ($user->role_id) {
                1 =>  redirect()->route('admin.dashboard'),
                2 =>  redirect()->route('staff.index'),
                3 =>  redirect()->route('sa.store.index'),
            };
        }

        toastr()->error('Tài khoản hoặc mật khẩu không chính xác!');

        return redirect()->back()->withInput(['email']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect()->route('formlogin');
    }
}
