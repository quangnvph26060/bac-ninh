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
        try {
            $credentials = $request->only(['email', 'password']);
            $result = $this->userService->authenticateUser($credentials);
            if ($result['user']->role_id == 1) {
                session()->put('authUser', $result['user']);
                return redirect()->route('admin.dashboard');
            } elseif ($result['user']->role_id == 2) {
                session()->put('authUser', $result['user']);
                return redirect()->route('staff.index');
            } elseif ($result['user']->role_id == 3) {
                session()->put('authUser', $result['user']);
                return redirect()->route('sa.store.index');
            }
            // dd($result);
        } catch (\Exception $e) {
            return $this->handleLoginError($request, $e);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect()->route('formlogin');
    }

    protected function handleLoginError($request, \Exception $e)
    {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}
