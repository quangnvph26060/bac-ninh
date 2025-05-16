<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function login()
    {
        return view('admin.auth.login');
    }
    public function forgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function forgotPasswordPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|exists:employees,email',
            'new_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ], __('request.messages'), [
            'email' => 'Email',
            'new_password' => 'Mật khẩu mới'
        ]);

        $existingRequest = PasswordChangeRequest::where('email', $credentials['email'])
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return errorResponse('Bạn đã gửi yêu cầu thay đổi mật khẩu và đang chờ duyệt. Vui lòng không gửi lại.', true, 422);
        }

        PasswordChangeRequest::create($credentials);

        return successResponse('Gửi yêu cầu thay đổi mật khẩu thành công.', null, 200, true);
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
